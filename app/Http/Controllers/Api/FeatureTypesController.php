<?php

namespace App\Http\Controllers\Api;

use App\Models\Feature;
use App\Models\FeatureType;
use App\Models\Section;
use App\Models\Value;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use TCG\Voyager\Facades\Voyager;

class FeatureTypesController extends Controller
{
    public function getValueFeatureType(Request $request) {

        if($request->isMethod('post') && $request->post('feature-type')) {

            $lang = config('voyager.multilingual.default');

            $sections =
            $features = collect();
            $featureType =
            $section = '';

            $idFeatureType = $request->post('feature-type');
            $featureType = FeatureType::find($idFeatureType);

            if($featureType->filter_type != 'list') {
                $features = Value::where('feature_type_id', $idFeatureType)
                    ->orderBy('value')
                    ->get()
                    ->groupBy('value');

                if(!$features->isEmpty()) {
                    $section = $features->first()[0]->featureType->section->name;

                    $features = $features->sortBy(function ($product, $key) {
                        if(is_numeric($key)) {
                            return (int) $key;
                        }
                        else {
                            return $key;
                        }
                    });


                    $vals = $features->keys();

                    $values = Value::whereRaw('BINARY value in ("' . implode('","', $vals->toArray()) . '")')
                        ->where('feature_type_id', $idFeatureType)
                        ->rightJoin('components', 'components.id', 'values.component_id')
                        ->select('component_id', 'value', DB::raw('count(component_id) as total'))
                        ->groupBy('value')
                        ->get()
                        ->pluck(null, 'value');

                    $features->map(function ($feature, $key) use (&$values) {
                        $has = $values->has($key);

                        if($has) {
                            $feature['total'] = $values[$key]->total;
                        }
                        else {
                            $feature['total'] = 0;
                        }

                    });
                }

                $name = $featureType['custom_name'] ? $featureType['custom_name'] : $featureType['name'];

                $sections->put($name, $features);
                $sections->put('is_feature', false);
            }
            else {
                $features = Section::find($featureType['section_id'])->features->where('feature_type_id', $idFeatureType)->pluck(null, 'id');

                if(!$features->isEmpty()) {

                    $ids = $features->keys();

                    $values = Value::whereIn('feature_id', $ids)
                        ->where('feature_type_id', $idFeatureType)
                        ->join('components', 'components.id', 'values.component_id')
                        ->select('component_id', 'feature_id', DB::raw('count(component_id) as total'))
                        ->groupBy('feature_id')
                        ->get()
                        ->pluck(null, 'feature_id');

                    $features->map(function ($feature) use (&$values) {
                        $has = $values->has($feature->id);

                        if($has) {
                            $feature['total'] = $values[$feature->id]->total;
                        }
                        else {
                            $feature['total'] = 0;
                        }
                    });

                    $section = $features->first()->pivot->pivotParent->name;

                    $name = $featureType['custom_name'] ? $featureType['custom_name'] : $featureType['name'];

                    $sections->put($name, $features);
                    $sections->put('is_feature', true);
                }
            }

            return view('voyager.modals.get-value-feature-type', compact(
                'sections',
                'section',
                'lang'
            ));
        }
    }
}
