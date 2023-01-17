<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CampaignContact;
use App\Models\SmsCampaignGroup;
use App\Models\SmsCampaign;
use App\Models\Contact;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;
use Hashids;

class CampaignContactController extends Controller
{
    /**
     * Create a new CampaignContact instance.
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
            $campaign_id = Hashids::decode($request->campaign_id)[0];
            $contact = Contact::where('id', $contact_id)->first();
            if ($contact) {
                $data = ['contact_id' => $contact->id, 'campaign_id' => $campaign_id, 'user_id' => auth()->user()->id, 'type' => $request->type];
                $find = CampaignContact::where('user_id', auth()->user()->id)->where('campaign_id', $campaign_id)->where('contact_id', $contact->id)->first();
                if (!$find)
                    CampaignContact::create($data);
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
            $campaign_id = Hashids::decode($request->campaign_id)[0];
            $contact = Contact::where('id', $contact_id)->first();
            if ($contact) {
                $found = CampaignContact::where('type', $request->type)->where('campaign_id', $campaign_id)->where('contact_id', $contact->id)->first();
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
}
