<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    protected $table = "campaigns";
    protected $guarded = [];

    public function audiences()
    {
        return $this->hasMany('\App\Models\Audience')->with('optimizeTarget','balanceTarget','detailTarget');
    }

    public function adsPage()
    {
        return $this->hasOne('\App\Models\AdsPage')->with('images');
    }

    public function ads()
    {
        return $this->hasMany('\App\Models\Ads')->with('image');
    }
}
