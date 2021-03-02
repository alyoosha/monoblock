<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\Component;
use App\Models\Configuration;
use App\Models\Feature;
use App\Models\FeatureType;
use App\Models\Relation;
use App\Models\Section;
use App\Models\Value;
use App\Traits\CatalogTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CatalogController extends Controller
{
    use CatalogTrait;

    protected $hasFilter = true;

    public function index(Request $request, $slug) {
        $hasFilter = $this->hasFilter;

        $components = collect([]);
        $features =
        $paramsFilter =
        $dataFilterRelations = [];
        $token = Session::get('pc-constructor')[0];

        $section = Section::where('slug', $slug)->with('components')->get()->pluck(null, 'id');

        abort_if($section->isEmpty(), 404);

        $section = $section->first();

        $featureTypes = FeatureType::where('section_id', $section->id)
            ->with(['features', 'values', 'uiFilter'])
            ->get()
            ->filter(function ($item) {
            return $item->features->isNotEmpty() || $item->values->isNotEmpty();
        });

        $features= Feature::all();

        $allComponents = Configuration::where('token_configuration', $token)->get()->countBy('component_id');

        $data = Value::getValuesByConfiguration($token);

        $data->map(function ($item, $key) use ($data, $slug, &$allComponents, &$section, &$featureTypes, &$paramsFilter, &$dataFilterRelations, &$features) {
            if($item->relation_type == 'one-to-many') {
                if($section->id != $item->section_id) {
                    if ($section->id == $item->section_first) {
                        $values = $item->features_second ? $item->features_second : $item->values_second;
                        $values = explode(',', $values);

                        if (in_array($item->feature_id, $values)) {
                            $paramsFilter['relations'][$key]['feature_type'] = $item->feature_type_first;
                            $paramsFilter['relations'][$key]['features'] = $item->features_first;
                            $paramsFilter['relations'][$key]['values'] = $item->values_first;
                            $paramsFilter['relations'][$key]['relation_type'] = $item->relation_type;
                            $item['relation_features'] = $this->getNamesFeatures($item->features_first, $features);
                            $item['relation_features_slug'] = $this->getSlugsFeatures($item->features_first, $features);
                            $item['relation_values'] = $this->getNamesValues($item->values_first);
                            $dataFilterRelations['required'][$item->section_name][] = $item;
                        }
                    } elseif ($section->id == $item->section_second) {
                        $values = $item->features_first ? $item->features_first : $item->values_first;
                        $values = explode(',', $values);

                        if (in_array($item->feature_id, $values)) {
                            $paramsFilter['relations'][$key]['feature_type'] = $item->feature_type_second;
                            $paramsFilter['relations'][$key]['features'] = $item->features_second;
                            $paramsFilter['relations'][$key]['values'] = $item->values_second;
                            $paramsFilter['relations'][$key]['relation_type'] = $item->relation_type;
                            $item['relation_feature_type'] = $featureTypes->firstWhere('id', $item->feature_type_second)->name;
                            $item['relation_features'] = $this->getNamesFeatures($item->features_second, $features);
                            $item['relation_features_slug'] = $this->getSlugsFeatures($item->features_second, $features);
                            $item['relation_values'] = $this->getNamesValues($item->values_second);
                            $dataFilterRelations['required'][$item->section_name][] = $item;
                        }
                    }
                }
            }
            elseif($item->relation_type == 'section') {
                if ($section->id == $item->section_second) {
                    $dataFilterRelations['norequired'][$item->section_name][] = $item;
                }
            }
            elseif($item->relation_type == 'size') {
                if($section->id != $item->section_id) {
                    if ($section->id == $item->section_first) {
                        $paramsFilter['relations'][$key]['sign'] = '>=';

                        $paramsFilter['relations'][$key]['feature_type'] = $item->feature_type_first;
                        $paramsFilter['relations'][$key]['features'] = $item->features_first;
                        $paramsFilter['relations'][$key]['values'] = $item->value;
                        $paramsFilter['relations'][$key]['relation_type'] = $item->relation_type;
                        $dataFilterRelations['required'][$item->section_name][] = $item;
                    } elseif ($section->id == $item->section_second) {
                        $paramsFilter['relations'][$key]['sign'] = '<=';

                        $paramsFilter['relations'][$key]['feature_type'] = $item->feature_type_second;
                        $paramsFilter['relations'][$key]['features'] = $item->features_second;
                        $paramsFilter['relations'][$key]['values'] = $item->value;
                        $paramsFilter['relations'][$key]['relation_type'] = $item->relation_type;
                        $dataFilterRelations['required'][$item->section_name][] = $item;
                    }
                }
            }
            elseif($item->relation_type == 'quantity') {
                $keyNew = $item->feature_type_first . '-' . $item->feature_type_second;

                if ($allComponents->has($item->component_id)) {
                    if ($section->id == $item->section_first) {
                        if ($section->id != $item->section_id) {

                            if ($item->value) {
                                $paramsFilter['relations'][$keyNew]['sign'] = '>=';
                                $paramsFilter['relations'][$keyNew]['feature_type'] = $item->feature_type_first;
                                $paramsFilter['relations'][$keyNew]['features'] = $item->features_first;
                                $paramsFilter['relations'][$keyNew]['relation_type'] = $item->relation_type;

                                for ($i = 0; $i < $allComponents[$item->component_id]; ++$i) {
                                    $dataFilterRelations[$item->section_name][] = $item;
                                }

                                if (isset($paramsFilter['relations'][$keyNew]['values'])) {
                                    $paramsFilter['relations'][$keyNew]['values'] = $paramsFilter['relations'][$keyNew]['values'] + ($item->value * $allComponents[$item->component_id]);
                                } else {
                                    $paramsFilter['relations'][$keyNew]['values'] = $item->value * $allComponents[$item->component_id];
                                }
                            }
                        } elseif ($section->id == $item->section_second) {
                            if ($section->id != $item->section_id) {
                                $thisSection = $data->first(function ($i) use ($item, $slug) {
                                    return $i->section_second == $item->section_second
                                        && $i->section_slug == $slug
                                        && $i->feature_type_first == $item->feature_type_first
                                        && $i->feature_type_second == $item->feature_type_second;
                                });

                                if ($thisSection && $thisSection->value) {
                                    $thisValue = $thisSection->value;

                                    $paramsFilter['relations'][$keyNew]['sign'] = '<=';
                                    $paramsFilter['relations'][$keyNew]['feature_type'] = $item->feature_type_second;
                                    $paramsFilter['relations'][$keyNew]['features'] = $item->features_second;
                                    $paramsFilter['relations'][$keyNew]['relation_type'] = $item->relation_type;

                                    for ($i = 0; $i < $allComponents[$item->component_id]; ++$i) {
                                        $dataFilterRelations[$item->section_name][] = $item;
                                    }


                                    if (isset($paramsFilter['relations'][$keyNew]['values'])) {
                                        echo $thisValue;
                                        $paramsFilter['relations'][$keyNew]['values'] = $paramsFilter['relations'][$keyNew]['values'] - ($thisValue * $allComponents[$item->component_id]);
                                    } else {
                                        echo $thisValue;
                                        $paramsFilter['relations'][$keyNew]['values'] = $item->value - ($thisValue * $allComponents[$item->component_id]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        });

        if(!empty($request->all())) {
            $paramsFilter = $this->sortParamsFilter($request->all(), $paramsFilter);
        }

        //ФИЛЬТРАЦИЯ ДУБЛИРУЮЩИХСЯ ЗНАЧЕНИЙ ДЛЯ ДАЛЬНЕЙШЕй ВЫБОРКИ
        $paramsFilter = $this->filterDobbedValues($paramsFilter);

        $featureTypes = $featureTypes->toArray();

        if(!empty($paramsFilter)) {
            $components = Value::getComponentsByFilter($paramsFilter, $section->id);
        }
        else {
            $components = $section->components;
        }

        $features = collect([]);

        // Требуется для изменения отображаения фильтра (возможно, потребуется убрать)
        if(!$components->isEmpty() && !isset($paramsFilter['list'])) {
            extract($this->filterFeatureTypes($components, $featureTypes));
        }

        $dataFilterRelations = collect($dataFilterRelations);
        $featureTypes = array_column($featureTypes, null, 'id');

        if(isset($paramsFilter['relations'])) {
            foreach ($paramsFilter['relations'] as $p) {
                if(array_key_exists($p['feature_type'], $featureTypes)) {
                    if(!empty($featureTypes[$p['feature_type']]['features'])) {
                        $f1 = array_column($featureTypes[$p['feature_type']]['features'], null, 'id');
                        $f2 = array_flip(explode(',', $p['features']));
                        $inter = array_intersect_key($f1, $f2);

                        if(!empty($inter)) {
                            $featureTypes[$p['feature_type']]['hasRelation'] = true;
                        }

                        $featureTypes[$p['feature_type']]['features'] = array_values($inter);
                    }

                    if(!empty($featureTypes[$p['feature_type']]['values'])) {
                        $v1 = array_column($featureTypes[$p['feature_type']]['values'], null, 'value');
                        $v2 = array_flip(explode(',', $p['values']));
                        $inter = array_intersect_key($v1, $v2);

                        if(!empty($inter)) {
                            $featureTypes[$p['feature_type']]['hasRelation'] = true;
                        }

                        $featureTypes[$p['feature_type']]['values'] = array_values($inter);
                    }
                }
            }
        }

        $featureTypes = collect($featureTypes);

        return view('pages.catalog.index', compact(
            'section',
            'featureTypes',
            'components',
            'hasFilter',
            'paramsFilter',
            'dataFilterRelations',
            'features'
            )
        );
    }
}
