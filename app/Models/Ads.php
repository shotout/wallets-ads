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
}
