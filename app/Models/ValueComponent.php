<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Yadakhov\InsertOnDuplicateKey;

class ValueComponent extends Model
{
    protected $table = 'value_components';

    use HasFactory;
    use InsertOnDuplicateKey;
}
