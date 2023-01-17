<?php

namespace App\Http\Controllers\Api;

use App\CustomClasses\TranslationHandler;
use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\User_log;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\GroupResource;
use App\Http\Resources\ContactResource;
use App\Models\EmailCampaign;
use App\Models\SmsCampaign;
use Hashids;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    /**
     * Create a new Group instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAll()
    {
        $id = auth()->user()->id;
        $groups = group::where('user_id', $id)->orderBy('created_at', 'DESC')->get();

        return GroupResource::collection($groups)
            ->additional([
                'message' => 'Groups',
                'status' => 1,
            ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $id = auth()->user()->id;
        if ($request->input('name')) {
            if ($request->input('created')) {
                if ($request->input('updated')) {
                    $groups = group::where('user_id', $id)->where('name', 'like', '%' . $request->input('name') . '%')->where('created_at', 'like', '%' . $request->created . '%')->where('updated_at', 'like', '%' . $request->updated . '%')->paginate(10);
                } else {
                    $groups = group::where('user_id', $id)->where('name', 'like', '%' . $request->input('name') . '%')->where('created_at', 'like', '%' . $request->created . '%')->paginate(10);
                }
            } elseif ($request->input('updated')) {
                $groups = group::where('user_id', $id)->where('name', 'like', '%' . $request->input('name') . '%')->where('updated_at', 'like', '%' . $request->updated . '%')->paginate(10);
            } else {
                $groups = group::where('user_id', $id)->where('name', 'like', '%' . $request->input('name') . '%')->paginate(10);
            }
        } elseif ($request->input('created')) {
            if ($request->input('updated')) {
                $groups = group::where('user_id', $id)->where('created_at', 'like', '%' . $request->created . '%')->where('updated_at', 'like', '%' . $request->updated . '%')->paginate(10);
            } else {
                $groups = group::where('user_id', $id)->where('created_at', 'like', '%' . $request->created . '%')->paginate(10);
            }
        } elseif ($request->input('updated')) {
            $groups = group::where('user_id', $id)->where('updated_at', 'like', '%' . $request->updated . '%')->paginate(10);
        } else {
            $groups = group::where('user_id', $id)->orderBy('created_at', 'DESC')->paginate(10);
        }
        return GroupResource::collection($groups)
            ->additional([
                'message' => 'Groups',
                'status' => 1,
            ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // 'user_id', 'name', 'group_type', 'sender_name', 'sender_email', 'double_opt_in', 'status'
        $messages = [
            'required' => TranslationHandler::getTranslation($request->lang, 'required'),
            'string' => TranslationHandler::getTranslation($request->lang, 'required'),
            'name.*.max' => TranslationHandler::getTranslation($request->lang, 'max_250'),
            'description.*.max' => TranslationHandler::getTranslation($request->lang, 'max_250'),
            'sender_name.max' => TranslationHandler::getTranslation($request->lang, 'max_65'),
            'email.max' => TranslationHandler::getTranslation($request->lang, 'max_65'),
            'email.*.regex' => TranslationHandler::getTranslation($request->lang, 'valid_email'),
            'number.*.required' => TranslationHandler::getTranslation($request->lang, 'required'),
            'number.*.regex' => TranslationHandler::getTranslation($request->lang, 'number_regex'),
            'number.*.max' => TranslationHandler::getTranslation($request->lang, 'number_invalid'),
        ];
        $data = $request->validate([
            'name' => ['required', 'string', 'max:250'],
            'description' => ['required', 'string', 'max:250'],
            'sender_name' => ['string', 'max:65'],
            'sender_email' => ['string', 'regex:/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/', 'max:65'],
            'double_opt_in' => ['required', 'boolean'],
            'for_sms' => ['required', 'boolean'],
            'for_email' => ['required', 'boolean'],
        ], $messages);

        if (!$request->for_sms && !$request->for_email) {
            // if both are unchecked
            return response(['message' => TranslationHandler::getTranslation($request->lang, 'invalid_data'), 'errors' => ['for' => [TranslationHandler::getTranslation($request->lang, "please_select_for_email_or_sms")]],], 422);
        }

        $final = array_merge($request->all(), ['user_id' => Auth()->user()->id]);

        $id = "";
        if (isset($request->id)) {
            $id = Hashids::decode($request->id)[0];
        }
        $group = group::updateOrCreate(
            [
                'id' => $id
            ],
            $final
        );
        User_log::create([
            'user_id' => auth()->user()->id,
            'item_id' => $group->id,
            'log_type' => 2,
            'module' => 6,
        ]);

        return GroupResource::collection([$group])[0]
            ->additional([
                'message' => 'Group',
                'status' => 1,
            ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $id = Hashids::decode($id)[0];
        $group = group::where('id', $id)->first();

        if ($group && $group->user_id == auth()->user()->id) {
            return GroupResource::collection([$group])[0]
                ->additional([
                    'message' => 'Group',
                    'status' => 1,
                ]);
        }
        return response("", 401);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $messages = [
            'required' => TranslationHandler::getTranslation($request->lang, 'required'),
            'string' => TranslationHandler::getTranslation($request->lang, 'required'),
            'name.*.max' => TranslationHandler::getTranslation($request->lang, 'max_250'),
            'description.*.max' => TranslationHandler::getTranslation($request->lang, 'max_250'),
            'sender_name.max' => TranslationHandler::getTranslation($request->lang, 'max_65'),
            'email.max' => TranslationHandler::getTranslation($request->lang, 'max_65'),
            'email.*.regex' => TranslationHandler::getTranslation($request->lang, 'valid_email'),
            'number.*.required' => TranslationHandler::getTranslation($request->lang, 'required'),
            'number.*.regex' => TranslationHandler::getTranslation($request->lang, 'number_regex'),
            'number.*.max' => TranslationHandler::getTranslation($request->lang, 'number_invalid'),
        ];
        $data = $request->validate([
            'name' =>  ['string', 'max:250'],
            'description' => ['string', 'max:250'],
            'sender_name' => ['string', 'max:65'],
            'sender_email' => ['string', 'regex:/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/', 'max:65'],
            'double_opt_in' => ['required'],
            'for_sms' => ['required'],
            'for_email' => ['required'],
        ], $messages);

        if (!$request->for_sms && !$request->for_email) {
            // if both are unchecked
            return response(['message' => TranslationHandler::getTranslation($request->lang, 'invalid_data'), 'errors' => ['for' => [TranslationHandler::getTranslation($request->lang, "please_select_for_email_or_sms")]],], 422);
        }

        $id = Hashids::decode($id)[0];


        $group = group::where('id', $id)->first();
        if ($group && $group->user_id == auth()->user()->id) {
            $group->update($request->all());
            User_log::create([
                'user_id' => auth()->user()->id,
                'item_id' => $group->id,
                'log_type' => 2,
                'module' => 7,
            ]);
            return GroupResource::collection([$group])
                ->additional([
                    'message' => 'Group',
                    'status' => 1,
                ]);
        }
        return response("", 401);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $id = Hashids::decode($id)[0];
        $found = group::where('id', $id)->first();
        if ($found && $found->user_id == auth()->user()->id) {
            $camp1 = EmailCampaign::whereJsonContains('group_ids', $found->id)->where('status', '!=', 2)->first();
            $camp2 = SmsCampaign::whereJsonContains('group_ids', $found->id)->where('status', '!=', 1)->first();
            if (!($camp2) && !($camp1)) {
                $found->delete();
                User_log::create([
                    'user_id' => auth()->user()->id,
                    'item_id' => $found->id,
                    'log_type' => 1,
                    'module' => 8,
                ]);
                $response = [
                    'message' => TranslationHandler::getTranslation($request->lang, 'group_deleted')
                ];

                return response($response, 201);
            }
            return response(TranslationHandler::getTranslation($request->lang, 'group_in_use'), 409);
        }
        return response("Group not found", 401);
    }

    public function activate($id)
    {
        $id = Hashids::decode($id)[0];
        $found = group::where('id', $id)->first();
        if ($found && $found->user_id == auth()->user()->id) {
            $found->update(['status' => 'active']);
            $response = [
                'message' => "group is activated successfully"
            ];

            return response($response, 201);
        }
        return response("", 401);
    }

    public function deactivate($id)
    {
        $id = Hashids::decode($id)[0];
        $found = group::where('id', $id)->first();
        if ($found && $found->user_id == auth()->user()->id) {
            $found->update(['status' => 'inactive']);
            $response = [
                'message' => "group is deactivated successfully"
            ];

            return response($response, 201);
        }
        return response("", 401);
    }

    public function contacts($id)
    {
        $id = Hashids::decode($id)[0];
        $contacts = Group::where('id', $id)->with('contacts')->first();
        if ($contacts) {
            $contacts = $contacts->contacts;
            return ContactResource::collection($contacts)
                ->additional([
                    'message' => 'Contacts',
                    'status' => 1,
                ]);
        }
    }

    public function groupsInfo()
    {
        $id = Auth()->user()->id;
        $myDate = date("Y-m-d", strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . "-1 day"));
        $groups = Group::where('user_id', $id)->get();
        $new_time = Group::where('user_id', $id)->latest()->first();
        if ($new_time) {
            $new_time = $new_time->created_at;
        } else {
            $new_time = 0;
        }
        $new_groups = $groups->where('created_at', '>=', $myDate)->count();
        $existing_groups = $groups->where('created_at', '>=', $myDate)->count();
        $total_groups = $groups->count();
        $deleted_groups = Group::onlyTrashed()->where('user_id', $id)->count();

        $response = [
            'existing' => $existing_groups,
            'deleted' => $deleted_groups,
            'total' => $total_groups,
            'new' => $new_groups,
            'message' => "Groups details fetched"
        ];

        return response($response, 201);
    }
}
