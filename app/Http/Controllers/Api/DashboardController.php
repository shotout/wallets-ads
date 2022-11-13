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
        ], 200);    
    }

    public function campaigns(Request $request)
    {
        if ($request->has('length') && $request->input('length') != '') {
            $length = $request->input('length');
        } else {
            $length = 5;
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
            "impression" => Campaign::where('user_id', auth('sanctum')->user()->id)->sum('count_impression'),
            "view" => Campaign::where('user_id', auth('sanctum')->user()->id)->sum('count_view'),
        );

        return response()->json([
            'status' => 'success',
            'data' => $campaigns,
            'counter' => $counter,
        ], 200);    
    }

    public function audiences($id)
    {
        $campaign = Campaign::find($id);

        if (!$campaign) {
            return response()->json([
                'status' => 'failed',
                'message' => 'data not found'
            ], 404); 
        }

        $audiences = Audience::where('campaign_id', $id)->with('ads')->with('file')->get();

        $airdrop =  Audience::where('campaign_id', $id)->sum('count_airdrop');
        $click =  Audience::where('campaign_id', $id)->sum('count_click');
        $mint =  Audience::where('campaign_id', $id)->sum('count_mint');
        $impression =  Audience::where('campaign_id', $id)->sum('count_impression');
        $view =  Audience::where('campaign_id', $id)->sum('count_view');

        $counter = (object) array(
            "airdrop" => $airdrop,
            "click" => $click,
            "mint" => $mint,
            "impression" => $impression,
            "view" => $view,
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
        ], 200);  
    }

    public function exportAudiences($id)
    {
        $campaign = Campaign::find($id);

        if (!$campaign) {
            return response()->json([
                'status' => 'failed',
                'message' => 'data not found'
            ], 404); 
        }
        
        $audiences = Audience::where('campaign_id', $id)->with('ads')->get();

        $airdrop =  Audience::where('campaign_id', $id)->sum('count_airdrop');
        $click =  Audience::where('campaign_id', $id)->sum('count_click');
        $mint =  Audience::where('campaign_id', $id)->sum('count_mint');
        $impression =  Audience::where('campaign_id', $id)->sum('count_impression');
        $view =  Audience::where('campaign_id', $id)->sum('count_view');

        $counter = (object) array(
            "airdrop" => $airdrop,
            "click" => $click,
            "mint" => $mint,
            "impression" => $impression,
            "view" => $view,
        );

        $data = (object) array(
            "campaign" => $campaign,
            "audiences" => $audiences,
            'counter' => $counter
        );

        return Excel::download(new CampaignAudience($data), 'audience '.$campaign->name.'.xlsx');
    }
}