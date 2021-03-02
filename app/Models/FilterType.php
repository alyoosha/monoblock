<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Yadakhov\InsertOnDuplicateKey;

class FilterType extends Model
{
    use HasFactory;
    use InsertOnDuplicateKey;

    public function featureTypes() {
        $this->hasMany(FeatureType::class,'filter_type_id', 'id');
    }
}
