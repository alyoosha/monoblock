<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Yadakhov\InsertOnDuplicateKey;

class Feature extends Model
{
    use HasFactory;
    use InsertOnDuplicateKey;

    public function sections() {
        return $this->belongsToMany('App\Models\Section', 'section_feature');
    }

    public function featureType() {
        return $this->belongsTo('App\Models\FeatureType', 'feature_type_id');
    }
}
