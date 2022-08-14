<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdsPage extends Model
{
    use HasFactory;

    protected $table = "ads_page";
    protected $guarded = [];

    public function images()
    {
        return $this->hasMany('\App\Models\Media', 'owner_id')
            ->whereIn('type', ['ads_logo','ads_banner']);
    }
}
