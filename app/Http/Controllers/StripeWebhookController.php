<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\StripePayment;
use Contentful\Management\Client;
use Illuminate\Http\Request;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $data = $this->webhookCall->payload;
        logger($data);

        // if ($request->type === 'checkout.session.completed' && $request->data['object']['payment_status'] === 'paid') {

        //     $paymentid = StripePayment::where('stripe_id', $request->data['object']['id'])->first();
        //     $campaign = Campaign::where('id', $paymentid->campaign_id)->first();

        //     $client = new Client(env('CONTENTFUL_MANAGEMENT_ACCESS_TOKEN'));
        //     $environment = $client->getEnvironmentProxy(env('CONTENTFUL_SPACE_ID'), 'master');

        //     $updatepayment = StripePayment::where('stripe_id', $request->data['object']['id'])->first();
        //     $updatepayment->status = '1';
        //     $updatepayment->save();

        //     $entry = $environment->getEntry($campaign->entry_id);
        //     $entry->setField('paymentStatus', 'en-US', true);
        //     $entry->update();
        //     $entry->publish();

        //     $updatestatus = StripePayment::where('stripe_id', $request->data['object']['id'])->first();
        //     $updatestatus->status = '1';
        //     $updatestatus->save();

        //     return response()->json(['success' => true], 200);
        // }
    }
}
