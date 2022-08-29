<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Ads;
use App\Models\Media;
use App\Models\AdsPage;
use App\Models\Audience;
use App\Models\Campaign;
use App\Models\DetailTarget;
use Illuminate\Http\Request;
use App\Models\BalanceTarget;
use App\Models\OptimizeTarget;
use App\Exports\CampaignAudience;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class DashboardController extends Controller
{
    public function listCampaign()
    {
        $listCampaign = Campaign::select('id','name')
            ->where('user_id', auth('sanctum')->user()->id)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $listCampaign
        ]);    
    }

    public function campaigns(Request $request)
    {
        if ($request->has('length') && $request->input('length') != '') {
            $length = $request->input('length');
        } else {
            $length = 10;
        }

        if ($request->has('column') && $request->input('column') != '') {
            $column = $request->input('column');
        } else {
            $column = 'id';
        }

        if ($request->has('dir') && $request->input('dir') != '') {
            $dir = $request->input('dir');
        } else {
            $dir = 'desc';
        }

        $query = Campaign::where('user_id', auth('sanctum')->user()->id)
            ->orderBy($column, $dir);

        if ($request->has('status') && $request->input('status') != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && $request->input('search') != '') {
            $query->where(function($q) use($request) {
                $q->where('field1', 'like', '%' . $request->input('search') . '%')
                    ->orWhere('field2', 'like', '%' . $request->input('search') . '%');
            });
        }

        $campaigns = $query->paginate($length);

        $counter = (object) array(
            "airdrop" => Campaign::where('user_id', auth('sanctum')->user()->id)->sum('count_airdrop'),
            "click" => Campaign::where('user_id', auth('sanctum')->user()->id)->sum('count_click'),
            "mint" => Campaign::where('user_id', auth('sanctum')->user()->id)->sum('count_mint'),
        );

        return response()->json([
            'status' => 'success',
            'data' => $campaigns,
            'counter' => $counter,
        ]);    
    }

    public function audiences($id)
    {
        $campaign = Campaign::find($id);
        $audiences = Audience::where('campaign_id', $id)->with('ads')->get();

        $counter = (object) array(
            "airdrop" => $audiences[0]->ads->countAirdrop(),
            "click" => $audiences[0]->ads->countClick(),
            "mint" => $audiences[0]->ads->countMint(),
        );

        // $adc = Audience::where('campaign_id', $id)->with('ads')->get();
        // foreach ($adc as $a) {
        //     $counter->airdrop += $a->ads->count_airdrop;
        //     $counter->click += $a->ads->count_click;
        //     $counter->mint += $a->ads->count_mint;
        // }

        $data = (object) array(
            "campaign" => $campaign,
            "audiences" => $audiences,
            'counter' => $counter
        );

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);  
    }

    public function exportAudiences($id)
    {
        $campaign = Campaign::find($id);
        $audiences = Audience::where('campaign_id', $id)->with('ads')->get();

        $counter = (object) array(
            "airdrop" => $audiences[0]->ads->countAirdrop(),
            "click" => $audiences[0]->ads->countClick(),
            "mint" => $audiences[0]->ads->countMint(),
        );

        $data = (object) array(
            "campaign" => $campaign,
            "audiences" => $audiences,
            'counter' => $counter
        );

        return Excel::download(new CampaignAudience($data), 'audience '.$campaign->name.'.xlsx');
    }
}