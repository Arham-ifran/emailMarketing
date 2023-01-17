<?php

namespace App\Http\Controllers\Api;

use App\CustomClasses\TranslationHandler;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SmsCampaignGroup;
use App\Models\SmsCampaign;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;

class SmsCampaignGroupController extends Controller
{
    /**
     * Create a new SmsCampaignGroup instance.
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
        $err = 401;
        for ($i = 0; $i < sizeof($request->group); $i++) {
            $group = Group::where('id', $request->group[$i])->first();
            $SmsCampaign = SmsCampaign::where('id', $request->campaign)->first();
            // dd($group, $SmsCampaign);

            if ($group && $SmsCampaign) {
                if ($group->user_id != auth()->user()->id || $SmsCampaign->user_id != auth()->user()->id) {
                    return response("", 401);
                }
                $data = ['group_id' => $group->id, 'sms_campaign_id' => $SmsCampaign->id, 'user_id' => auth()->user()->id];
                SmsCampaignGroup::create($data);
                $add++;
            } else {
                $err = 404;
            }
        }
        if ($add) {
            $response = [
                'message' => $add . TranslationHandler::getTranslation($request->lang, 'group_added_to_campaign')
            ];
            return response($response, 201);
        } else {
            $response = [
                'message' => TranslationHandler::getTranslation($request->lang, 'error_group_added_to_campaign')
            ];
            return response($response, $err);
        }
    }

    public function removeFromCampaign(Request $request)
    {
        $del = 0;
        $err = 401;

        for ($i = 0; $i < sizeof($request->group); $i++) {
            $group = Group::where('id', $request->group[$i])->first();
            $SmsCampaign = SmsCampaign::where('id', $request->campaign)->first();
            if ($group && $SmsCampaign) {
                if ($group->user_id != auth()->user()->id || $SmsCampaign->user_id != auth()->user()->id) {
                    return response("", 401);
                }
                $found = SmsCampaignGroup::where('sms_campaign_id', $SmsCampaign->id)->where('group_id', $group->id)->first();
                if ($found && ($found->user_id == auth()->user()->id)) {
                    $found->delete();
                    $del++;
                }
            } else {
                $err = 404;
            }
        }

        if ($del) {
            $response = [
                'message' => $del . TranslationHandler::getTranslation($request->lang, 'required')
            ];
            return response($response, 201);
        } else {
            $response = [
                'message' => TranslationHandler::getTranslation($request->lang, 'error_group_removed_from_campaign')
            ];
            return response($response, $err);
        }
    }
}
