<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    // list type
    const VALUE = 1;
    const PERCENTAGE = 2;

    // list status
    const ACTIVE = 2;
}
