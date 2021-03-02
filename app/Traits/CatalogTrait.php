<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 26.01.2021
 * Time: 9:19
 */

namespace App\Traits;


use App\Models\Value;

trait CatalogTrait
{
    public function getNamesFeatures($str, $features) {
        if(is_null($str)) return null;
        $arr = explode(',', $str);
        $names = [];

        foreach ($arr as $item) {
            $value = $features->firstWhere('id', $item);

            if($value) {
                $names[] = $value->name;
            }
        }

        return $names;
    }

    public function getSlugsFeatures($str, $features) {
        if(is_null($str)) return null;
        $arr = explode(',', $str);
        $slugs = [];

        foreach ($arr as $item) {
            $value = $features->firstWhere('id', $item);

            if($value) {
                $slugs[] = $value->slug;
            }
        }

        return $slugs;
    }

    public function getNamesValues($str) {
        if(is_null($str)) return null;
        return explode(',', $str);
    }

    public function sortParamsFilter($request, $paramsFilter) {
        foreach($request as $k => $input) {
            if(!is_null($input)) {
                if(preg_match('/(?:[a-z0-9-]+)%[25]*L(?<id>[0-9]+)%[25]*D(?<feature_id>[0-9]+)$/', $k, $matches)) {
                    $paramsFilter['features']['list'][$matches['id']][] = $matches['feature_id'];
                }
                elseif(preg_match('/^from%[25]*N(?<slug>[a-z0-9-]+)%R(?<id>[0-9]+)$/', $k, $matches)) {
                    $paramsFilter['values']['number'][$matches['id']]['feature_type_id'] = (int) $matches['id'];
                    $paramsFilter['values']['number'][$matches['id']]['values']['from'] = (int) $input;
                }
                elseif (preg_match('/^to%[25]*N(?<slug>[a-z0-9-]+)%[25]*R(?<id>[0-9]+)$/', $k, $matches)) {
                    $paramsFilter['values']['number'][$matches['id']]['feature_type_id'] = (int) $matches['id'];
                    $paramsFilter['values']['number'][$matches['id']]['values']['to'] = (int) $input;
                }
                elseif (preg_match('/(?:[a-z0-9-]+)%[25]*S(?<value_id>[0-9-]+)%[25]*T(?<id>[0-9]+)$/', $k, $matches)) {
                    $paramsFilter['values']['string'][$matches['id']]['feature_type_id'] = (int) $matches['id'];
                    $paramsFilter['values']['string'][$matches['id']]['strings'][] = (string) $input;
                }
                elseif (preg_match('/(?:[a-z0-9-]+)%[25]*B(?<id>[0-9]+)$/', $k, $matches)) {
                    $paramsFilter['values']['boolean'][$matches['id']]['feature_type_id'] = (int) $matches['id'];
                    $paramsFilter['values']['boolean'][$matches['id']]['boolean'] = (int) $input;
                }
            }
        }

        return $paramsFilter;
    }

    public function filterDobbedValues($paramsFilter) {
        if(isset($paramsFilter['features']['list']) && isset($paramsFilter['relations'])) {
            foreach ($paramsFilter['relations'] as &$relation) {
                if(isset($paramsFilter['features']['list'][$relation['feature_type']])) {
                    $relation['features'] = implode(',', $paramsFilter['features']['list'][$relation['feature_type']]);
                    unset($paramsFilter['features']['list'][$relation['feature_type']]);
                }
            }
        }

        if(isset($paramsFilter['features']['string']) && isset($paramsFilter['relations'])) {
            foreach ($paramsFilter['relations'] as $key => &$relation) {
                if(isset($paramsFilter['features']['string'][$relation['feature_type']])) {
                    $relation['values'] = implode(',', $paramsFilter['features']['string'][$relation['feature_type']]);
                    unset($paramsFilter['features']['string'][$relation['feature_type']]);
                }
            }
        }

        return$paramsFilter;
    }

    public function filterFeatureTypes($components, $featureTypes) {
        foreach ($components as $c) {
            $ids[] = $c->id;
        }

        $features = Value::whereIn('component_id', $ids)
            ->leftJoin('features', 'features.id', 'values.feature_id')
            ->get(
                [
                    'values.feature_type_id',
                    'features.id as feature_id',
                    'features.slug as slug',
                    'values.value',
                ]
            );

        $valuesValues = $features->groupBy('value')->keys()->flip()->toArray();
        $idsFeatures = $features->groupBy('feature_id')->keys()->flip()->toArray();

        foreach ($featureTypes as $k => &$featureType) {
            if(!empty($featureType['features'])) {
                $featureType['features'] = array_column($featureType['features'], null, 'id');
                $inter = array_intersect_key($featureType['features'], $idsFeatures);
                $featureType['features'] = array_values($inter);
            }

            if(empty($featureType['features']) && empty($featureType['values'])) {
                unset($featureTypes[$k]);
            }
        }

        foreach ($featureTypes as $k => &$featureType) {
            if(!empty($featureType['values'])) {
                $featureType['values'] = array_column($featureType['values'], null, 'value');
                $inter = array_intersect_key($featureType['values'], $valuesValues);
                $featureType['values'] = array_values($inter);
            }

            if(empty($featureType['features']) && empty($featureType['values'])) {
                unset($featureTypes[$k]);
            }
        }

        return compact('featureTypes', 'features');
    }
}