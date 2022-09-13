<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    protected $table = "campaigns";
    protected $guarded = [];

    const IN_REVIEW = 1;
    const FINISHED = 2;

    public function audiences()
    {
        return $this->hasMany('\App\Models\Audience')->with('detailTarget','file');
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
