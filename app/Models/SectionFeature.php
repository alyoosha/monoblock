<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Yadakhov\InsertOnDuplicateKey;

class SectionFeature extends Model
{
    use HasFactory;
    use InsertOnDuplicateKey;

    protected $table = "section_feature";
}
