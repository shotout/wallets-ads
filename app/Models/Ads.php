<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ads extends Model
{
    use HasFactory;

    protected $table = "ads";
    protected $guarded = [];

    public function image()
    {
        return $this->hasOne('\App\Models\Media', 'owner_id')
            ->where('type', 'ads_nft');
    }

    public function countAirdrop()
    {
        return self::sum('count_airdrop');
    }

    public function countClick()
    {
        return self::sum('count_click');
    }

    public function countMint()
    {
        return self::sum('count_mint');
    }
}
