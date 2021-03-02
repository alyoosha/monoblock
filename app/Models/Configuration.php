<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'component_id',
        'token_configuration'
    ];

    public function component() {
        return $this->belongsTo('App\Models\Component', 'component_id', 'id');
    }

    public function values() {
        return $this->hasMany('App\Models\Value', 'component_id', 'component_id');
    }
}
