<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use function PHPUnit\Framework\isEmpty;

class ComponentsController extends Controller
{
    public function getFeaturesComponent(Request $request) {

        if($request->isMethod('post') && $request->post('component')) {

            $lang = config('voyager.multilingual.default');
            $idComponent = $request->post('component');
            $component = collect();

//            $component = DB::select("
//                select
//                  components.full_name,
//                  components.short_name,
//                  feature_types.name as feature_type_name,
//                  values.value as 'value'
//                from `values`
//                left  join `components` as components on values.product_id = components.id
//                left  join `feature_types` as feature_types on values.feature_type_id = feature_types.id
//                where values.product_id=?",
//                [$idComponent]
//            );

            $components = Component::where('components.id', $idComponent)
                ->leftJoin('values', 'values.component_id', 'components.id')
                ->leftJoin('features', 'values.feature_id', 'features.id')
                ->leftJoin('feature_types', 'values.feature_type_id', 'feature_types.id')
//                ->leftJoin('sections', 'components.section_id', 'sections.id')
                ->get(
                    [
                        "components.id as id",
                        "short_name",
                        "full_name",
                        "picture",
                        "price",
                        "qty",
                        "sku",
                        "value",
//                        "sections.name as section_name",
                        "features.name as feature_name",
                        "feature_types.name as feature_type_name",
                        "feature_types.custom_name as feature_types_custom_name",
                        "filter_type",
                    ]
                );


            return view('voyager.modals.get-features-component', compact(
                'components',
                'lang'
            ));
        }
    }

    public function getComponent(Request $request) {
        if($request->isMethod('post') && $request->post('id')) {
            $id = $request->get('id');

            $components = Component::where('components.id', $id)
                ->leftJoin('values', 'values.component_id', 'components.id')
                ->leftJoin('features', 'values.feature_id', 'features.id')
                ->leftJoin('feature_types', 'values.feature_type_id', 'feature_types.id')
                ->leftJoin('sections', 'components.section_id', 'sections.id')
                ->get(
                    [
                    "components.id as id",
                    "short_name",
                    "full_name",
                    "picture",
                    "price",
                    "qty",
                    "sku",
                    "value",
                    "sections.name as section_name",
                    "features.name as feature_name",
                    "feature_types.custom_name as feature_types_custom_name",
                    "feature_types.name as feature_type_name",
                    "filter_type",
                    ]
                );

            if(!$components->isEmpty()) {
                $section = $components->first()->section_name;

                return view('parts.modal-component', compact(
                    [
                        'components',
                        'section',
                    ]
                ));
            }
            else {
                return null;
            }
        }
    }
}
