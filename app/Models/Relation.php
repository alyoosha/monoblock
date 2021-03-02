<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Yadakhov\InsertOnDuplicateKey;

class Relation extends Model
{
    use HasFactory;
    use InsertOnDuplicateKey;

    protected $table = 'relations';

    public $timestamps = false;

    protected $fillable = [
        'section_first',
        'section_second',
        'feature_type_first',
        'feature_type_second',
        'features_first',
        'features_second',
        'values_first',
        'values_second',
        'relation_type_id'
    ];

    public function feature_type_first() {
        return $this->belongsTo('App\Models\FeatureType', 'feature_type_first', 'id');
    }

    public function feature_type_second()
    {
        return $this->belongsTo('App\Models\FeatureType', 'feature_type_second', 'id');
    }

    public static function updateOrCreateByFirstFeatures($values) {
        self::updateOrCreate(
            [
                'section_first' => $values[0]['section_first'],
                'section_second' => $values[0]['section_second'],
                'feature_type_first' => $values[0]['feature_type_first'],
                'feature_type_second' => $values[0]['feature_type_second'],
                'features_first' => $values[0]['features_first'],

            ],
            [
                'features_second' => $values[0]['features_second'],
                'values_first' => $values[0]['values_first'],
                'values_second' => $values[0]['values_second'],
                'relation_type_id' => $values[0]['relation_type']
            ]
        );
    }

    public static function updateOrCreateByFirstValues($values) {
        self::updateOrCreate(
            [
                'section_first' => $values[0]['section_first'],
                'section_second' => $values[0]['section_second'],
                'feature_type_first' => $values[0]['feature_type_first'],
                'feature_type_second' => $values[0]['feature_type_second'],
                'values_first' => $values[0]['features_first'],

            ],
            [
                'features_first' => $values[0]['features_first'],
                'features_second' => $values[0]['features_second'],
                'values_second' => $values[0]['values_second'],
                'relation_type_id' => $values[0]['relation_type']
            ]
        );
    }
}
