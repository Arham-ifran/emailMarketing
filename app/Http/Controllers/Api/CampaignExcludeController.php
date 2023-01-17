<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CampaignExclude;
use App\Models\SmsCampaign;
use App\Models\Contact;
use App\Models\EmailCampaign;
use Illuminate\Support\Facades\Auth;
use Hashids;

class CampaignExcludeController extends Controller
{
    /**
     * Create a new CampaignExclude instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function addToCampaign(Request $request)
    {
        $add = 0;
        for ($i = 0; $i < sizeof($request->contact); $i++) {
            $contact_id = Hashids::decode($request->contact[$i])[0];
            $campaign_id = Hashids::decode($request->campaign)[0];
            $contact = Contact::where('id', $contact_id)->first();
            if ($request->type == 1) {
                // sms
                $campaign = SmsCampaign::where('id', $campaign_id)->first();
            } else {
                // email
                $campaign = EmailCampaign::where('id', $campaign_id)->first();
            }
            if ($contact && $campaign) {
                if ($contact->user_id != auth()->user()->id || $campaign->user_id != auth()->user()->id) {
                    return response("", 403);
                }
                $data = ['contact_id' => $contact->id, 'campaign_id' => $campaign->id, 'user_id' => auth()->user()->id, 'type' => $request->type];
                $find = CampaignExclude::where('deleted_at', null)->where('user_id', auth()->user()->id)->where('campaign_id', $campaign->id)->where('contact_id', $contact->id)->first();
                if (!$find)
                    CampaignExclude::create($data);
                else
                    $add--;
                $add++;
            }
        }
        $response = [
            'total' => $add,
            'message' => "Contacts Added to the Campaign successfully"
        ];
        return response($response, 200);
    }

    public function removeFromCampaign(Request $request)
    {
        $del = 0;

        for ($i = 0; $i < sizeof($request->contact); $i++) {
            $contact_id = Hashids::decode($request->contact[$i])[0];
            $campaign_id = Hashids::decode($request->campaign)[0];
            $contact = Contact::where('id', $contact_id)->first();
            if ($request->type == 1) {
                // sms
                $campaign = SmsCampaign::where('id', $campaign_id)->first();
            } else {
                // email
                $campaign = EmailCampaign::where('id', $campaign_id)->first();
            }
            if ($contact && $campaign) {
                if ($contact->user_id != auth()->user()->id || $campaign->user_id != auth()->user()->id) {
                    return response("", 403);
                }
                $found = CampaignExclude::where('campaign_id', $campaign->id)->where('contact_id', $contact->id)->first();
                if ($found && ($found->user_id == auth()->user()->id)) {
                    $found->delete();
                    $del++;
                }
            }
        }

        $response = [
            'total' => $del,
            'message' =>  "Contacts removed from the Campaign successfully"
        ];
        return response($response, 200);
    }

    public function clearCampaign(Request $request)
    {
        if (Hashids::decode($request->campaign) == []) {
            $campaign_id = Hashids::decode($request->campaign)[0];
            $found = CampaignExclude::where('campaign_id', $campaign_id)->where('type', $request->type)->get();
            if ($found) {
                foreach ($found as $row) {
                    $row->delete();
                }
            }
            $response = [
                'message' => "Contacts removed from the Campaign Excluded list successfully"
            ];
        } else {
            $response = [
                'message' => "Campaign not found"
            ];
        }
        return response($response, 200);
    }
}
