<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 07.01.2021
 * Time: 13:16
 */

namespace App\Http\Controllers;

use App\Models\Feature;
use App\Models\FeatureType;
use App\Models\Relation;
use App\Models\RelationType;
use App\Models\Section;
use App\Models\Value;
use App\Traits\RelationTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\FeatureSet;
use TCG\Voyager\Database\Schema\SchemaManager;
use TCG\Voyager\Facades\Voyager;

class VoyagerRelationsController extends \TCG\Voyager\Http\Controllers\VoyagerBaseController
{
    use RelationTrait;

    public function index(Request $request)
    {
        // GET THE SLUG, ex. 'posts', 'pages', etc.
        $slug = $this->getSlug($request);

        // GET THE DataType based on the slug
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('browse', app($dataType->model_name));

        $getter = $dataType->server_side ? 'paginate' : 'get';

        $search = (object) ['value' => $request->get('s'), 'key' => $request->get('key'), 'filter' => $request->get('filter')];

        $searchNames = [];
        if ($dataType->server_side) {
            $searchable = SchemaManager::describeTable(app($dataType->model_name)->getTable())->pluck('name')->toArray();
            $dataRow = Voyager::model('DataRow')->whereDataTypeId($dataType->id)->get();
            foreach ($searchable as $key => $value) {
                $field = $dataRow->where('field', $value)->first();
                $displayName = ucwords(str_replace('_', ' ', $value));
                if ($field !== null) {
                    $displayName = $field->getTranslatedAttribute('display_name');
                }
                $searchNames[$value] = $displayName;
            }
        }

        $orderBy = $request->get('order_by', $dataType->order_column);
        $sortOrder = $request->get('sort_order', $dataType->order_direction);
        $usesSoftDeletes = false;
        $showSoftDeleted = false;

        // Next Get or Paginate the actual content from the MODEL that corresponds to the slug DataType
        if (strlen($dataType->model_name) != 0) {
            $model = app($dataType->model_name);

            if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope'.ucfirst($dataType->scope))) {
                $query = $model->{$dataType->scope}();
            } else {

                //Изменена логика для возможности фильтрации по разделам
                $idSection = 0;

                if($request->isMethod("get") && $request->input("filter-by-section")) {

                    if($request->input("filter-by-section") != "all") {
                        $idSection = $request->input("filter-by-section");
                        $query = Relation::select('*')->where('section_first', $idSection);
                    }
                    else {
                        $query = $model::select('*');
                    }
                }
                else {
                    $query = $model::select('*');
                }
            }

            // Use withTrashed() if model uses SoftDeletes and if toggle is selected
            if ($model && in_array(SoftDeletes::class, class_uses_recursive($model)) && Auth::user()->can('delete', app($dataType->model_name))) {
                $usesSoftDeletes = true;

                if ($request->get('showSoftDeleted')) {
                    $showSoftDeleted = true;
                    $query = $query->withTrashed();
                }
            }

            // If a column has a relationship associated with it, we do not want to show that field
            $this->removeRelationshipField($dataType, 'browse');

            if ($search->value != '' && $search->key && $search->filter) {
                $search_filter = ($search->filter == 'equals') ? '=' : 'LIKE';
                $search_value = ($search->filter == 'equals') ? $search->value : '%'.$search->value.'%';
                $query->where($search->key, $search_filter, $search_value);
            }

            $query->selectRaw(
                'GROUP_CONCAT(DISTINCT (relations.features_first)) AS features_first,
                GROUP_CONCAT(DISTINCT (relations.features_second)) AS features_second'
            );
            $query->groupBy(['section_first', 'section_second', 'feature_type_first', 'feature_type_second']);

            if ($orderBy && in_array($orderBy, $dataType->fields())) {
                $querySortOrder = (!empty($sortOrder)) ? $sortOrder : 'desc';
                $dataTypeContent = call_user_func([
                    $query->orderBy($orderBy, $querySortOrder),
                    $getter,
                ]);

            } elseif ($model->timestamps) {
                $dataTypeContent = call_user_func([$query->latest($model::CREATED_AT), $getter]);
            } else {
                $dataTypeContent = call_user_func([$query->orderBy($model->getKeyName(), 'DESC'), $getter]);
            }

            // Replace relationships' keys for labels and create READ links if a slug is provided.
            $dataTypeContent = $this->resolveRelations($dataTypeContent, $dataType);
        } else {
            // If Model doesn't exist, get data from table name
            $dataTypeContent = call_user_func([DB::table($dataType->name), $getter]);
            $model = false;
        }

        // Check if BREAD is Translatable
        $isModelTranslatable = is_bread_translatable($model);

        // Eagerload Relations
        $this->eagerLoadRelations($dataTypeContent, $dataType, 'browse', $isModelTranslatable);

        // Check if server side pagination is enabled
        $isServerSide = isset($dataType->server_side) && $dataType->server_side;

        // Check if a default search key is set
        $defaultSearchKey = $dataType->default_search_key ?? null;

        // Actions
        $actions = [];
        if (!empty($dataTypeContent->first())) {
            foreach (Voyager::actions() as $action) {
                $action = new $action($dataType, $dataTypeContent->first());

                if ($action->shouldActionDisplayOnDataType()) {
                    $actions[] = $action;
                }
            }
        }

        // Define showCheckboxColumn
        $showCheckboxColumn = false;
        if (Auth::user()->can('delete', app($dataType->model_name))) {
            $showCheckboxColumn = true;
        } else {
            foreach ($actions as $action) {
                if (method_exists($action, 'massAction')) {
                    $showCheckboxColumn = true;
                }
            }
        }

        // Define orderColumn
        $orderColumn = [];
        if ($orderBy) {
            $index = $dataType->browseRows->where('field', $orderBy)->keys()->first() + ($showCheckboxColumn ? 1 : 0);
            $orderColumn = [[$index, $sortOrder ?? 'desc']];
        }

        $view = 'voyager::bread.browse';

        if (view()->exists("voyager::$slug.browse")) {
            $view = "voyager::$slug.browse";
        }

        $sections = Arr::pluck(Section::all(), null, 'id');
        $featureTypes = Arr::pluck(FeatureType::all(), null, 'id');
        $features = Arr::pluck(Feature::all(), null, 'id');
        $values = Arr::pluck(Value::all(), null, 'value');
        $relations = Relation::where('relations.id', '!=', null)
            ->leftJoin('relation_types', 'relation_types.id', 'relations.relation_type_id')
            ->get()
            ->pluck('name', 'id');

        return Voyager::view($view, compact(
            'actions',
            'dataType',
            'dataTypeContent',
            'isModelTranslatable',
            'search',
            'orderBy',
            'orderColumn',
            'sortOrder',
            'searchNames',
            'isServerSide',
            'defaultSearchKey',
            'usesSoftDeletes',
            'showSoftDeleted',
            'showCheckboxColumn',
            'sections',
            'featureTypes',
            'features',
            'values',
            'relations',
            'idSection'

        ));
    }

    public function show(Request $request, $id)
    {
        $slug = $this->getSlug($request);
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();
        $model = app($dataType->model_name);

        if ($model && in_array(SoftDeletes::class, class_uses_recursive($model))) {
            $model = $model->withTrashed();
        }
        if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope'.ucfirst($dataType->scope))) {
            $model = $model->{$dataType->scope}();
        }

        $relation = DB::table('relations')->selectRaw('*')->where('id', $id)->first();

        $relations = DB::table('relations')
            ->leftJoin('relation_types', 'relation_types.id', 'relations.relation_type_id')
            ->where('section_first', $relation->section_first)
            ->where('section_second', $relation->section_second)
            ->where('feature_type_first', $relation->feature_type_first)
            ->where('feature_type_second', $relation->feature_type_second)
            ->get(
                [
                    'relations.id as id',
                    'section_first',
                    'section_second',
                    'feature_type_first',
                    'feature_type_second',
                    'features_first',
                    'features_second',
                    'values_first',
                    'values_second',
                    'relation_type_id',
                    'relation_types.name as name',
                ]
            );

        $sections = Section::all()->pluck(null, 'id');
        $featureTypes = FeatureType::all()->pluck(null, 'id');
        $values = Value::all()->pluck(null, 'value');
        $features = Feature::all()->pluck(null, 'id');

        $relations->map(function ($item) use (&$sections, &$featureTypes, &$values, &$features) {
            if(!is_null($item->section_first)) {
                $arr = explode(',', $item->section_first);
                foreach ($arr as $i) {
                    if($sections->has($i)){
                        $item->section_first = ($sections->get($i))->name;
                    }
                }
            }
            if(!is_null($item->section_second)) {
                $arr = explode(',', $item->section_second);
                foreach ($arr as $i) {
                    if($sections->has($i)){
                        $item->section_second = ($sections->get($i))->name;
                    }
                }
            }
            if(!is_null($item->feature_type_first)) {
                $arr = explode(',', $item->feature_type_first);
                foreach ($arr as $i) {
                    if($featureTypes->has($i)){
                        $item->feature_type_first = ($featureTypes->get($i))->name;
                    }
                }
            }
            if(!is_null($item->feature_type_second)) {
                $arr = explode(',', $item->feature_type_second);
                foreach ($arr as $i) {
                    if($featureTypes->has($i)){
                        $item->feature_type_second = ($featureTypes->get($i))->name;
                    }
                }
            }
            if(!is_null($item->features_first)) {
                $arr = explode(',', $item->features_first);
                $item->features_first = [];
                foreach ($arr as $i) {
                    if($features->has($i)){
                        $item->features_first[] = ($features->get($i))->name;
                    }
                }
            }
            if(!is_null($item->features_second)) {
                $arr = explode(',', $item->features_second);
                $item->features_second = [];
                foreach ($arr as $i) {
                    if($features->has($i)){
                        $item->features_second[] = ($features->get($i))->name;
                    }
                }
            }
            if(!is_null($item->name)) {
                $item->relation_type_id = $item->name;
            }
        });


        return view('vendor.voyager.relations.read', compact('relations', 'dataType'));
    }

    public function create(Request $request)
    {
        $slug = $this->getSlug($request);

        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Check permission
        $this->authorize('add', app($dataType->model_name));

        $dataTypeContent = (strlen($dataType->model_name) != 0)
            ? new $dataType->model_name()
            : false;

        foreach ($dataType->addRows as $key => $row) {
            $dataType->addRows[$key]['col_width'] = $row->details->width ?? 100;
        }

        // If a column has a relationship associated with it, we do not want to show that field
        $this->removeRelationshipField($dataType, 'add');

        // Check if BREAD is Translatable
        $isModelTranslatable = is_bread_translatable($dataTypeContent);

        // Eagerload Relations
        $this->eagerLoadRelations($dataTypeContent, $dataType, 'add', $isModelTranslatable);

        $view = 'voyager::bread.edit-add';

        if (view()->exists("voyager::$slug.edit-add")) {
            $view = "voyager::$slug.edit-add";
        }

        $sections = Arr::pluck(Section::all(), null, 'id');
        $relationTypes = RelationType::all();
        $relations = Relation::where('relations.id', '!=', null)
            ->leftJoin('relation_types', 'relation_types.id', 'relations.relation_type_id')
            ->get()
            ->toJson();

        return Voyager::view($view, compact(
            'dataType',
            'dataTypeContent',
            'isModelTranslatable',
            'sections',
            'relationTypes',
            'relations'
            )
        );
    }

    public function edit(Request $request, $id)
    {
        $slug = $this->getSlug($request);
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        if (strlen($dataType->model_name) != 0) {
            $model = app($dataType->model_name);

            // Use withTrashed() if model uses SoftDeletes and if toggle is selected
            if ($model && in_array(SoftDeletes::class, class_uses_recursive($model))) {
                $model = $model->withTrashed();
            }
            if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope'.ucfirst($dataType->scope))) {
                $model = $model->{$dataType->scope}();
            }
            $dataTypeContent = call_user_func([$model, 'findOrFail'], $id);
        } else {
            // If Model doest exist, get data from table name
            $dataTypeContent = DB::table($dataType->name)->where('id', $id)->first();
        }

        foreach ($dataType->editRows as $key => $row) {
            $dataType->editRows[$key]['col_width'] = isset($row->details->width) ? $row->details->width : 100;
        }

        // If a column has a relationship associated with it, we do not want to show that field
        $this->removeRelationshipField($dataType, 'edit');

        // Check permission
        $this->authorize('edit', $dataTypeContent);

        // Check if BREAD is Translatable
        $isModelTranslatable = is_bread_translatable($dataTypeContent);

        // Eagerload Relations
        $this->eagerLoadRelations($dataTypeContent, $dataType, 'edit', $isModelTranslatable);

        $view = 'voyager::bread.edit-add';

        if (view()->exists("voyager::$slug.edit-add")) {
            $view = "voyager::$slug.edit-add";
        }

        $valuesFirst =
        $valuesSecond = null;

        $sections = Arr::pluck(Section::all(), null, 'id');
        $featureTypes = Arr::pluck(FeatureType::all(), null, 'id');
        $features = Arr::pluck(Feature::all(), null, 'id');
        $values = Arr::pluck(Value::all(), null, 'value');

        $featureTypesFirst = FeatureType::where('section_id', $dataTypeContent->section_first)->get();
        $featureTypesSecond = FeatureType::where('section_id', $dataTypeContent->section_second)->get();

        $featuresFirstAll = Feature::where('feature_type_id', $dataTypeContent->feature_type_first)
            ->get()
            ->pluck(null, 'id');
//
        $featuresSecondAll = Feature::where('feature_type_id', $dataTypeContent->feature_type_second)
            ->get()
            ->pluck(null, 'id');

        $valuesFirstAll = Value::where('feature_type_id', $dataTypeContent->feature_type_first)
            ->where('value', '!=', null)
            ->orderBy('value')
            ->get()
            ->groupBy('value');

        $valuesSecondAll = Value::where('feature_type_id', $dataTypeContent->feature_type_second)
            ->where('value', '!=', null)
            ->orderBy('value')
            ->get()
            ->groupBy('value');

        $relations = Relation::where('relations.id', '!=', null)
            ->leftJoin('relation_types', 'relation_types.id', 'relations.relation_type_id')
            ->get(
                [
                    'relations.id as id',
                    'relations.section_first',
                    'relations.section_second',
                    'relations.feature_type_first',
                    'relations.feature_type_second',
                    'relations.features_first',
                    'relations.features_second',
                    'relations.values_first',
                    'relations.values_second',
                    'relation_types.code',
                    'relation_types.name',
                    'relation_types.description',
                ]
            );

        $relationTypes = RelationType::all()->pluck(null, 'id');
        $relationType = $relationTypes[$dataTypeContent->relation_type_id]->code;

        $theseRelations = Relation::where('section_first', $dataTypeContent->section_first)
            ->where('section_second', $dataTypeContent->section_second)
            ->where('feature_type_first', $dataTypeContent->feature_type_first)
            ->where('feature_type_second', $dataTypeContent->feature_type_second)
            ->get();


        foreach ($theseRelations as $r) {
            $str = $r->id;

            if(!is_null($r['section_first'])) {
                if(array_key_exists($r['section_first'], $sections)) {
                    $str .= ' - ' . $sections[$r['section_first']]->name;
                }
            }

            if(!is_null($r['feature_type_first'])) {
                if(array_key_exists($r['feature_type_first'], $featureTypes)) {
                    $str .= ' > ' . $featureTypes[$r['feature_type_first']]->name;
                }
            }

            if(!is_null($r['features_first'])) {
                $ar = explode(',', $r['features_first']);

                $isFirst = true;
                foreach ($ar as $item) {
                    $substr = $isFirst ? ': ' : ', ';

                    if(array_key_exists($item, $features)) {
                        $str .= $substr . $features[$item]->name;
                    }

                    $isFirst = false;
                }
            }

            if(!is_null($r['values_first'])) {
                $ar = explode(',', $r['values_first']);

                $isFirst = true;
                foreach ($ar as $item) {
                    $substr = $isFirst ? ': ' : ', ';

                    if(array_key_exists($item, $values)) {
                        $str .=  $substr . $values[$item]->value;
                    }

                    $isFirst = false;
                }
            }

            if(!is_null($r['section_second'])) {
                if(array_key_exists($r['section_second'], $sections)) {
                    $str .= ' | ' . $sections[$r['section_second']]->name;
                }
            }


            if(!is_null($r['feature_type_second'])) {
                if(array_key_exists($r['feature_type_second'], $featureTypes)) {
                    $str .= ' > ' . $featureTypes[$r['feature_type_second']]->name;
                }
            }

            if(!is_null($r['features_second'])) {
                $ar = explode(',', $r['features_second']);

                $isFirst = true;
                foreach ($ar as $item) {
                    $substr = $isFirst ? ': ' : ', ';

                    if(array_key_exists($item, $features)) {
                        $str .= $substr . $features[$item]->name;
                    }

                    $isFirst = false;
                }
            }

            if(!is_null($r['values_second'])) {
                $ar = explode(',', $r['values_second']);

                $isFirst = true;
                foreach ($ar as $item) {
                    $substr = $isFirst ? ': ' : ', ';

                    if(array_key_exists($item, $features)) {
                        $str .= $substr . $values[$item]->value;
                    }

                    $isFirst = false;
                }
            }

            $r['name_option'] = $str;
        }
        return Voyager::view($view, compact(
            'dataType',
            'dataTypeContent',
            'isModelTranslatable',
            'sections',
            'featureTypes',
            'featureTypesFirst',
            'features',
            'featureTypesSecond',
            'featuresFirstAll',
            'featuresSecondAll',
            'values',
            'valuesFirstAll',
            'valuesSecondAll',
            'relationType',
            'relationTypes',
            'relations',
            'theseRelations',
            'id'
            )
        );
    }

    public function destroy(Request $request, $id)
    {
        $slug = $this->getSlug($request);
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        $relation = Relation::find($id);
        $theseRelations = $this->getRelationsWithNames($relation);

        // Init array of IDs
//        $ids = [];

//        dd($dataType);
//        if (empty($id)) {
//            // Bulk delete, get IDs from POST
//            $ids = explode(',', $request->ids);
//        } else {
//            // Single item delete, get ID from URL
//            $ids[] = $id;
//        }
//        foreach ($ids as $id) {
//            $data = call_user_func([$dataType->model_name, 'findOrFail'], $id);
//
//            // Check permission
//            $this->authorize('delete', $data);
//
//            $model = app($dataType->model_name);
//            if (!($model && in_array(SoftDeletes::class, class_uses_recursive($model)))) {
//                $this->cleanup($dataType, $data);
//            }
//        }
//
//        $displayName = count($ids) > 1 ? $dataType->getTranslatedAttribute('display_name_plural') : $dataType->getTranslatedAttribute('display_name_singular');
//
//        $res = $data->destroy($ids);
//        $data = $res
//            ? [
//                'message'    => __('voyager::generic.successfully_deleted')." {$displayName}",
//                'alert-type' => 'success',
//            ]
//            : [
//                'message'    => __('voyager::generic.error_deleting')." {$displayName}",
//                'alert-type' => 'error',
//            ];
//
//        if ($res) {
//            event(new BreadDataDeleted($dataType, $data));
//        }

        return Voyager::view('vendor.voyager.relations.destroy', compact(
                    'theseRelations',
                    'id'
            )
        );
    }

}