<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 26.01.2021
 * Time: 21:30
 */

namespace App\Traits;


use App\Models\Feature;
use App\Models\FeatureType;
use App\Models\Relation;
use App\Models\RelationType;
use App\Models\Section;
use App\Models\Value;
use Illuminate\Support\Arr;

trait RelationTrait
{
    public function getRelationsWithNames($relation) {
        $sections = Arr::pluck(Section::all(), null, 'id');
        $featureTypes = Arr::pluck(FeatureType::all(), null, 'id');
        $features = Arr::pluck(Feature::all(), null, 'id');
        $values = Arr::pluck(Value::all(), null, 'value');

        $theseRelations = Relation::where('section_first', $relation->section_first)
            ->where('section_second', $relation->section_second)
            ->where('feature_type_first', $relation->feature_type_first)
            ->where('feature_type_second', $relation->feature_type_second)
            ->get();

        $theseRelations->firstWhere('id', $relation->id)['selected'] = true;

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

        return $theseRelations;
    }

    public function checkSpecialCases($configurations) {
        dd($configurations);
    }
}