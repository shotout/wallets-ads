<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailTarget extends Model
{
    use HasFactory;

    protected $table = "detailed_targeting";
    protected $guarded = [];
}
