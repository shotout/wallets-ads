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
        return $this->hasOne('\App\Models\OptimizeTarget');
    }

    public function balanceTarget()
    {
        return $this->hasOne('\App\Models\BalanceTarget');
    }

    public function detailTarget()
    {
        return $this->hasOne('\App\Models\DetailTarget');
    }

    public function file()
    {
        return $this->hasOne('\App\Models\Media', 'owner_id')
            ->where('type', 'audience_file');
    }

    public function ads()
    {
        return $this->belongsTo('\App\Models\Ads', 'ads_id')->with('image');
    }
}
