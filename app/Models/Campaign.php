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
        return $this->hasMany('\App\Models\Audience', 'id', 'campaign_id')
            ->with('optimizeTarget','balanceTarget','detailTarget');
    }

    public function adsPage()
    {
        return $this->hasOne('\App\Models\AdsPage', 'id', 'campaign_id')
            ->with('images');
    }

    public function ads()
    {
        return $this->hasMany('\App\Models\Ads', 'id', 'campaign_id')
            ->with('image');
    }
}
