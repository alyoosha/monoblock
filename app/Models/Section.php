<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Yadakhov\InsertOnDuplicateKey;

class Section extends Model
{
    use HasFactory;
    use InsertOnDuplicateKey;

    protected $appends = [
        'url',
    ];

    protected $casts = [
        'url' => 'string',
    ];

    public function featureTypes() {
        return $this->belongsToMany('App\Models\FeatureType', 'section_feature_type');
    }

    public function components() {
        return $this->hasMany('App\Models\Component', 'section_id');
    }

    public function features() {
        return $this->belongsToMany('App\Models\Feature', 'section_feature');
    }

    public function getUrlAttribute() {
        return route('pages.catalog.index', $this->slug);
    }
}
