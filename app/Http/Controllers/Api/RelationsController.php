<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 07.01.2021
 * Time: 13:28
 */

namespace App\Http\Controllers\Api;

use App\Models\Component;
use App\Models\Feature;
use App\Models\FeatureType;
use App\Models\Relation;
use App\Models\RelationType;
use App\Models\Section;
use App\Models\Value;
use App\Traits\RelationTrait;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class RelationsController
{
    use RelationTrait;

    public function getFeatureTypes(Request $request) {
        $featureTypes = [];

        if($request->isMethod('post') && $request->post('section-first')) {
            $featureTypes = FeatureType::where('section_id', $request->post('section-first'))
//                ->where('filter_type', 'list')
//                ->where('filter', 1)
            ->get();
        }

        if($request->isMethod('post') && $request->post('section-second')) {
            $featureTypes = FeatureType::where('section_id', $request->post('section-second'))
//                ->where('filter_type', 'list')
//                ->where('filter', 1)
            ->get();
        }

        return view('voyager.parts.selects-with-feature-types', compact(
            'featureTypes'
        ));

    }

    public function getFeatures(Request $request) {
        $features = [];
        $features['values'] = collect([]);

        if($request->isMethod('post') && $request->post('feature-type-first')) {
            if($request->post('filter') == 'list') {
                $features['values'] = Feature::where('feature_type_id', $request->post('feature-type-first'))
                    ->get();
                $response['filter'] = $features['filter'] = 'list';

            }
            elseif($request->post('filter') == 'string') {
                $features['values'] = Value::where('feature_type_id', $request->post('feature-type-first'))
                    ->orderBy('value')
                    ->get()
                    ->groupBy('value');
                $response['filter'] = $features['filter'] = 'string';

            }
            elseif($request->post('filter') == 'number') {
                $features['values'] = Value::where('feature_type_id', $request->post('feature-type-first'))
                    ->orderBy('value')
                    ->get()
                    ->groupBy('value');
                $response['filter'] = $features['filter'] = 'number';

            }
            elseif($request->post('filter') == 'boolean') {
                $features['values'] = collect([0,1]);
                $response['filter'] = $features['filter'] = 'boolean';

            }

            $isFirst = $request->post('is-first');
        }

        if($request->isMethod('post') && $request->post('feature-type-second')) {
            if($request->post('filter') == 'list') {
                $features['values'] = Feature::where('feature_type_id', $request->post('feature-type-second'))
                    ->get();
                $response['filter'] = $features['filter'] = 'list';

            }
            elseif($request->post('filter') == 'string') {
                $features['values'] = Value::where('feature_type_id', $request->post('feature-type-second'))
                    ->orderBy('value')
                    ->get()
                    ->groupBy('value');
                $response['filter'] = $features['filter'] = 'string';

            }
            elseif($request->post('filter') == 'number') {
                $features['values'] = Value::where('feature_type_id', $request->post('feature-type-second'))
                    ->orderBy('value')
                    ->get()
                    ->groupBy('value');
                $response['filter'] = $features['filter'] = 'number';

            }
            elseif($request->post('filter') == 'boolean') {
                $features['values'] = collect([0,1]);
                $response['filter'] = $features['filter'] = 'boolean';

            }

            $isFirst = $request->post('is-first');
        }

        if(!$features['values']->isEmpty()) {
            if(is_a($features['values']->first(), Value::class)) {
                $sorted = $features['values']->sortBy('value');
                $features['values'] = $sorted->values()->all();
            }
        }

//        var_dump($features['values']->toArray());
//        return;
//        if($request->isMethod('post') && $request->get('feature-type-first')) {
//            $features = Feature::where('feature_type_id', $request->post('feature-type-first'))
//                    ->get();
//
//            $isFirst = $request->post('is-first');
//        }
//
//        if($request->isMethod('post') && $request->post('feature-type-second')) {
//            $features = Feature::where('feature_type_id', $request->post('feature-type-second'))
//                    ->get();
//
//            $isFirst = $request->post('is-first');
//        }

        $relationType = $request->post('relation-type');

        if($relationType == 'one-to-many' && $isFirst) {
            $response['view'] = view('voyager.parts.select-with-features', compact(
                'features',
                'isFirst'
            ))->render();
        }
        else {
            $response['view'] = view('voyager.parts.checkbox-with-features', compact(
                'features',
                'isFirst'
            ))->render();
        }

        return $response;
    }

    public function saveRelation(Request $request) {
        $data = $request->all();
        $values = [];

        $relationType = RelationType::where('code', $request->post('relation-type'))->first();

        foreach ($data as $k => $d) {
            if(strstr($k, '_token')) continue;

            if(!strstr($k, 'f-') && !strstr($k, 'v-')) {
                if($k == 'relation-type') {
                    $values[0][str_replace('-', "_", $k)] = $relationType->id;
                    continue;
                }

                if($k == 'feature-first-select') {
                    $d = json_decode($d, true);
                    $values[0]['features_first'][] = $d['value'];
                    $values[0]['values_first'] = null;
                    continue;
                }

                $values[0][str_replace('-', "_", $k)] = (int) $d;
                continue;
            }

            $j = json_decode($d, true);

            if(strstr($k, 'f-')) {
                if($j['is_first']) {
                    $values[0]['features_first'][] = $j['value'];
                    $values[0]['values_first'] = null;
                }
                else {
                    $values[0]['features_second'][] = $j['value'];
                    $values[0]['values_second'] = null;
                }
            }
            elseif(strstr($k, 'v-')) {
                if($j['is_first']) {
                    $values[0]['values_first'][] = $j['value'];
                    $values[0]['features_first'] = null;
                }
                else {
                    $values[0]['values_second'][] = $j['value'];
                    $values[0]['features_second'] = null;
                }
            }
        }


        if(!isset($values[0]['feature_type_first'])) {
            $values[0]['feature_type_first'] = null;
        }

        if(!isset($values[0]['feature_type_second'])) {
            $values[0]['feature_type_second'] = null;
        }

        if(!isset($values[0]['features_first'])) {
            $values[0]['features_first'] = null;
        }

        if(!isset($values[0]['features_second'])) {
            $values[0]['features_second'] = null;
        }

        if(!isset($values[0]['values_first'])) {
            $values[0]['values_first'] = null;
        }

        if(!isset($values[0]['values_second'])) {
            $values[0]['values_second'] = null;
        }

        if(!is_null($values[0]['features_first'])) {
            $values[0]['features_first'] = implode(',', $values[0]['features_first']);
        }

        if(!is_null($values[0]['features_second'])) {
            $values[0]['features_second'] = implode(',', $values[0]['features_second']);
        }

        if(!is_null($values[0]['values_first'])) {
            $values[0]['values_first'] = implode(',', $values[0]['values_first']);
        }

        if(!is_null($values[0]['values_second'])) {
            $values[0]['values_second'] = implode(',', $values[0]['values_second']);
        }

        try{
            if(!empty($values)) {
                if($relationType->code == 'one-to-many') {
                    if(!is_null($values[0]['features_first'])) {
                       Relation::updateOrCreateByFirstFeatures($values);
                    }
                    else {
                        Relation::updateOrCreateByFirstValues($values);
                    }
                }
                else {
                    $id = Relation::create(
                        [
                            'section_first' => $values[0]['section_first'],
                            'section_second' => $values[0]['section_second'],
                            'feature_type_first' => $values[0]['feature_type_first'],
                            'feature_type_second' => $values[0]['feature_type_second'],
                            'features_first' => $values[0]['features_first'],
                            'features_second' => $values[0]['features_second'],
                            'values_first' => $values[0]['values_first'],
                            'values_second' => $values[0]['values_second'],
                            'relation_type_id' => $values[0]['relation_type']
                        ]
                    );
                }

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
                    )->toJson();

                return [
                    'status' => 'success',
                    'message' => 'Связь успешно сохранена',
                    'relations' => $relations,
//                    'theseRelations' => $theseRelations
                ];
            }
        }
        catch (QueryException $exception) {
            return [
                'status' => 'error',
                'message' => 'Ошибка. Связь не была сохранена'
            ];
        }
    }

    public function editRelation(Request $request) {
        $data = $request->all();
        $values = [];

        $relationType = RelationType::where('code', $request->post('relation-type'))->first();
        $relation = Relation::where('id', $request->post('relation-id'))->first();

        foreach ($data as $k => $d) {
            if(strstr($k, '_token')) continue;

            if(!strstr($k, 'f-') && !strstr($k, 'v-')) {
                if($k == 'relation-type') {
                    $values[0][str_replace('-', "_", $k)] = $relationType->id;
                    continue;
                }

                if($k == 'feature-first-select') {
                    $d = json_decode($d, true);
                    $values[0]['features_first'][] = $d['value'];
                    $values[0]['values_first'] = null;
                    continue;
                }

                $values[0][str_replace('-', "_", $k)] = (int) $d;
                continue;
            }

            $j = json_decode($d, true);

            if(strstr($k, 'f-')) {
                if($j['is_first']) {
                    $values[0]['features_first'][] = $j['value'];
                    $values[0]['values_first'] = null;
                }
                else {
                    $values[0]['features_second'][] = $j['value'];
                    $values[0]['values_second'] = null;
                }
            }
            elseif(strstr($k, 'v-')) {
                if($j['is_first']) {
                    $values[0]['values_first'][] = $j['value'];
                    $values[0]['features_first'] = null;
                }
                else {
                    $values[0]['values_second'][] = $j['value'];
                    $values[0]['features_second'] = null;
                }
            }
        }

        if(!isset($values[0]['relation_id'])) {
            $values[0]['relation_id'] = null;
        }

        if(!isset($values[0]['features_first'])) {
            $values[0]['features_first'] = null;
        }

        if(!isset($values[0]['features_second'])) {
            $values[0]['features_second'] = null;
        }

        if(!isset($values[0]['values_first'])) {
            $values[0]['values_first'] = null;
        }

        if(!isset($values[0]['values_second'])) {
            $values[0]['values_second'] = null;
        }

        if(!is_null($values[0]['features_first'])) {
            $values[0]['features_first'] = implode(',', $values[0]['features_first']);
        }

        if(!is_null($values[0]['features_second'])) {
            $values[0]['features_second'] = implode(',', $values[0]['features_second']);
        }

        if(!is_null($values[0]['values_first'])) {
            $values[0]['values_first'] = implode(',', $values[0]['values_first']);
        }

        if(!is_null($values[0]['values_second'])) {
            $values[0]['values_second'] = implode(',', $values[0]['values_second']);
        }

        try{
            if(!empty($values)) {
                Relation::updateOrCreate(
                        [
                            'section_first' => $values[0]['section_first'],
                            'section_second' => $values[0]['section_second'],
                            'feature_type_first' => $values[0]['feature_type_first'],
                            'feature_type_second' => $values[0]['feature_type_second'],
                            'id' => $values[0]['relation_id']
                        ],
                        [
                            'features_first' => $values[0]['features_first'],
                            'features_second' => $values[0]['features_second'],
                            'values_first' => $values[0]['values_first'],
                            'values_second' => $values[0]['values_second'],
                            'relation_type_id' => $values[0]['relation_type']
                        ]
                    );
            }

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
                )->toJson();

            return [
                'status' => 'success',
                'message' => 'Связь успешно отредактирована',
                'relations' => $relations,
            ];
        }
        catch (QueryException $exception) {
            return [
            'status' => 'error',
            'message' => 'Ошибка. Связь не была сохранена'
            ];
        }
//        return redirect('/admin/relations/');
    }

    public function removeRelations(Request $request) {
        if($request->method('post') && !empty($request->all())) {
            Relation::destroy($request->all());
            return redirect('/admin/relations/');
        }
    }
}