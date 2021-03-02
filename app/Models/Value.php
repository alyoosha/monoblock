<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Yadakhov\InsertOnDuplicateKey;

class Value extends Model
{
    use HasFactory;
    use InsertOnDuplicateKey;

    public function feature() {
        return $this->belongsTo('App\Models\Feature', 'feature_id', 'id');
    }

    public function featureType() {
        return $this->belongsTo('App\Models\FeatureType', 'feature_type_id');
    }

    public static function getComponentsByFilter($data, $id) {
        $query = '';
        $subQuery = '';
        $subQueryBracket = '';

        if(isset($data['relations'])) {
            foreach ($data['relations'] as $item) {

                $values = $item['values'] ? $item['values'] : $item['features'];

                if(!$values) continue;

                $column = $item['values'] ? 'value' : 'feature_id';
                $featureType = $item['feature_type'];

                if ($item['relation_type'] == 'one-to-many') {
                    if (!empty($query)) {
                        $subQuery = ' AND component_id IN (SELECT component_id FROM `values` WHERE ';
                        $subQueryBracket = ')';
                    }

                    $query .= $subQuery . '`values`.feature_type_id = ' . $featureType . ' AND ' . $column . ' IN (' . $values . ')' . $subQueryBracket;
                }
                elseif ($item['relation_type'] == 'size') {
                    $sign = $item['sign'];

                    if (!empty($query)) {
                        $subQuery = ' AND component_id IN (SELECT component_id FROM `values` WHERE ';
                        $subQueryBracket = ')';
                    }

                    $query .= $subQuery . '`values`.feature_type_id = ' . $featureType . ' AND ' . $column .  ' ' . $sign  . ' ' . $values . $subQueryBracket;
                }
                elseif ($item['relation_type'] == 'section') {
                    // НЕ ТРЕБУЮТСЯ КАКИЕ-ЛИБО ДЕЙСТВИЯ
                }
                elseif ($item['relation_type'] == 'quantity') {
                    if($values) {
                        $sign = $item['sign'];

                        if (!empty($query)) {
                            $subQuery = ' AND component_id IN (SELECT component_id FROM `values` WHERE ';
                            $subQueryBracket = ')';
                        }

                        $query .= $subQuery . '`values`.feature_type_id = ' . $featureType . ' AND ' . $column .  ' ' . $sign  . ' ' . $values . $subQueryBracket;
                    }
                }
            }
        }

        if(isset($data['features']) && isset($data['features']['list'])) {
            foreach ($data['features']['list'] as $k => $item) {
                if(!empty($query)) {
                    $subQuery = ' AND component_id IN (SELECT component_id FROM `values` WHERE ';
                    $subQueryBracket = ')';
                }

                $query .= $subQuery . '`values`.feature_type_id = ' . $k  . ' AND feature_id IN (' . implode(',', $item) . ')' . $subQueryBracket;
            }
        }

        if(isset($data['values']) && isset($data['values']['number'])) {
            foreach ($data['values']['number'] as $k => $item) {
                if(isset($item['values']['from'])) {
                    if(!empty($query)) {
                        $subQuery = ' AND component_id IN (SELECT component_id FROM `values` WHERE ';
                        $subQueryBracket = ')';
                    }

                    $query .= $subQuery . '`values`.feature_type_id = ' . $item['feature_type_id'] . ' AND value >= ' . $item['values']['from'];

                    if(isset($item['values']['to'])) {
                        $query .= ' AND value <= ' . $item['values']['to'] . $subQueryBracket;
                        continue;
                    }

                    $query .= $subQueryBracket;
                    continue;
                }
                else {
                    if(!empty($query)) {
                        $subQuery = ' AND component_id IN (SELECT component_id FROM `values` WHERE ';
                        $subQueryBracket = ')';
                    }

                    $query .= $subQuery . '`values`.feature_type_id = ' . $item['feature_type_id'] . ' AND value <= ' . $item['values']['to'] . $subQueryBracket;
                    continue;
                }
            }
        }

        if(isset($data['values']) && isset($data['values']['string'])) {
            foreach ($data['values']['string'] as $k => $item) {
                if(!empty($query)) {
                    $subQuery = ' AND component_id IN (SELECT component_id FROM `values` WHERE ';
                    $subQueryBracket = ')';
                }

                $query .= $subQuery . '`values`.feature_type_id = ' . $k  . ' AND value IN ("' . implode('","', $item['strings']) . '")' . $subQueryBracket;
            }
        }

        if(isset($data['values']) && isset($data['values']['boolean'])) {
            foreach ($data['values']['boolean'] as $k => $item) {
                if(!empty($query)) {
                    $subQuery = ' AND component_id IN (SELECT component_id FROM `values` WHERE ';
                    $subQueryBracket = ')';
                }

                $query .= $subQuery . '`values`.feature_type_id = ' . $k  . ' AND value = ' . $item['boolean'] . $subQueryBracket;
            }
        }

        if($query != '') {
            return self::selectRaw('
                `values`.component_id, 
                `values`.feature_id, 
                `values`.value,
                `values`.feature_type_id,
                components.id,
                components.short_name,
                components.price
                ')
                    ->rightJoin('components', 'components.id', 'component_id')
                    ->whereRaw($query)
                    ->get();
        }
        else {
            return self::selectRaw('
                `values`.component_id,
                `values`.feature_id,
                `values`.value,
                `values`.feature_type_id,
                components.id,
                components.short_name,
                components.price
                ')
                ->join('components', function ($join) use ($id) {
                    $join->on('components.id', '=', 'component_id')
                        ->where('components.section_id', '=', $id);
                })
                ->groupBy('components.id')
                ->get();
        }

        return collect([]);
    }

    public static function getValuesByConfiguration($token) {
        return self::whereRaw("component_id in (select component_id from configurations where token_configuration = '" . $token . "')")
            ->join('relations', function ($join) {
                $join->on('relations.feature_type_second', '=', 'values.feature_type_id')
                    ->orOn('relations.feature_type_first', '=', 'values.feature_type_id');
            })
            ->join('components', 'components.id', 'values.component_id')
            ->join('feature_types', 'values.feature_type_id', 'feature_types.id')
            ->join('sections', 'sections.id', 'feature_types.section_id')
            ->leftJoin('features', 'values.feature_id', 'features.id')
            ->leftJoin('ui_filters', 'feature_types.ui_filter_id', 'ui_filters.id')
            ->leftJoin('relation_types', 'relation_types.id', 'relations.relation_type_id')
            ->get(
                [
                    'components.id as component_id',
                    'components.short_name as component_short_name',
                    'feature_types.id as feature_type_id',
                    'features.id as feature_id',
                    'sections.id as section_id',
                    'sections.name as section_name',
                    'values.feature_id as values_feature_id',
                    'values.feature_type_id as values_feature_type_id',
                    'feature_types.name as feature_type_name',
                    'feature_types.show_name_in_filter as show_name',
                    'ui_filters.name as ui_filter_name',
                    'ui_filters.slug as ui_filter_slug',
                    'features.slug as feature_slug',
                    'features.name as feature_name',
                    'value',
                    'section_first',
                    'section_second',
                    'feature_type_first',
                    'feature_type_second',
                    'features_first',
                    'features_second',
                    'values_first',
                    'values_second',
                    'relation_type_id',
                    'relation_types.code as relation_type',
                    'relation_types.name as relation_type_name'
                ]
            );
    }
}
