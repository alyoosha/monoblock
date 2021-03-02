<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Yadakhov\InsertOnDuplicateKey;

class Component extends Model
{
    use HasFactory;
    use InsertOnDuplicateKey;

    public function section() {
        return $this->belongsTo('App\Models\Section', 'section_id', 'id');
    }
}
