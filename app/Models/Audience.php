<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Audience extends Model
{
    use HasFactory;

    protected $table = "audiences";
    protected $guarded = [];

    public function optimizeTarget()
    {
        return $this->hasOne('\App\Models\OptimizeTarget', 'id', 'audience_id');
    }

    public function balanceTarget()
    {
        return $this->hasOne('\App\Models\BalanceTarget', 'id', 'audience_id');
    }

    public function detailTarget()
    {
        return $this->hasOne('\App\Models\DetailTarget', 'id', 'audience_id');
    }
}
