<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Yadakhov\InsertOnDuplicateKey;

class FeatureType extends Model
{
    use HasFactory;
    use InsertOnDuplicateKey;

    public $timestamps = false;

    public function filterType() {
        return $this->belongsTo(FilterType::class,'filter_type_id', 'id');
    }

    public function section() {
        return $this->belongsTo('App\Models\Section', 'section_id', 'id');
    }

    public function features() {
        return $this->hasMany(Feature::class,'feature_type_id', 'id');
    }

    public function values() {
        return $this->hasMany(Value::class,'feature_type_id', 'id')->orderBy('value');
    }

    public function uiFilter() {
        return $this->belongsTo(UiFilter::class,'ui_filter_id', 'id');
    }
}
