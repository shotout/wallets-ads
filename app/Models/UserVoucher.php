<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserVoucher extends Model
{
    use HasFactory;

    protected $table = 'user_voucher';

    // list type
    const NEW_USER = 1;

    // list status
    const CREATED = 1;
    const USED = 2;
}
