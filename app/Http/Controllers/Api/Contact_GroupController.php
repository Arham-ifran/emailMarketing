<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Contact;
use App\Models\Contact_group;
use Hashids;
use Illuminate\Http\Request;
use App\Models\User_log;

class Contact_GroupController extends Controller
{
    /**
     * Create a new Contact_group instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function addToGroup(Request $request)
    {
        $add = 0;
        $err = 403;
        for ($i = 0; $i < sizeof($request->contact); $i++) {
            $contact_id = Hashids::decode($request->contact[$i])[0];
            $group_id = Hashids::decode($request->group)[0];
            $contact = Contact::where('id', $contact_id)->first();
            $group = Group::where('id', $group_id)->first();

            if ($contact && $group) {
                if ($contact->user_id != auth()->user()->id || $group->user_id != auth()->user()->id) {
                    return response("", 403);
                }
                $find = Contact_group::where('deleted_at', null)->where('user_id', auth()->user()->id)->where('group_id', $group->id)->where('contact_id', $contact->id)->first();
                if (!$find) {
                    $data = ['contact_id' => $contact->id, 'group_id' => $group->id, 'user_id' => auth()->user()->id];
                    if (($contact->for_sms == 1 && $group->for_sms == 1) || ($contact->for_email == 1 && $group->for_email == 1)) {
                        // check
                        $cg = Contact_group::create($data);
                        $add++;
                        User_log::create([
                            'user_id' => auth()->user()->id,
                            'item_id' => $cg->id,
                            'module' => 6,
                            'log_type' => 5,
                        ]);
                    }
                }
            } else {
                $err = 404;
            }
        }

        $response = [
            'message' => $add . "Contacts added to the Group successfully"
        ];
        return response($response, 201);
    }

    public function removeContactFromGroup(Request $request)
    {
        $del = 0;
        $err = 403;

        for ($i = 0; $i < sizeof($request->contact); $i++) {
            $contact_id = Hashids::decode($request->contact[$i])[0];
            $group_id = Hashids::decode($request->group)[0];
            $contact = Contact::where('id', $contact_id)->first();
            $group = Group::where('id', $group_id)->first();
            if ($contact && $group) {
                if ($contact->user_id != auth()->user()->id || $group->user_id != auth()->user()->id) {
                    return response("", 403);
                }
                $found = Contact_group::where('group_id', $group->id)->where('contact_id', $contact->id)->first();
                if ($found && ($found->user_id == auth()->user()->id)) {
                    $found->delete();
                    $del++;
                    User_log::create([
                        'user_id' => auth()->user()->id,
                        'item_id' => $found->id,
                        'log_type' => 5,
                        'module' => 8,
                    ]);
                }
            } else {
                $err = 404;
            }
        }

        $response = [
            'message' => $del . "Contacts removed from the Group successfully"
        ];
        return response($response, 201);
    }
}
