<?php

namespace App\Http\Controllers\RestApi;

use App\CustomClasses\TranslationHandler;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MailController;
use Hashids;
use App\Http\Resources\RestResources\ECampaignResource;
use App\Http\Resources\RestResources\CampaignHistoryResource;
use App\Http\Resources\RestResources\ContactResource;
use App\Http\Resources\RestResources\GroupResource;
use App\Http\Resources\RestResources\SmsCampaignResource;
use App\Http\Resources\RestResources\TemplateResource;
use App\Jobs\EmailCampaignJob;
use App\Jobs\MessageCampaignJob;
use App\Jobs\SplitTestingJob;
use App\Models\Admin\Package;
use App\Models\Admin\PackageLinkFeature;
use App\Models\Admin\PackageSubscription;
use App\Models\CampaignContact;
use App\Models\CampaignExclude;
use App\Models\CampaignHistory;
use App\Models\Contact;
use App\Models\Contact_group;
use App\Models\EmailCampaign;
use App\Models\EmailCampaignClick;
use App\Models\EmailCampaignLogs;
use App\Models\EmailCampaignOpen;
use App\Models\Group;
use App\Models\Notification;
use App\Models\SmsCampaign;
use App\Models\EmailCampaignTemplate;
use App\Models\EmailSendingLog;
use App\Models\PayAsYouGoPayments;
use App\Models\SmsCampaignRecursion;
use App\Models\SmsCampaignSchedule;
use App\Models\SplitTestSubject;
use App\Models\User_log;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Illuminate\Support\Str;

class ApiController extends Controller
{
    // Global var
    private $source = 2;

    public function __construct()
    {
        $this->middleware('verifyToken');
    }

    // private function to decode incoming ids
    private function decodeId($id)
    {
        $arr = Hashids::decode($id);
        $decoded = NULL;
        if (count($arr)) {
            $decoded = $arr[0];
        }
        return $decoded;
    }
    // private function to encode incoming ids
    private function encodeId($id)
    {
        return Hashids::encode($id);
    }

    // =================================== //
    // ======== CONTACT FUNCTIONS ======== //
    // =================================== //

    /**
     * Display a listing of the contact.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function contactIndex(Request $request)
    {
        $user = $request->get('user');
        $contacts = Contact::where('user_id', $user->id)->paginate(10);
        return ContactResource::collection($contacts)->additional([
            'total_contacts' => $contacts->count(), 'message' =>  'Contacts Fetched',
            "messageType" => "success", 'status' => 1
        ]);
    }

    /**
     * Store newly created contacts in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function contactStore(Request $request)
    {
        $user = $request->get('user');
        // validation
        // ----------
        $messages = [
            'required' => 'This field is required.',
            'string' => 'This field is required!',
            'email.string' => 'This field is required!',
            'first_name.*.max' => 'Maximum of 35 characters allowed.',
            'last_name.*.max' => 'Maximum of 35 characters allowed.',
            'email.max' => 'Maximum of 65 characters allowed.',
            'email.*.regex' => 'This field must contain a valid email address.',
            'number.*.required' => 'This field is required.',
            'number.*.regex' => 'The number must contain ( +countrycode phone number ).',
            'number.*.max' => 'Invalid number.',
        ];
        $validation_rules = [
            'first_name' => ['required', 'array'],
            'last_name' => ['required', 'array'],
            'for_email' => ['required', 'array'],
            'for_sms' => ['required', 'array'],

            'first_name.*' => ['required', 'string', 'max:35'],
            'last_name.*' => ['required', 'string', 'max:35'],
            'for_email.*' => ['boolean'],
            'for_sms.*' => ['boolean'],
        ];

        $validator = \Validator::make($request->all(), $validation_rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'errors' =>  $validator->messages()->get('*')
            ]);
            exit;
        }

        $should_have_emails = count(array_filter($request->for_email, function ($a) {
            return $a == 1;
        }));
        $should_have_sms = count(array_filter($request->for_sms, function ($a) {
            return $a == 1;
        }));
        if ($should_have_emails + $should_have_sms  == 0) {
            return response(['message' => "Please select for Email or SMS"], 422);
        }
        $empty_emails = count(array_filter(array_unique($request->email), function ($a) {
            return $a == '' || $a == null;
        }));
        $empty_sms = count(array_filter(array_unique($request->number), function ($a) {
            return $a == '' || $a == null;
        }));
        if ((count(array_unique($request->number)) - $empty_sms != $should_have_sms || sizeof($request->first_name) != sizeof($request->number) || sizeof($request->first_name) != sizeof($request->for_sms)) || (count(array_unique($request->email)) - $empty_emails != $should_have_emails || sizeof($request->first_name) != sizeof($request->email) || sizeof($request->first_name) != sizeof($request->for_email))) {
            // number or email is repeated somewhere
            return response(['message' => "The given data was invalid.", 'errors' => ['error_message' => ['Values are repeating or missing. please make sure each contact has unique email and number.']],], 422);
        }

        // checking user package limits
        // if ($user->package_id && $user->package_id != 9) {
        //     $package = Package::where('id', $user->package_id)->where('status', 1)->orderBy('monthly_price')->first();
        //     if ($package) {
        //         $hasContacts = Contact::where('user_id', $user->id)->count();
        //         $foundfeature = PackageLinkFeature::where('package_id', $package->id)->where('feature_id', 3)->first();
        //         if ($foundfeature) {
        //             $limit = $foundfeature->count;
        //             $addingmore = sizeof($request->first_name);
        //             $addingContacts = $addingmore + $hasContacts;
        //             if ($addingContacts > $limit) {
        //                 return response(['message' => "Cannot add contacts. Adding would exceed your package limits.", 'code' => 0, 'errors' => ['error_message' => ["Limit Exceeded"]],], 422);
        //             }
        //         }
        //     }
        // }
        // checking user package limits end

        for ($i = 0; $i < sizeof($request->first_name); $i++) {
            if ($request->for_sms[$i]) {
                // check sms fields
                $validation_rules = [
                    'number' => ['required', 'array',],
                    'number.' . $i => ['required', 'regex:/([1-9]{2})([0-9]{10})/', 'max:13'],
                ];
                $validator = \Validator::make($request->all(), $validation_rules, $messages);

                if ($validator->fails()) {
                    return response()->json([
                        'status' => 0,
                        'errors' =>  $validator->messages()->get('*')
                    ]);
                    exit;
                }
                $find = Contact::where('user_id', $user->id)->where('number', $request->number[$i])->first();
                if ($find) {
                    return response(['message' => "The given data was invalid", 'errors' => ['number.' . $i => ['Number already exists']],], 422);
                }
            }
            if ($request->for_email[$i]) {
                // check email fields
                $validation_rules = [
                    'email' => ['required', 'array',],
                    'email.' . $i => ['required', 'string', 'regex:/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/', 'max:65'],
                ];
                $validator = \Validator::make($request->all(), $validation_rules, $messages);

                if ($validator->fails()) {
                    return response()->json([
                        'status' => 0,
                        'errors' =>  $validator->messages()->get('*')
                    ]);
                    exit;
                }
                $find = Contact::where('user_id', $user->id)->where('email', $request->email[$i])->first();
                if ($find) {
                    return response(['message' => "The given data was invalid", 'errors' => ['email.' . $i => ['Email already exists']],], 422);
                }
            }
            if (!$request->for_sms[$i] && !$request->for_email[$i]) {
                // if both are unchecked
                return response(['message' => "The given data was invalid", 'errors' => ['for.' . $i => ['Please select for Email or SMS']],], 422);
            }
        }

        if ($request->group_id) {
            if (Hashids::decode($request->group_id))
                $list = Group::where('user_id', $user->id)->where('id', Hashids::decode($request->group_id)[0])->first();
            else
                return response()->json(['errors' =>  ['group' => ['Group not found!']], "messageType" => "error", 'status' => 0], 404);
            if (!$list)
                return response()->json(['errors' =>  ['group' => ['Group not found!']], "messageType" => "error", 'status' => 0], 404);
        }

        // for creating if validated.
        // --------------------------
        $added_to_group = NULL;
        $contacts = [];
        for ($i = 0; $i < sizeof($request->first_name); $i++) {
            $final = array_merge(['request_source' => 2, 'user_id' => $user->id, 'first_name' => $request->first_name[$i], 'last_name' => $request->last_name[$i], 'email' => $request->email[$i], 'country_code' => 00, 'number' => $request->number[$i], 'for_sms' => $request->for_sms[$i], 'for_email' => $request->for_email[$i]]);
            $con = Contact::create($final);
            $hasContacts = Contact::where('user_id', $user->id)->count();
            if ($user->package && $user->package_id == 9) {
                // $contacts_span1_start = PackageSettingsValue(7, 'start_range');
                // $contacts_span1_end = PackageSettingsValue(7, 'end_range');
                // $contacts_span1_price = PackageSettingsValue(7, 'price_without_vat');
                // $contacts_span2_start = PackageSettingsValue(8, 'start_range');
                // $contacts_span2_end = PackageSettingsValue(8, 'end_range');
                // $contacts_span2_price = PackageSettingsValue(8, 'price_without_vat');
                // $contacts_span3_start = PackageSettingsValue(9, 'start_range');
                // $contacts_span3_price = PackageSettingsValue(9, 'price_without_vat');
                // $user->subscription->contacts_paying_for += 1;
                // if ($hasContacts >= $contacts_span1_start && $hasContacts <= $contacts_span1_end) {
                //     $user->subscription->contacts_to_pay += $contacts_span1_price;
                // } else if ($hasContacts >= $contacts_span2_start && $hasContacts <= $contacts_span2_end) {
                //     $user->subscription->contacts_to_pay += $contacts_span2_price;
                // } else if ($hasContacts >= $contacts_span3_start) {
                //     $user->subscription->contacts_to_pay += $contacts_span3_price;
                // }
                // $user->subscription->save();
                // check payment Relief
                $last_payment = PayAsYouGoPayments::where('package_subscription_id', $user->subscription->id)->where('status', '!=', 1)->first();
                if ($last_payment && isset($last_payment->timestamp)) {
                    $last_payment = $last_payment->timestamp;
                    $payment_relief_days = (int)settingValue('payment_relief_days');
                    $releif_timestamp = Carbon::now('UTC')->subDays($payment_relief_days)->timestamp;
                    if ($last_payment < $releif_timestamp) {
                        return response(['message' => "You have not paid for your data. We have no choice but to Disable your account. Please contact support for reactivation.", 'code' => 1, 'errors' => ['error_message' => ['Package not paid for!']],], 422);
                    }
                }
            }
            array_push($contacts, $con);
            $add = 0;
            // add the contact to default group or selected list
            // --------------------------------
            if ($request->group_id && Hashids::decode($request->group_id)) {
                $list = Group::where('user_id', $user->id)->where('id', Hashids::decode($request->group_id)[0])->first();
                if ($list && (($con->for_sms == 1 && $list->for_sms == 1) || ($con->for_email == 1 && $list->for_email == 1))) {
                    $find = Contact_group::where('deleted_at', null)->where('user_id', $user->id)->where('group_id', $list->id)->where('contact_id', $con->id)->first();
                    if ($find) {
                        return response("Already in group", 304);
                    }
                    $added_to_group = $list->id;
                    $data = ['contact_id' => $con->id, 'group_id' => $list->id, 'user_id' => $user->id];
                    $cg = Contact_group::create($data);
                    User_log::create([
                        'user_id' => $user->id,
                        'item_id' => $cg->id,
                        'log_type' => 1,
                        'module' => 6,
                    ]);
                } else
                    $add = 1;
            } else
                $add = 1;

            // $default = Group::where('user_id', $user->id)->where('name', 'default')->first();
            // if ($add && $default) {
            //     $data = ['contact_id' => $con->id, 'group_id' => $default->id, 'user_id' => $user->id];
            //     $cg = Contact_group::create($data);
            //     User_log::create([
            //         'user_id' => $user->id,
            //         'item_id' => $cg->id,
            //         'log_type' => 1,
            //         'module' => 6,
            //     ]);
            // }
        }

        $ids = [];
        foreach ($contacts as $contact) {
            array_push($ids, $this->encodeId($contact->id));
        }

        if ($added_to_group == NULL) {
            if (count($ids)) {
                return response()->json([
                    '_id' => $ids,
                    'message' => 'Contacts added successfully', "messageType" => "success", 'status' => 1
                ]);
            } else {
                return response()->json(['errors' =>  ['contact' => ['No contacts created. Please check your data validity!']], "messageType" => "error", 'status' => 0], 404);
            }
        }

        if (count($ids)) {
            return response()->json([
                '_id' => $ids,
                'added_to_group' => $this->encodeId($added_to_group), 'message' => 'Contacts added successfully', "messageType" => "success", 'status' => 1
            ]);
        } else {
            return response()->json(['errors' =>  ['contact' => ['No contacts created. Please check your data validity!']], "messageType" => "error", 'status' => 0], 404);
        }
    }

    /**
     * Display the specified contact.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function contactShow(Request $request, $id)
    {
        $user = $request->get('user');
        //
        $id = $this->decodeId($id);
        $contact = Contact::where('id', $id)->first();
        if ($contact && $contact->user_id == $user->id)
            return ContactResource::collection([$contact])[0]->additional([
                'message' => 'Contact Fetched', "messageType" => "success", 'status' => 1
            ]);
        return response()->json(['errors' =>  ['contact' => ['Contact not found!']], "messageType" => "error", 'status' => 0], 404);
    }

    /**
     * Update the specified contact in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function contactUpdate(Request $request)
    {
        $user = $request->get('user');
        $messages = [
            'required' => 'This field is required.',
            'string' => 'This field is required!',
            'email.string' => 'This field is required!',
            'first_name.*.max' => 'Maximum of 35 characters allowed.',
            'last_name.*.max' => 'Maximum of 35 characters allowed.',
            'email.max' => 'Maximum of 65 characters allowed.',
            'email.*.email' => 'This field must contain a valid email address.',
            'number.*.required' => 'This field is required.',
            'number.*.regex' => 'The number must contain ( +countrycode phone number ).',
            'number.*.max' => 'Invalid number.',
        ];
        $validator = \Validator::make($request->all(), [
            'id' => ['required', 'array'],
            'first_name' => ['required', 'array'],
            'last_name' => ['required', 'array'],
            'number' => ['array'],
            'email' => ['array'],
            'for_email' => ['array'],
            'for_sms' => ['array'],

            // 'id.*' => [],
            'first_name.*' => ['string', 'max:35'],
            'last_name.*' => ['string', 'max:35'],
            'number.*' => ['regex:/([1-9]{2})(\d{10})/', 'max:13'],
            'email.*' => ['string', 'email', 'max:65'],
            'for_email.*' => ['boolean'],
            'for_sms.*' => ['boolean'],
        ], $messages);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json(['errors' => $errors, "messageType" => "error", 'status' => 0], 422);
        }
        $data = $request->all();

        $should_have_emails = count(array_filter($request->for_email, function ($a) {
            return $a == 1;
        }));
        $should_have_sms = count(array_filter($request->for_sms, function ($a) {
            return $a == 1;
        }));
        $empty_emails = count(array_filter(array_unique($request->email), function ($a) {
            return $a == '' || $a == null;
        }));
        $empty_sms = count(array_filter(array_unique($request->number), function ($a) {
            return $a == '' || $a == null;
        }));
        if ((count(array_unique($request->number)) - $empty_sms != $should_have_sms || sizeof($request->first_name) != sizeof($request->number) || sizeof($request->first_name) != sizeof($request->for_sms)) || (count(array_unique($request->email)) - $empty_emails != $should_have_emails || sizeof($request->first_name) != sizeof($request->email) || sizeof($request->first_name) != sizeof($request->for_email))) {
            // number or email is repeated somewhere
            return response(['message' => "The given data was invalid.", 'errors' => ['error_message' => ['Values are repeating. please make sure each contact has unique email and number.']],], 422);
        }

        if ($request->group_id) {
            if (Hashids::decode($request->group_id))
                $list = Group::where('user_id', $user->id)->where('id', Hashids::decode($request->group_id)[0])->first();
            else
                return response()->json(['errors' =>  ['group' => ['Group not found!']], "messageType" => "error", 'status' => 0], 404);
            if (!$list)
                return response()->json(['errors' =>  ['group' => ['Group not found!']], "messageType" => "error", 'status' => 0], 404);
        }

        $added_to_group = NULL;
        $contacts = [];
        for ($i = 0; $i < sizeof($request->id); $i++) {
            $final = array_merge(['user_id' => $user->id, 'first_name' => $request->first_name[$i], 'last_name' => $request->last_name[$i], 'email' => $request->email[$i], 'country_code' => 00, 'number' => $request->number[$i], 'for_sms' => $request->for_sms[$i], 'for_email' => $request->for_email[$i]]);
            if (Hashids::decode($request->id[$i])) {
                $id = Hashids::decode($request->id[$i])[0];
                $contact = Contact::where('id', $id)->first();
                if ($contact) {
                    if ($request->for_sms[$i]) {
                        // check sms fields
                        $validation_rules = [
                            'number' => ['required', 'array',],
                            'number.' . $i => ['required', 'regex:/([1-9]{2})(\d{10})/'],
                        ];
                        $validator = \Validator::make($request->all(), $validation_rules, $messages);

                        if ($validator->fails()) {
                            return response()->json([
                                'status' => 0,
                                'errors' =>  $validator->messages()->get('*')
                            ]);
                            exit;
                        }
                        $find = Contact::where('user_id', $user->id)->where('number', $request->number[$i])->first();
                        if ($find) {
                            return response(['message' => "The given data was invalid", 'errors' => ['number.' . $i => ['Number already exists']],], 422);
                        }
                    }
                    if ($request->for_email[$i]) {
                        // check email fields
                        $validation_rules = [
                            'email' => ['required', 'array',],
                            'email.' . $i => ['required', 'string', 'email', 'max:65'],
                        ];
                        $validator = \Validator::make($request->all(), $validation_rules, $messages);

                        if ($validator->fails()) {
                            return response()->json([
                                'status' => 0,
                                'errors' =>  $validator->messages()->get('*')
                            ]);
                            exit;
                        }
                        $find = Contact::where('user_id', $user->id)->where('email', $request->email[$i])->first();
                        if ($find && $find->id != $contact->id) {
                            return response(['message' => "The given data was invalid", 'errors' => ['email.' . $i => ['Email already exists']],], 422);
                        }
                    }
                    if (!$request->for_sms[$i] && !$request->for_email[$i]) {
                        // if both are unchecked
                        return response(['message' => "The given data was invalid", 'errors' => ['for.' . $i => ['Please select for Email or SMS']],], 422);
                    }

                    $contact->update($final);
                    array_push($contacts, $contact);
                    User_log::create([
                        'user_id' => $user->id,
                        'item_id' => $contact->id,
                        'log_type' => 1,
                        'module' => 7,
                    ]);


                    // add the contact to a selected list
                    // ----------------------------------
                    if ($request->group_id) {
                        $list = Group::where('user_id', $user->id)->where('id', Hashids::decode($request->group_id)[0])->first();
                        if ($list && (($contact->for_sms == 1 && $list->for_sms == 1) || ($contact->for_email == 1 && $list->for_email == 1))) {
                            $find = Contact_group::where('deleted_at', null)->where('user_id', $user->id)->where('group_id', $list->id)->where('contact_id', $contact->id)->first();
                            if (!$find) {
                                $added_to_group = $list->id;
                                $data = ['contact_id' => $contact->id, 'group_id' => $list->id, 'user_id' => $user->id];
                                $cg = Contact_group::create($data);
                                User_log::create([
                                    'user_id' => $user->id,
                                    'item_id' => $cg->id,
                                    'log_type' => 1,
                                    'module' => 6,
                                ]);
                            }
                        }
                    }
                }
            }
        }
        if ($added_to_group == NULL) {
            if (count($contacts)) {
                return ContactResource::collection($contacts)->additional([
                    'message' => 'Contacts Edited', "messageType" => "success", 'status' => 1
                ]);
            } else {
                return response()->json(['errors' =>  ['contact' => ['No contacts edited. Please check your data validity!']], "messageType" => "error", 'status' => 0], 404);
            }
        }
        if (count($contacts)) {
            return ContactResource::collection($contacts)->additional([
                'added_to_group' => $this->encodeId($added_to_group), 'message' => 'Contacts Edited', "messageType" => "success", 'status' => 1
            ]);
        } else {
            return response()->json(['errors' =>  ['contact' => ['No contacts eedited. Please check your data validity!']], "messageType" => "error", 'status' => 0], 404);
        }
    }

    /**
     * Remove the specified contact from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function contactDestroy(Request $request)
    {
        $user = $request->get('user');
        $validator = \Validator::make($request->all(), [
            'id' => ['required', 'array'],
            'id.*' => ['required', 'string'],
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json(['errors' => $errors, "messageType" => "error", 'status' => 0], 422);
        }

        $del = 0;
        for ($i = 0; $i < sizeof($request->id); $i++) {
            $id = $this->decodeId($request->id[$i]);
            $contact = Contact::where('id', $id)->first();
            if ($contact) {
                $found = CampaignContact::where('contact_id', $contact->id);
                $found2 = clone $found;
                $sms_ids = $found->where('type', 1)->pluck('campaign_id')->toArray();
                $email_ids = $found2->where('type', 2)->pluck('campaign_id')->toArray();
                $groups = $contact->groups()->get();
                $ingroup = false;
                if ($groups->count()) {
                    foreach ($groups as $grp) {
                        $emailhas = EmailCampaign::whereJsonContains('group_ids', $grp->id)->where('status', '!=', 2)->first();
                        $smshas = SmsCampaign::whereJsonContains('group_ids', $grp->id)->where('status', '!=', 1)->first();
                        if ($emailhas || $smshas) {
                            $ingroup = true;
                        }
                    }
                }
                if (!$ingroup && !(EmailCampaign::whereIn('id', $email_ids)->where('status', '!=', 2)->first() || SmsCampaign::whereIn('id', $sms_ids)->where('status', '!=', 1)->first())) {
                    $contact->delete();
                    $del++;
                    User_log::create([
                        'user_id' => $user->id,
                        'item_id' => $contact->id,
                        'log_type' => 1,
                        'module' => 8,
                    ]);
                }
            }
        }
        if ($del) {
            return response()->json(['message' => "Contacts Deleted success", "messageType" => "success", 'status' => 1], 200);
        }
        return response()->json(['errors' =>  ['contact' => ['Contacts not found or cannot be deleted!']], "messageType" => "error", 'status' => 0], 404);
    }

    /**
     * Verify Subscription of the specified contact from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function subscribe($id)
    {
        $id = $this->decodeId($id);
        $contact = Contact::where('id', $id)->first();
        if ($contact) {
            $contact->update(['subscribed' => 1, 'confirmed_at' => now()]);
            $response = [
                'message' => "Contact subscribed successfully", "messageType" => "success", 'status' => 1
            ];
            // notifying the user of subscription
            Notification::create([
                'user_id' => $contact->user_id,
                'item_id' => $contact->id,
                'module' => 1,
                'notification_type' => 1,
                'notification_text' => $contact->first_name . " " . $contact->last_name . " has Subscribed.",
                'redirect_to' => "/contacts/" . $this->encodeId($contact->id),
            ]);
            User_log::create([
                'user_id' => $contact->user_id,
                'item_id' => $contact->id,
                'module' => 1,
                'log_type' => 1,
            ]);
            // SendMail::dispatch(User::where('id', $contact->user_id)->first()->email, 'Contact Subscribed', "Your contact " . $contact->first_name . " " . $contact->last_name . " has Subscribed.");


            return response()->json($response, 200);
        }
        return response()->json(['errors' =>  ['contact' => ['Contact not found!']], "messageType" => "error", 'status' => 0], 404);
    }

    /**
     * UnSsubscribe the specified contact from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function unsubscribe($id)
    {
        $id = $this->decodeId($id);
        $contact = Contact::where('id', $id)->first();
        if ($contact) {
            $contact->update(['subscribed' => 0, 'unsubscribed_at' => now()]);
            $response = [
                'message' => "Contact unsubscribed successfully", "messageType" => "success", 'status' => 1
            ];

            // notifying the user of subscription
            Notification::create([
                'user_id' => $contact->user_id,
                'item_id' => $contact->id,
                'module' => 2,
                'notification_type' => 1,
                'notification_text' => $contact->first_name . " " . $contact->last_name . " has unSubscribed.",
                'redirect_to' => "/contacts/" . $this->encodeId($contact->id),
            ]);
            User_log::create([
                'user_id' => $contact->user_id,
                'item_id' => $contact->id,
                'module' => 2,
                'log_type' => 1,
            ]);
            // SendMail::dispatch(User::where('id', $contact->user_id)->first()->email, 'Contact Unsubscribed', "Your contact " . $contact->first_name . " " . $contact->last_name . " has Unsubscribed.");

            return response()->json($response, 200);
        }
        return response()->json(['errors' =>  ['contact' => ['Contact not found!']], "messageType" => "error", 'status' => 0], 404);
    }

    // =================================== //
    // ======== GROUP FUNCTIONS ========== //
    // =================================== //

    /**
     * Display a listing of the group.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function groupIndex(Request $request)
    {
        $user = $request->get('user');
        $groups = Group::where('user_id', $user->id)->paginate(10);
        return GroupResource::collection($groups)->additional([
            'total_groups' => $groups->count(), 'message' => 'Groups Fetched',
            "messageType" => "success", 'status' => 1
        ]);
    }

    /**
     * Store a newly created group in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function groupStore(Request $request)
    {
        $user = $request->get('user');
        $messages = [
            'required' => 'This field is required.',
            'string' => 'This field is required!',
            'sender_email.string' => 'This field is required!',
            'first_name.*.max' => 'Maximum of 35 characters allowed.',
            'last_name.*.max' => 'Maximum of 35 characters allowed.',
            'sender_email.max' => 'Maximum of 65 characters allowed.',
            'sender_email.*.regex' => 'This field must contain a valid email address.',
            'number.*.required' => 'This field is required.',
            'number.*.regex' => 'The number must contain ( +countrycode phone number ).',
            'number.*.max' => 'Invalid number.',
        ];
        $validation_rules = [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
            'sender_name' => ['string', 'max:65'],
            'sender_email' => ['string', 'regex:/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/', 'max:65'],
            // 'double_opt_in' => ['required', 'boolean'],
            'for_sms' => ['boolean'],
            'for_email' => ['boolean'],
        ];
        $validator = \Validator::make($request->all(), $validation_rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'errors' =>  $validator->messages()->get('*')
            ]);
            exit;
        }

        if (!$request->for_sms && !$request->for_email) {
            // if both are unchecked
            return response(['message' => "The given data was invalid", 'errors' => ['for' => ['Please select for Email or SMS']],], 422);
        }

        $final = array_merge($request->all(), ['request_source' => 2, 'user_id' => $user->id]);

        $id = "";
        if (isset($request->id)) {
            $id = Hashids::decode($request->id);
            if (count($id))
                $id = $id[0];
        }
        $group = group::updateOrCreate(
            [
                'id' => $id
            ],
            $final
        );
        User_log::create([
            'user_id' => $user->id,
            'item_id' => $group->id,
            'log_type' => 2,
            'module' => 6,
        ]);

        return response()->json(['_id' => $this->encodeId($group->id), 'message' => 'Group Created', "messageType" => "success", 'status' => 1], 200);
    }

    /**
     * Display the specified group.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function groupShow(Request $request, $id)
    {
        $id = $this->decodeId($id);
        $user = $request->get('user');
        $group = group::where('id', $id)->first();

        if ($group && $group->user_id == $user->id) {
            return GroupResource::collection([$group])[0]->additional([
                'message' => 'Group Fetched', "messageType" => "success", 'status' => 1
            ]);
        }
        return response()->json(['errors' =>  ['group' => ['Group not found!']], "messageType" => "error", 'status' => 0], 404);
    }

    /**
     * Update the specified group in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function groupUpdate(Request $request, $id)
    {
        $user = $request->get('user');
        $messages = [
            'required' => 'This field is required.',
            'string' => 'This field is required!',
            'sender_email.string' => 'This field is required!',
            'first_name.*.max' => 'Maximum of 35 characters allowed.',
            'last_name.*.max' => 'Maximum of 35 characters allowed.',
            'sender_email.max' => 'Maximum of 65 characters allowed.',
            'sender_email.*.regex' => 'This field must contain a valid email address.',
            'number.*.required' => 'This field is required.',
            'number.*.regex' => 'The number must contain ( +countrycode phone number ).',
            'number.*.max' => 'Invalid number.',
        ];
        $validation_rules = [
            'name' =>  ['string', 'max:255'],
            'description' => ['string', 'max:255'],
            'sender_name' => ['string', 'max:255'],
            'sender_email' => ['string', 'regex:/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/', 'max:255'],
            // 'double_opt_in' => ['boolean'],
            'for_sms' => ['boolean'],
            'for_email' => ['boolean'],
        ];
        $validator = \Validator::make($request->all(), $validation_rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'errors' =>  $validator->messages()->get('*')
            ]);
            exit;
        }

        if (!$request->for_sms && !$request->for_email) {
            // if both are unchecked
            return response(['message' => "The given data was invalid", 'errors' => ['for' => ['Please select for Email or SMS']],], 422);
        }

        $id = Hashids::decode($id);

        if ($id) {
            $group = group::where('id', $id[0])->first();
            if ($group && $group->user_id == $user->id) {
                $group->update($request->all());
                User_log::create([
                    'user_id' => $user->id,
                    'item_id' => $group->id,
                    'log_type' => 2,
                    'module' => 7,
                ]);
                return GroupResource::collection([$group])[0]->additional([
                    'message' => 'Group Updated', "messageType" => "success", 'status' => 1
                ]);
            }
        }
        return response()->json(['errors' =>  ['group' => ['Group not found!']], "messageType" => "error", 'status' => 0], 404);
    }

    /**
     * Remove the specified group from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function groupDestroy(Request $request, $id)
    {
        $id = $this->decodeId($id);
        $user = $request->get('user');
        $found = group::where('id', $id)->first();
        if ($found && $found->user_id == $user->id) {
            $camp1 = EmailCampaign::whereJsonContains('group_ids', $found->id)->where('status', '!=', 2)->first();
            $camp2 = SmsCampaign::whereJsonContains('group_ids', $found->id)->where('status', '!=', 1)->first();
            if (!($camp2) && !($camp1)) {
                $found->delete();
                User_log::create([
                    'user_id' => $user->id,
                    'item_id' => $found->id,
                    'log_type' => 1,
                    'module' => 8,
                ]);
                return response()->json(['message' => "Group Deleted successfully", "messageType" => "success", 'status' => 1], 200);
            }
            return response()->json(['errors' =>  ['group' => ['Group in use, cannot be Deleted!']], "messageType" => "error", 'status' => 0], 404);
        }
        return response()->json(['errors' =>  ['group' => ['Group not found!']], "messageType" => "error", 'status' => 0], 404);
    }

    // ========================================== //
    // ======== SMS CAMPAIGN FUNCTIONS ========== //
    // ========================================== //

    /**
     * Display a listing of the sms.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function smsIndex(Request $request)
    {
        $user = $request->get('user');
        $sms = SmsCampaign::where('user_id', $user->id)->paginate(10);
        return SmsCampaignResource::collection($sms)->additional([
            'total_campaigns' => $sms->count(), 'message' => 'Campaigns Fetched',
            "messageType" => "success", 'status' => 1
        ]);
    }

    /**
     * Store a newly created sms in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function smsStore(Request $request)
    {
        $user = $request->get('user');

        $input = $request->all();
        $campaign_id = '';

        if (isset($input['campaign_id'])) {
            if (Hashids::decode($input['campaign_id']) == []) {
                return response()->json(['errors' =>  ['campaign' => ['campaign not found!']], "messageType" => "error", 'status' => 0], 404);
            }
            $campaign_id = Hashids::decode($input['campaign_id'])[0];
            $camp = SmsCampaign::where('id', $campaign_id)->whereIn('status', [3, 4, 6, 7])->first();
            if ($camp) {
                return response()->json(['errors' =>  ['campaign' => ['Campaign can not be edited during of after sending!']], "messageType" => "error", 'status' => 0], 404);
            }
        } else {
            $input['status'] = 5;
        }

        if ($campaign_id == '')
            $camp = false;
        else
            $camp = EmailCampaign::where('id', $campaign_id)->where('status', '!=', 2)->first();

        $messages = [
            'name.required' => "This field is required!",
            'message.required' => "This field is required!",
            'sender_name.required' => "This field is required!",
            'reply_to_number.required' => "This field is required!",
            'type.required' => "This field is required!",
            'schedule_date.required' => "This field is required!",
            'recursive_campaign_type.required' => "This field is required!",
            'day_of_week.required' => "This field is required!",
            'day_of_month.required' => "This field is required!",
            'month_of_year.required' => "This field is required!",
            'group_ids.required' => "Group field is required!",
            'no_of_time.required' => "This field is required!",
            'callback_url.regex' => "Invalid URL"
        ];

        $validation_rules = [
            'name' => 'required|string|max:250',
            'message' => 'required|string|max:250',
            'sender_name' => 'required|string|max:65',
            'reply_to_number' => 'nullable|regex:/([1-9]{2})(\d{10})/',
            'group_ids' => 'required|array',
            'type' => 'required|integer|between:1,3',
            'schedule_date' => Rule::requiredIf($request->type == 2),
            'no_of_time' => Rule::requiredIf($request->type == 3),
            'recursive_campaign_type' => Rule::requiredIf($request->type == 3),
            'day_of_week' => Rule::requiredIf($request->recursive_campaign_type == 1),
            'day_of_month' => Rule::requiredIf($request->recursive_campaign_type == 2 || $request->recursive_campaign_type == 3),
            'month_of_year' => Rule::requiredIf($request->recursive_campaign_type == 3),
            'callback_url' => 'required',
        ];

        $validator = \Validator::make($request->all(), $validation_rules, $messages);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json(['errors' => $errors, "messageType" => "error", 'status' => 0], 422);
        }
        $input['sending_to'] = 0;

        if (isset($input['recursive_campaign_type'])) {
            $validator = \Validator::make($request->all(), ['recursive_campaign_type' => 'integer|between:1,3'], $messages);
            if ($validator->fails()) {
                $errors = $validator->errors();
                return response()->json(['errors' => $errors, "messageType" => "error", 'status' => 0], 422);
            }
        }
        if (isset($input['day_of_week'])) {
            $validator = \Validator::make($request->all(), ['day_of_week' => 'integer|between:0,7'], $messages);
            if ($validator->fails()) {
                $errors = $validator->errors();
                return response()->json(['errors' => $errors, "messageType" => "error", 'status' => 0], 422);
            }
        }
        if (isset($input['day_of_month'])) {
            $validator = \Validator::make($request->all(), ['day_of_month' => 'integer|between:1,31'], $messages);
            if ($validator->fails()) {
                $errors = $validator->errors();
                return response()->json(['errors' => $errors, "messageType" => "error", 'status' => 0], 422);
            }
        }
        if (isset($input['month_of_year'])) {
            $validator = \Validator::make($request->all(), ['month_of_year' => 'integer|between:1,12'], $messages);
            if ($validator->fails()) {
                $errors = $validator->errors();
                return response()->json(['errors' => $errors, "messageType" => "error", 'status' => 0], 422);
            }
        }
        if (isset($input['reply_to_number'])) {
            $input['sender_number'] =  '+' . $input['reply_to_number'];
        }

        if (!in_array($input['callback_url'], json_decode($user->endpoint_urls))) {
            return response()->json(['errors' => ['callback_url' => ['Call back URL not found. Please recheck or register the url on website.']], "messageType" => "error", 'status' => 0], 422);
        }

        unset($input['campaign_id']);

        // decoding each group_id
        $grp_ids = [];
        foreach ($input['group_ids'] as $grp) {
            if (Hashids::decode($grp) != []) {
                $grup = Group::where('user_id', $user->id)->where('id', Hashids::decode($grp)[0])->first();
                if (!$grup) {
                    return response()->json(['errors' =>  ['group' => [$grp . ' group not found!']], "messageType" => "error", 'status' => 0], 404);
                }
                // array_push($grp_ids, Hashids::decode($grp)[0]);
                $g = Group::where('id', Hashids::decode($grp)[0])->first();
                if (!$camp) {
                    $last = $g->contacts()->withPivot('id')->orderBy('contact_groups.id', 'desc')->first()->pivot->id;
                } else {
                    $col = array_search($g->id, array_column(json_decode($camp->group_ids), 'id'));
                    if ($col >= 0)
                        $last = array_column(json_decode($camp->group_ids), 'last')[$col];
                    else {
                        $last = 0;
                    }
                }
                array_push($grp_ids, ['id' => $g->id, 'last' => $last]);
            } else {
                return response()->json(['errors' =>  ['group' => [$grp . ' group not found!']], "messageType" => "error", 'status' => 0], 404);
            }
        }
        $input['group_ids'] = json_encode($grp_ids);
        // checking if campaign has contacts in group.
        $input['sending_to'] = 3;

        $contacts = [];
        $excluding = 0;
        if ($input['group_ids'] && count(json_decode($input['group_ids'])) != 0) {
            $groups = Group::whereIn('id', array_column(json_decode($input['group_ids']), 'id'))->get();
            foreach ($groups as $group) {
                $col = array_search($group->id, array_column(json_decode($input['group_ids']), 'id'));
                $pivotid = array_column(json_decode($input['group_ids']), 'last')[$col];
                $grp_contacts = $group->contacts()->withPivot('id')->wherePivot('id', '<=', $pivotid)->get()->toArray();
                $contacts = array_merge($contacts, array_column($grp_contacts, 'id'));
            }
        }
        if ($campaign_id != "") {
            // $includes = CampaignContact::where('type', 1)->where('campaign_id', $campaign_id)->pluck('contact_id')->toArray();
            if ($camp) {
                $includes = CampaignContact::where('type', 1)->where('campaign_id', $campaign_id)->where('id', '<=', $camp->group_id)->pluck('contact_id')->toArray();
            } else {
                $includes = CampaignContact::where('type', 1)->where('campaign_id', $campaign_id)->pluck('contact_id')->toArray();
            }
            $allcontacts = array_unique(array_merge($contacts, $includes));
            $contacts = Contact::whereIn('id', $allcontacts)->get()->toArray();
            $excluding = CampaignExclude::where('type', 1)->where('campaign_id', $campaign_id)->get()->count();
        }
        $total_contacts = 0;
        if ($request->type == 3) {
            $total_contacts = (count($contacts) - $excluding) * $request->no_of_time;
        } else {
            $total_contacts = count($contacts) - $excluding;
        }
        if ($total_contacts <= 0) {
            return response(['message' => 'Add more contacts.', 'errors' => ['group_ids' => ['Contacts not found or excluded']]], 422);
        } else {
            // checking user package limits
            if ($campaign_id == '')
                $camp = false;
            else
                $camp = SmsCampaign::where('id', $campaign_id)->where('status', '!=', 1)->first();
            if ($user->package_subscription_id && !($camp)) {
                $subscription = PackageSubscription::where('id', $user->package_subscription_id)->where('is_active', 1)->first();
                if ($subscription && $subscription->package_id != 9) {
                    // sms_limit
                    $smsLimit = $subscription->sms_limit;
                    $smsUsed = $subscription->sms_used;
                    $sending = $total_contacts;
                    $addingNow = $sending + $smsUsed;
                    if ($addingNow > $smsLimit) {
                        return response(['message' => "Cannot send this Campaign. Sending this would exceed your package limits.", 'code' => 0, 'errors' => ['error_message' => ["Limit Exceeded"]],], 422);
                    } else {
                        // add to used SMS
                        if ($user->package_subscription_id) {
                            $subscription = PackageSubscription::where('id', $user->package_subscription_id)->where('is_active', 1)->first();
                            if ($subscription) {
                                $subscription->update(['sms_used' => $subscription->sms_used + $total_contacts]);
                            }
                        }
                    }
                } else if ($subscription && $subscription->package_id == 9) {
                    // check payment Relief
                    $last_payment = PayAsYouGoPayments::where('package_subscription_id', $user->subscription->id)->where('status', '!=', 1)->first();
                    if ($last_payment && isset($last_payment->timestamp)) {
                        $last_payment = $last_payment->timestamp;
                        $payment_relief_days = (int)settingValue('payment_relief_days');
                        $releif_timestamp = Carbon::now('UTC')->subDays($payment_relief_days)->timestamp;
                        if ($last_payment < $releif_timestamp) {
                            return response(['message' => "You have not paid for your data. We have no choice but to Disable your account. Please contact support for reactivation.", 'code' => 1, 'errors' => ['error_message' => ["Package not paid for!"]],], 422);
                            $user->update([
                                'status' => 0
                            ]);
                        }
                    } else {
                        // add to used emails
                        $sms_span1_start = PackageSettingsValue(4, 'start_range');
                        $sms_span1_end = PackageSettingsValue(4, 'end_range');
                        $sms_span1_price = PackageSettingsValue(4, 'price_without_vat');
                        $sms_span2_start = PackageSettingsValue(5, 'start_range');
                        $sms_span2_end = PackageSettingsValue(5, 'end_range');
                        $sms_span2_price = PackageSettingsValue(5, 'price_without_vat');
                        $sms_span3_start = PackageSettingsValue(6, 'start_range');
                        $sms_span3_price = PackageSettingsValue(6, 'price_without_vat');

                        for ($i = 0; $i < $total_contacts; $i++) {
                            $subscription->update(['sms_used' => $subscription->sms_used + 1]);
                            $subscription->sms_paying_for += 1;
                            if ($subscription->sms_used >= $sms_span1_start && $subscription->sms_used <= $sms_span1_end) {
                                $subscription->sms_to_pay += $sms_span1_price;
                            } else if ($subscription->sms_used >= $sms_span2_start && $subscription->sms_used <= $sms_span2_end) {
                                $subscription->sms_to_pay += $sms_span2_price;
                            } else if ($subscription->sms_used >= $sms_span3_start) {
                                $subscription->sms_to_pay += $sms_span3_price;
                            }
                            $subscription->save();
                        }
                    }
                }
            }
            // checking user package limits end
        }
        // if (count($contacts) - $excluding <= 0)
        //     return response()->json(['errors' => ['group_id' => ['Contacts not found or excluded']], "messageType" => "error", 'status' => 0], 422);
        if ($request->type != 3) {
            $input['no_of_time'] = 0;
            $input['recursive_campaign_type'] = null;
            $input['day_of_month'] = null;
            $input['month_of_year'] = null;
            $input['day_of_week'] = null;
        } else if ($request->type != 2) {
            $input['schedule_date'] = null;
        }

        // create a unique job code
        do {
            $job_code = Str::random(25);
        } while (SMSCampaign::where("job_code", $job_code)->first() instanceof SMSCampaign);
        $input['job_code'] = $job_code;
        $input['request_source'] = 2;
        $input['subscription_id'] = $user->package_subscription_id;

        // create or update campaign
        $smsCampaign = SMSCampaign::updateOrCreate(
            [
                'user_id' => $user->id,
                'id' => $campaign_id,
            ],
            $input
        );
        // adding user_logs
        if ($smsCampaign->wasRecentlyCreated) {
            User_log::create([
                'user_id' => $user->id,
                'item_id' => $smsCampaign->id,
                'log_type' => 3,
                'module' => 6,
            ]);
            $done = 'created';
        } else if (!$smsCampaign->wasRecentlyCreated && $smsCampaign->wasChanged()) {
            User_log::create([
                'user_id' => $user->id,
                'item_id' => $smsCampaign->id,
                'log_type' => 3,
                'module' => 7,
            ]);
            $done = 'updated';
        }

        if ($request->type == 1) {
            //schedule immediately
            MessageCampaignJob::dispatch($user, $request->all(), $smsCampaign->id, $job_code);
        } elseif ($request->type == 2) {
            //schedule once
            $scheduleTime = Carbon::parse($request->schedule_date);
            $currentTime = Carbon::parse(now()->format('Y-m-d'));
            $totalDuration = $currentTime->diff($scheduleTime);

            MessageCampaignJob::dispatch($user, $request->all(), $smsCampaign->id, $job_code)->delay($totalDuration);
        } elseif ($request->type == 3) {
            //schedule recursively
            if ($request->recursive_campaign_type == 1) {
                //weekly
                $dayOfWeek = Carbon::parse('next ' . config('constants.days_of_week')[$request->day_of_week]); //->toDateString();
                $currentTime = Carbon::parse(now()->format('Y-m-d'));
                $totalDuration = $currentTime->diff($dayOfWeek);

                MessageCampaignJob::dispatch($user, $request->all(), $smsCampaign->id, $job_code)->delay($totalDuration);
            } elseif ($request->recursive_campaign_type == 2) {
                //Monthly
                $month = Carbon::now()->format('F');
                $year = Carbon::now()->format('Y');
                $day = $request->day_of_month;

                $selectedDay = Carbon::parse($month . ' ' . $day . ' ' . $year);
                // $currentDay = Carbon::parse(Carbon::now());
                $currentTime = Carbon::parse(now()->format('Y-m-d'));

                if ($selectedDay->gt($currentTime)) {
                    $selectedDay = $selectedDay;
                } else {
                    $selectedDay = $selectedDay->addMonth();
                }

                $selectedDay = Carbon::parse($selectedDay); //->toDateString();
                $currentTime = Carbon::parse(now()->format('Y-m-d'));
                $totalDuration = $currentTime->diff($selectedDay);

                MessageCampaignJob::dispatch($user, $request->all(), $smsCampaign->id, $job_code)->delay($totalDuration);
            } elseif ($request->recursive_campaign_type == 3) {
                //Yearly

                $month = config('constants.months_of_year')[$request->month_of_year];
                $day = $request->day_of_month;
                $year = date('Y');

                $selectedDay = Carbon::parse($month . ' ' . $day . ' ' . $year);
                // $currentDay = Carbon::parse(Carbon::now());
                $currentTime = Carbon::parse(now()->format('Y-m-d'));

                if ($selectedDay->gt($currentTime)) {
                    $selectedDay = $selectedDay;
                } else {
                    $selectedDay = $selectedDay->addYear();
                }

                $selectedDay = Carbon::parse($selectedDay); //->toDateString();
                $currentTime = Carbon::parse(now()->format('Y-m-d'));
                $totalDuration = $currentTime->diff($selectedDay);

                MessageCampaignJob::dispatch($user, $request->all(), $smsCampaign->id, $job_code)->delay($totalDuration);
            }
        }
        return response()->json(['_id' => $this->encodeId($smsCampaign->id), 'message' => 'SMS Campaign ' . $done, "messageType" => "success", 'status' => 1], 200);
    }

    /**
     * Display the specified sms.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function smsShow(Request $request, $id)
    {
        $id = $this->decodeId($id);
        $user = $request->get('user');
        $SmsCampaign = SmsCampaign::where('id', $id)->first();

        if ($SmsCampaign && $SmsCampaign->user_id == $user->id) {
            return SmsCampaignResource::collection([$SmsCampaign])[0]
                ->additional([
                    'message' => 'Campaign Fetched', "messageType" => "success", 'status' => 1
                ]);
        }
        return response()->json(['errors' =>  ['campaign' => ['Campaign not found!']], "messageType" => "error", 'status' => 0], 404);
    }

    /**
     * Remove the specified sms from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function smsDestroy(Request $request, $id)
    {
        $id = $this->decodeId($id);
        $user = $request->get('user');
        $found = SmsCampaign::where('id', $id)->where('status', 1)->first();
        if ($found && $found->user_id == $user->id) {
            if ($found->status == 1) {
                $found->delete();
                User_log::create([
                    'user_id' => $user->id,
                    'item_id' => $found->id,
                    'log_type' => 1,
                    'module' => 8,
                ]);
                return response()->json(['message' => "Campaign Deleted successfully", "messageType" => "success", 'status' => 1], 200);
            }
            return response()->json(['errors' =>  ['campaign' => ['Campaign can only be deleted before it is sent or delivered!']], "messageType" => "error", 'status' => 0], 404);
        }
        return response()->json(['errors' =>  ['campaign' => ['Campaign not found or cannot be deleted!']], "messageType" => "error", 'status' => 0], 404);
    }

    /**
     * Display a listing of the report of sms.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function smsReport(Request $request, $id)
    {
        $user = $request->get('user');
        //
        $id = $this->decodeId($id);
        $SmsCampaign = SmsCampaign::where('id', $id)->whereIn('status', [2, 3, 6])->first();
        if ($SmsCampaign && $SmsCampaign->user_id == $user->id) {
            $reports = CampaignHistory::where('type', 1)->where('campaign_id', $id)->paginate(20);
            return CampaignHistoryResource::collection($reports)->additional([
                'name' => $SmsCampaign->name,
                'type' => $SmsCampaign->type == 3 ? "Recursive" : "OneTime",
                'message' => 'SMS Campaign Report',
                "messageType" => "success",
                'status' => 1,
            ]);
        }
        return response()->json(['errors' =>  ['campaign' => ['Campaign not found!']], "messageType" => "error", 'status' => 0], 404);
    }

    /**
     * Stop a specified sms campaign.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function smsStop(Request $request, $id)
    {
        $user = $request->get('user');
        $id = Hashids::decode($id);
        $SmsCampaign = SmsCampaign::where('id', $id)->first();
        if ($SmsCampaign && $SmsCampaign->user_id == $user->id) {

            if ($SmsCampaign->status != 2 && $SmsCampaign->status != 5) {
                return response()->json(['errors' =>  ['campaign' => ['Campaign can not be stopped!']], "messageType" => "error", 'status' => 0], 404);
            }
            if ($SmsCampaign->status == 4 || $SmsCampaign->status == 6) {
                return response()->json(['errors' =>  ['campaign' => ['Campaign already stopped!']], "messageType" => "error", 'status' => 0], 404);
            }

            // create a unique job code
            do {
                $job_code = Str::random(25);
            } while (SMSCampaign::where("job_code", $job_code)->first() instanceof SMSCampaign);

            $contacts = [];
            if ($SmsCampaign->group_ids && count(json_decode($SmsCampaign->group_ids)) != 0) {
                $groups = Group::whereIn('id', array_column(json_decode($SmsCampaign->group_ids), 'id'))->with('contacts')->get();
                foreach ($groups as $group) {
                    // $contacts = array_merge($contacts, $group->contacts->pluck('id')->toArray());
                    $col = array_search($group->id, array_column(json_decode($SmsCampaign->group_ids), 'id'));
                    $pivotid = array_column(json_decode($SmsCampaign->group_ids), 'last')[$col];
                    $grp_contacts = $group->contacts()->withPivot('id')->wherePivot('id', '<=', $pivotid)->get()->toArray();
                    $contacts = array_merge($contacts, array_column($grp_contacts, 'id'));
                }
            }
            // $includes = CampaignContact::where('type', 1)->where('campaign_id', $id)->pluck('contact_id')->toArray();
            $includes = CampaignContact::where('type', 1)->where('campaign_id', $id)->where('id', '<=', $SmsCampaign->group_id)->pluck('contact_id')->toArray();
            $allcontacts = array_unique(array_merge($contacts, $includes));
            $contacts = Contact::whereIn('id', $allcontacts)->get()->toArray();
            $excluding = CampaignExclude::where('type', 1)->where('campaign_id', $id)->get()->count();

            if ($SmsCampaign->type == 3) {
                // recursive
                $total_contacts = (count($contacts) - $excluding) * $SmsCampaign->no_of_time;
                $history = CampaignHistory::where('type', 1)->where('campaign_id', $SmsCampaign->id)->first();
                if ($history) {
                    $SmsCampaign->update(['status' => 6, 'sending_stopped_at' => now(),  'sending_started_at' => now(), 'sending_completed_at' => now(), 'job_code' => $job_code]);
                    // saving stopped history
                    User_log::create([
                        'user_id' => $SmsCampaign->user_id,
                        'item_id' => $SmsCampaign->id,
                        'module' => 3,
                        'log_type' => 11,
                    ]);
                    $message = "Stopped";
                } else {
                    $SmsCampaign->update(['status' => 4, 'sending_stopped_at' => now(),  'sending_started_at' => now(), 'sending_completed_at' => now(), 'job_code' => $job_code]);
                    $message = "Disabled";
                }
            } else {
                $SmsCampaign->update(['status' => 4, 'sending_stopped_at' => now(),  'sending_started_at' => now(), 'sending_completed_at' => now(), 'job_code' => $job_code]);
                $total_contacts = count($contacts) - $excluding;
                $message = "Disabled";
            }

            if ($user->package_subscription_id) {
                $subscription = PackageSubscription::where('id', $user->package_subscription_id)->where('is_active', 1)->first();
                if ($subscription && $subscription->package_id != 9) {
                    // sub from to used emails
                    if ($subscription->sms_used - $total_contacts < 0)
                        $val = 0;
                    else
                        $val = $subscription->sms_used - $total_contacts;
                    $subscription->update(['sms_used' => $val]);
                } else if ($subscription && $subscription->package_id == 9 && $SmsCampaign->created_at->startOfWeek()->format('Y-m-d') ==  now()->startOfWeek()->format('Y-m-d')) {
                    // add to used emails
                    $sms_span1_price = PackageSettingsValue(4, 'price_without_vat');

                    if ($subscription->sms_used - $total_contacts < 0)
                        $val = 0;
                    else
                        $val = $subscription->sms_used - $total_contacts;
                    $subscription->update(['sms_used' => $val]);
                    $subscription->sms_paying_for -= $total_contacts;
                    if ($subscription->sms_paying_for < 0)
                        $subscription->sms_paying_for = 0;
                    $subscription->sms_to_pay -= $sms_span1_price;
                    if ($subscription->sms_to_pay < 0)
                        $subscription->sms_to_pay = 0;
                    $subscription->save();
                }
            }

            return response()->json(['message' => $message, "messageType" => "success", 'status' => 1], 200);
        }
        return response()->json(['errors' =>  ['campaign' => ['Campaign not found!']], "messageType" => "error", 'status' => 0], 404);
    }

    // ========================================== //
    // ======== Email CAMPAIGN FUNCTIONS ========== //
    // ========================================== //

    /**
     * Display a listing of the email.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function templateIndex(Request $request)
    {
        $user = $request->get('user');
        $template = EmailCampaignTemplate::where('user_id', $user->id)->paginate(10);
        return TemplateResource::collection($template)->additional([
            'total_templates' => $template->count(), 'message' => 'Template Fetched',
            "messageType" => "success", 'status' => 1
        ]);
    }

    /**
     * Display a listing of the email.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function emailIndex(Request $request)
    {
        $user = $request->get('user');
        $email = EmailCampaign::where('user_id', $user->id)->where('is_split_testing', 0)->paginate(10);
        return ECampaignResource::collection($email)->additional([
            'total_campaigns' => $email->count(), 'message' => 'Campaigns Fetched',
            "messageType" => "success", 'status' => 1
        ]);
    }

    /**
     * Store a newly created email in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function emailStore(Request $request)
    {
        $user = $request->get('user');

        $input = $request->all();
        $id = '';
        if (isset($input['campaign_id'])) {
            if (Hashids::decode($input['campaign_id']) == []) {
                return response()->json(['errors' =>  ['campaign' => ['campaign not found!']], "messageType" => "error", 'status' => 0], 404);
            }
            $id = Hashids::decode($input['campaign_id'])[0];
            $camp = EmailCampaign::where('id', $id)->whereIn('status', [3, 5, 6, 7])->first();
            if ($camp) {
                return response()->json(['errors' =>  ['campaign' => ['Campaign can not be edited during or after sending!']], "messageType" => "error", 'status' => 0], 404);
            }
        } else {
            $input['status'] = 1;
        }

        if ($id == '')
            $camp = false;
        else
            $camp = EmailCampaign::where('id', $id)->where('status', '!=', 2)->first();

        $messages = [
            'name.required' => "This field is required!",
            'subject.required' => "This field is required!",
            'sender_name.required' => "This field is required!",
            'sender_email.required' => "This field is required!",
            'reply_to_email.required' => "This field is required!",
            'campaign_type.required' => "This field is required!",
            'schedule_date.required' => "This field is required!",
            'recursive_campaign_type.required' => "This field is required!",
            'day_of_week.required' => "This field is required!",
            'day_of_month.required' => "This field is required!",
            'month_of_year.required' => "This field is required!",
            // 'day_of_week_year.required' => "This field is required!",
            'group_ids.required' => "This field is required!",
            'template_id.required' => "This field is required!",
            'callback_url.regex' => "Invalid URL"
        ];

        $validation_rules = [
            'name' => 'required|string|max:250',
            'subject' => 'required|string|max:250',
            'sender_name' => 'required|string|max:65',
            'sender_email' => ['required', 'regex:/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/', 'max:65'],
            'reply_to_email' => ['nullable', 'regex:/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/', 'max:65'],
            'campaign_type' => 'required|integer|between:1,3',
            'group_ids' => 'required|array',
            'template_id' => 'required|string',
            'schedule_date' => Rule::requiredIf($request->campaign_type == 2),
            'recursive_campaign_type' => Rule::requiredIf($request->campaign_type == 3),
            'no_of_time' => Rule::requiredIf($request->campaign_type == 3),
            'day_of_week' => Rule::requiredIf($request->recursive_campaign_type == 1),
            'day_of_month' => Rule::requiredIf($request->recursive_campaign_type == 2 || $request->recursive_campaign_type == 3),
            'month_of_year' => Rule::requiredIf($request->recursive_campaign_type == 3),
            // 'day_of_week_year' => Rule::requiredIf($request->recursive_campaign_type == 3),
            'callback_url' => 'required',
        ];
        $validator = \Validator::make($request->all(), $validation_rules, $messages);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json(['errors' => $errors, "messageType" => "error", 'status' => 0], 422);
        }

        if (isset($input['recursive_campaign_type'])) {
            $validator = \Validator::make($request->all(), ['recursive_campaign_type' => 'integer|between:1,3'], $messages);
            if ($validator->fails()) {
                $errors = $validator->errors();
                return response()->json(['errors' => $errors, "messageType" => "error", 'status' => 0], 422);
            }
        }
        if (isset($input['day_of_week'])) {
            $validator = \Validator::make($request->all(), ['day_of_week' => 'integer|between:0,7'], $messages);
            if ($validator->fails()) {
                $errors = $validator->errors();
                return response()->json(['errors' => $errors, "messageType" => "error", 'status' => 0], 422);
            }
        }
        if (isset($input['day_of_month'])) {
            $validator = \Validator::make($request->all(), ['day_of_month' => 'integer|between:1,31'], $messages);
            if ($validator->fails()) {
                $errors = $validator->errors();
                return response()->json(['errors' => $errors, "messageType" => "error", 'status' => 0], 422);
            }
        }
        if (isset($input['month_of_year'])) {
            $validator = \Validator::make($request->all(), ['month_of_year' => 'integer|between:1,12'], $messages);
            if ($validator->fails()) {
                $errors = $validator->errors();
                return response()->json(['errors' => $errors, "messageType" => "error", 'status' => 0], 422);
            }
        }

        if (!in_array($input['callback_url'], json_decode($user->endpoint_urls))) {
            return response()->json(['errors' => ['callback_url' => ['Call back URL not found. Please recheck or register the url on website.']], "messageType" => "error", 'status' => 0], 422);
        }

        unset($input['campaign_id']);
        $input['group_id'] = 0;

        // decoding each group_id
        $grp_ids = [];
        foreach ($input['group_ids'] as $grp) {
            if (Hashids::decode($grp) != []) {
                $grup = Group::where('user_id', $user->id)->where('id', Hashids::decode($grp)[0])->first();
                if (!$grup) {
                    return response()->json(['errors' =>  ['group' => [$grp . ' group not found!']], "messageType" => "error", 'status' => 0], 404);
                }
                // array_push($grp_ids, Hashids::decode($grp)[0]);
                $g = Group::where('id', Hashids::decode($grp)[0])->first();
                if (!$camp) {
                    $last = $g->contacts()->withPivot('id')->orderBy('contact_groups.id', 'desc')->first()->pivot->id;
                } else {
                    $col = array_search($g->id, array_column(json_decode($camp->group_ids), 'id'));
                    if ($col >= 0)
                        $last = array_column(json_decode($camp->group_ids), 'last')[$col];
                    else {
                        $last = 0;
                    }
                }
                array_push($grp_ids, ['id' => $g->id, 'last' => $last]);
            } else {
                return response()->json(['errors' =>  ['group' => [$grp . ' group not found!']], "messageType" => "error", 'status' => 0], 404);
            }
        }
        $input['group_ids'] = json_encode($grp_ids);

        if (Hashids::decode($input['template_id']) != []) {
            $template = EmailCampaignTemplate::where('user_id', $user->id)->where('id', Hashids::decode($input['template_id'])[0])->first();
            if (!$template) {
                return response()->json(['errors' =>  ['template' => ['template not found!']], "messageType" => "error", 'status' => 0], 404);
            }
            $input['template_id'] = Hashids::decode($input['template_id'])[0];
        } else {
            return response()->json(['errors' =>  ['template' => ['template not found!']], "messageType" => "error", 'status' => 0], 404);
        }

        // checking if campaign has contacts in group.
        $input['sending_to'] = 3;

        $contacts = [];
        $excluding = 0;
        if ($input['group_ids'] && count(json_decode($input['group_ids'])) != 0) {
            // dd();
            $groups = Group::whereIn('id', array_column(json_decode($input['group_ids']), 'id'))->get();
            foreach ($groups as $group) {
                $col = array_search($group->id, array_column(json_decode($input['group_ids']), 'id'));
                $pivotid = array_column(json_decode($input['group_ids']), 'last')[$col];
                $grp_contacts = $group->contacts()->withPivot('id')->wherePivot('id', '<=', $pivotid)->get()->toArray();
                $contacts = array_merge($contacts, array_column($grp_contacts, 'id'));
            }
        }
        if ($id != "") {
            // $includes = CampaignContact::where('type', 2)->where('campaign_id', $id)->pluck('contact_id')->toArray();
            if ($camp) {
                $includes = CampaignContact::where('type', 2)->where('campaign_id', $id)->where('id', '<=', $camp->group_id)->pluck('contact_id')->toArray();
            } else {
                $includes = CampaignContact::where('type', 2)->where('campaign_id', $id)->pluck('contact_id')->toArray();
            }
            $allcontacts = array_unique(array_merge($contacts, $includes));
            $contacts = Contact::whereIn('id', $allcontacts)->get()->toArray();
            $excluding = CampaignExclude::where('type', 2)->where('campaign_id', $id)->get()->count();
        }
        $total_contacts = 0;
        if ($request->campaign_type == 3) {
            $total_contacts = (count($contacts) - $excluding) * $request->no_of_time;
        } else {
            $total_contacts = count($contacts) - $excluding;
        }
        if ($total_contacts <= 0) {
            return response(['message' => "Add more contacts.", 'errors' => ['group_ids' => ["Contacts not found or excluded"]]], 422);
        } else {
            // checking user package limits
            if ($id == '')
                $camp = false;
            else
                $camp = EmailCampaign::where('id', $id)->where('status', '!=', 2)->first();
            if ($user->package_subscription_id && !($camp)) {
                $subscription = PackageSubscription::where('id', $user->package_subscription_id)->where('is_active', 1)->first();
                if ($subscription && $subscription->package_id != 9) {
                    // email_limit
                    $emailLimit = $subscription->email_limit;
                    $emailUsed = $subscription->email_used;
                    $sending = $total_contacts;
                    $addingNow = $sending + $emailUsed;
                    if ($addingNow > $emailLimit) {
                        return response(['message' => "Cannot send this Campaign. Sending this would exceed your package limits.", 'code' => 0, 'errors' => ['error_message' => ["Limit Exceeded"]],], 422);
                    } else {
                        // add to used emails
                        if ($user->package_subscription_id) {
                            $subscription = PackageSubscription::where('id', $user->package_subscription_id)->where('is_active', 1)->first();
                            if ($subscription) {
                                $subscription->update(['email_used' => $subscription->email_used + $total_contacts]);
                            }
                        }
                    }
                } else if ($subscription && $subscription->package_id == 9) {
                    // check payment Relief
                    $last_payment = PayAsYouGoPayments::where('package_subscription_id', $user->subscription->id)->where('status', '!=', 1)->first();
                    if ($last_payment && isset($last_payment->timestamp)) {
                        $last_payment = $last_payment->timestamp;
                        $payment_relief_days = (int)settingValue('payment_relief_days');
                        $releif_timestamp = Carbon::now('UTC')->subDays($payment_relief_days)->timestamp;
                        if ($last_payment < $releif_timestamp) {
                            return response(['message' => "You have not paid for your data. We have no choice but to Disable your account. Please contact support for reactivation.", 'code' => 1, 'errors' => ['error_message' => ["Package not paid for!"]],], 422);
                            $user->update([
                                'status' => 0
                            ]);
                        }
                    } else {
                        // add to used emails
                        $email_span1_start = PackageSettingsValue(1, 'start_range');
                        $email_span1_end = PackageSettingsValue(1, 'end_range');
                        $email_span1_price = PackageSettingsValue(1, 'price_without_vat');
                        $email_span2_start = PackageSettingsValue(2, 'start_range');
                        $email_span2_end = PackageSettingsValue(2, 'end_range');
                        $email_span2_price = PackageSettingsValue(2, 'price_without_vat');
                        $email_span3_start = PackageSettingsValue(3, 'start_range');
                        $email_span3_price = PackageSettingsValue(3, 'price_without_vat');

                        for ($i = 0; $i < $total_contacts; $i++) {
                            $subscription->update(['email_used' => $subscription->email_used + 1]);
                            $subscription->emails_paying_for += 1;
                            if ($subscription->email_used >= $email_span1_start && $subscription->email_used <= $email_span1_end) {
                                $subscription->emails_to_pay += ($email_span1_price);
                            } else if ($subscription->email_used >= $email_span2_start && $subscription->email_used <= $email_span2_end) {
                                $subscription->emails_to_pay += ($email_span2_price);
                            } else if ($subscription->email_used >= $email_span3_start) {
                                $subscription->emails_to_pay += ($email_span3_price);
                            }
                            $subscription->save();
                        }
                    }
                }
            }
            // checking user package limits end
        }

        if ($request->campaign_type != 3) {
            $input['no_of_time'] = 0;
            $input['recursive_campaign_type'] = null;
            $input['day_of_month'] = null;
            $input['month_of_year'] = null;
            $input['day_of_week'] = null;
        } else if ($request->campaign_type != 2) {
            $input['schedule_date'] = null;
        }
        // if (count($contacts) - $excluding <= 0)
        //     return response()->json(['errors' => ['group_id' => ['Contacts not found or excluded']], "messageType" => "error", 'status' => 0], 422);

        // create a unique job code
        do {
            $job_code = Str::random(25);
        } while (SMSCampaign::where("job_code", $job_code)->first() instanceof SMSCampaign);
        $input['job_code'] = $job_code;
        $input['request_source'] = 2;
        $input['subscription_id'] = $user->package_subscription_id;

        $emailCampaign = EmailCampaign::updateOrCreate(
            [
                'user_id' => $user->id,
                'id' => $id,
            ],
            $input
        );

        if ($id == '')
            $done = 'created';
        else
            $done = 'updated';

        if ($request->campaign_type == 1) {
            //schedule immediately
            EmailCampaignJob::dispatch($user, $request->all(), $emailCampaign->id, $job_code);
        } elseif ($request->campaign_type == 2) {
            //schedule once

            $scheduleTime = Carbon::parse($request->schedule_date);
            $currentTime = Carbon::parse(now()->format('Y-m-d'));
            //$totalDuration = $scheduleTime->diffInMinutes($currentTime);
            $totalDuration = $currentTime->diff($scheduleTime);
            //$length = $finishTime->diffInMinutes($startTime);
            EmailCampaignJob::dispatch($user, $request->all(), $emailCampaign->id, $job_code)->delay($totalDuration);
        } elseif ($request->campaign_type == 3) {
            //schedule recursively

            if ($request->recursive_campaign_type == 1) {
                //weekly
                $dayOfWeek = Carbon::parse('next ' . config('constants.days_of_week')[$request->day_of_week]); //->toDateString();
                $currentTime = Carbon::parse(now()->format('Y-m-d'));
                $totalDuration = $currentTime->diff($dayOfWeek);

                EmailCampaignJob::dispatch($user, $request->all(), $emailCampaign->id, $job_code)->delay($totalDuration);
            } elseif ($request->recursive_campaign_type == 2) {
                //Monthly

                $month = Carbon::now()->format('F');
                $year = Carbon::now()->format('Y');
                $day = $request->day_of_month;

                $selectedDay = Carbon::parse($month . ' ' . $day . ' ' . $year);
                $currentDay = Carbon::parse(Carbon::now());

                if ($selectedDay->gt($currentDay)) {
                    $selectedDay = $selectedDay;
                } else {
                    $selectedDay = $selectedDay->addMonth();
                }
                $scheduleTime = Carbon::parse($selectedDay);
                $currentTime = Carbon::parse(now()->format('Y-m-d'));
                $totalDuration = $currentTime->diff($scheduleTime);

                EmailCampaignJob::dispatch($user, $request->all(), $emailCampaign->id, $job_code)->delay($totalDuration);
            } elseif ($request->recursive_campaign_type == 3) {
                //Yearly

                $month = config('constants.months_of_year')[$request->month_of_year];
                $day = $request->day_of_month;
                $year = date('Y');

                if ($request->month_of_year > Carbon::now()->month) {
                    $selectedDay = Carbon::parse($month . ' ' . $day . ' ' . $year)->startOfMonth();
                } else {
                    $selectedDay = Carbon::parse($month . ' ' . $day . ' ' . $year)->addMonth(12)->startOfMonth();
                }

                $scheduleTime = Carbon::parse($selectedDay);
                $currentTime = Carbon::parse(now()->format('Y-m-d'));
                $totalDuration = $currentTime->diff($scheduleTime);

                EmailCampaignJob::dispatch($user, $request->all(), $emailCampaign->id, $job_code)->delay($totalDuration);
            }
        }

        return response()->json(['_id' => $this->encodeId($emailCampaign->id), 'message' => 'Email Campaign ' . $done, "messageType" => "success", 'status' => 1], 200);
    }

    /**
     * Display the specified Email.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function emailShow(Request $request, $id)
    {
        $id = $this->decodeId($id);
        $user = $request->get('user');
        $emailCampaign = EmailCampaign::where('id', $id)->where('is_split_testing', 0)->first();

        if ($emailCampaign && $emailCampaign->user_id == $user->id) {
            return ECampaignResource::collection([$emailCampaign])[0]
                ->additional([
                    'message' => 'Campaign Fetched', "messageType" => "success", 'status' => 1
                ]);
        }
        return response()->json(['errors' =>  ['campaign' => ['Campaign not found!']], "messageType" => "error", 'status' => 0], 404);
    }

    /**
     * Remove the specified Email from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function emailDestroy(Request $request, $id)
    {
        $id = $this->decodeId($id);
        $user = $request->get('user');
        $found = EmailCampaign::where('id', $id)->where('is_split_testing', 0)->where('status', 2)->first();
        if ($found && $found->user_id == $user->id) {
            if ($found->status == 2) {
                $found->delete();
                User_log::create([
                    'user_id' => $user->id,
                    'item_id' => $found->id,
                    'log_type' => 1,
                    'module' => 8,
                ]);
                return response()->json(['message' => "Campaign Deleted successfully", "messageType" => "success", 'status' => 1], 200);
            }
            return response()->json(['errors' =>  ['campaign' => ['Campaign can only be deleted before it is sent or delivered!']], "messageType" => "error", 'status' => 0], 404);
        }
        return response()->json(['errors' =>  ['campaign' => ['Campaign not found or cannot be deleted!']], "messageType" => "error", 'status' => 0], 404);
    }

    /**
     * Display a listing of the report of Email.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function emailReport(Request $request, $id)
    {
        $user = $request->get('user');
        //
        $id = $this->decodeId($id);
        $EmailCampaign = EmailCampaign::where('id', $id)->where('is_split_testing', 0)->whereIn('status', [4, 5, 6])->first();
        if ($EmailCampaign && $EmailCampaign->user_id == $user->id) {
            $reports = CampaignHistory::where('type', 2)->where('campaign_id', $id)->paginate(20);
            return CampaignHistoryResource::collection($reports)->additional([
                'name' => $EmailCampaign->name,
                'type' => $EmailCampaign->campaign_type == 3 ? "Recursive" : "OneTime",
                'message' => 'Email Campaign Report',
                "messageType" => "success",
                'status' => 1,
            ]);
        }
        return response()->json(['errors' =>  ['campaign' => ['Campaign not found!']], "messageType" => "error", 'status' => 0], 404);
    }

    // ========================================== //
    // ======== Split CAMPAIGN FUNCTIONS ========== //
    // ========================================== //

    /**
     * Display a listing of the split.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function splitIndex(Request $request)
    {
        $user = $request->get('user');
        $split = EmailCampaign::where('user_id', $user->id)->where('is_split_testing', 1)->paginate(10);
        return ECampaignResource::collection($split)->additional([
            'total_campaigns' => $split->count(), 'message' => 'Campaigns Fetched',
            "messageType" => "success", 'status' => 1
        ]);
    }

    /**
     * Store a newly created split in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function splitStore(Request $request)
    {
        $user = $request->get('user');

        $input = $request->all();
        $id = '';
        if (isset($input['campaign_id'])) {
            if (Hashids::decode($input['campaign_id']) == []) {
                return response()->json(['errors' =>  ['campaign' => ['campaign not found!']], "messageType" => "error", 'status' => 0], 404);
            }
            $id = Hashids::decode($input['campaign_id'])[0];
            $camp = EmailCampaign::where('id', $id)->whereIn('status', [3, 5, 6, 7])->first();
            if ($camp) {
                return response()->json(['errors' =>  ['campaign' => ['Campaign can not be edited during or after sending!']], "messageType" => "error", 'status' => 0], 404);
            }
        } else {
            $input['status'] = 1;
        }

        if ($id == '')
            $camp = false;
        else
            $camp = EmailCampaign::where('id', $id)->where('status', '!=', 2)->first();

        $messages = [
            'name.required' => "This field is required!",
            'subject.required' => "This field is required!",
            'sender_name.required' => "This field is required!",
            'sender_email.required' => "This field is required!",
            'reply_to_email.required' => "This field is required!",
            'campaign_type.required' => "This field is required!",
            'schedule_date.required' => "This field is required!",
            'recursive_campaign_type.required' => "This field is required!",
            'day_of_week.required' => "This field is required!",
            'day_of_month.required' => "This field is required!",
            'month_of_year.required' => "This field is required!",
            // 'day_of_week_year.required' => "This field is required!",
            'group_ids.required' => "This field is required!",
            'split_test_param.required' => "You must select one option to proceed!",
            'split_test_param.integer' => "Selected option must be a number!",
            'template_id.*.string' => "Group _id must be a valid string",
            'callback_url.regex' => "Invalid URL"
        ];

        $validation_rules = [
            'name' => 'required|string|max:250',
            'subject' => Rule::requiredIf($request->split_test_param == 2),
            'sender_name' => 'required|string|max:65',
            'sender_email' => ['required', 'regex:/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/', 'max:65'],
            'reply_to_email' => ['nullable', 'regex:/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/', 'max:65'],
            'campaign_type' => 'required|integer|between:1,3',
            'group_ids' => 'required|array',
            'split_test_param' => 'required|integer|between:1,2',
            'template_id' => Rule::requiredIf($request->split_test_param == 1),
            'template_id.*' => 'string',
            'split_subject_line_1' => Rule::requiredIf($request->split_test_param == 1),
            'split_subject_line_2' => Rule::requiredIf($request->split_test_param == 1),
            'split_email_content_1' => Rule::requiredIf($request->split_test_param == 2),
            'split_email_content_2' => Rule::requiredIf($request->split_test_param == 2),
            'schedule_date' => Rule::requiredIf($request->campaign_type == 2),
            'recursive_campaign_type' => Rule::requiredIf($request->campaign_type == 3),
            'no_of_time' => Rule::requiredIf($request->campaign_type == 3),
            'day_of_week' => Rule::requiredIf($request->recursive_campaign_type == 1),
            'day_of_month' => Rule::requiredIf($request->recursive_campaign_type == 2 || $request->recursive_campaign_type == 3),
            'month_of_year' => Rule::requiredIf($request->recursive_campaign_type == 3),
            // 'day_of_week_year' => Rule::requiredIf($request->recursive_campaign_type == 3),
            'callback_url' => 'required',
        ];
        $validator = \Validator::make($request->all(), $validation_rules, $messages);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json(['errors' => $errors, "messageType" => "error", 'status' => 0], 422);
        }

        if (isset($input['recursive_campaign_type'])) {
            $validator = \Validator::make($request->all(), ['recursive_campaign_type' => 'integer|between:1,3'], $messages);
            if ($validator->fails()) {
                $errors = $validator->errors();
                return response()->json(['errors' => $errors, "messageType" => "error", 'status' => 0], 422);
            }
        }
        if (isset($input['day_of_week'])) {
            $validator = \Validator::make($request->all(), ['day_of_week' => 'integer|between:0,7'], $messages);
            if ($validator->fails()) {
                $errors = $validator->errors();
                return response()->json(['errors' => $errors, "messageType" => "error", 'status' => 0], 422);
            }
        }
        if (isset($input['day_of_month'])) {
            $validator = \Validator::make($request->all(), ['day_of_month' => 'integer|between:1,31'], $messages);
            if ($validator->fails()) {
                $errors = $validator->errors();
                return response()->json(['errors' => $errors, "messageType" => "error", 'status' => 0], 422);
            }
        }
        if (isset($input['month_of_year'])) {
            $validator = \Validator::make($request->all(), ['month_of_year' => 'integer|between:1,12'], $messages);
            if ($validator->fails()) {
                $errors = $validator->errors();
                return response()->json(['errors' => $errors, "messageType" => "error", 'status' => 0], 422);
            }
        }

        if (!in_array($input['callback_url'], json_decode($user->endpoint_urls))) {
            return response()->json(['errors' => ['callback_url' => ['Call back URL not found. Please recheck or register the url on website.']], "messageType" => "error", 'status' => 0], 422);
        }

        unset($input['campaign_id']);
        $input['is_split_testing'] = 1;
        $input['group_id'] = 0;

        // decoding each group_id
        $grp_ids = [];
        foreach ($input['group_ids'] as $grp) {
            if (Hashids::decode($grp) != []) {
                $grup = Group::where('user_id', $user->id)->where('id', Hashids::decode($grp)[0])->first();
                if (!$grup) {
                    return response()->json(['errors' =>  ['group' => [$grp . ' group not found!']], "messageType" => "error", 'status' => 0], 404);
                }
                // array_push($grp_ids, Hashids::decode($grp)[0]);
                $g = Group::where('id', Hashids::decode($grp)[0])->first();
                if (!$camp) {
                    $last = $g->contacts()->withPivot('id')->orderBy('contact_groups.id', 'desc')->first()->pivot->id;
                } else {
                    $col = array_search($g->id, array_column(json_decode($camp->group_ids), 'id'));
                    if ($col >= 0)
                        $last = array_column(json_decode($camp->group_ids), 'last')[$col];
                    else {
                        $last = 0;
                    }
                }
                array_push($grp_ids, ['id' => $g->id, 'last' => $last]);
            } else {
                return response()->json(['errors' =>  ['group' => [$grp . ' group not found!']], "messageType" => "error", 'status' => 0], 404);
            }
        }
        $input['group_ids'] = json_encode($grp_ids);

        if (isset($input['template_id'])) {
            if (Hashids::decode($input['template_id']) != []) {
                $template = EmailCampaignTemplate::where('user_id', $user->id)->where('id', Hashids::decode($input['template_id'])[0])->first();
                if (!$template) {
                    return response()->json(['errors' =>  ['template' => ['template not found!']], "messageType" => "error", 'status' => 0], 404);
                }
                $input['template_id'] = Hashids::decode($input['template_id'])[0];
            } else {
                return response()->json(['errors' =>  ['template' => ['template not found!']], "messageType" => "error", 'status' => 0], 404);
            }
        }

        if (isset($input['split_email_content_1'])) {
            if (Hashids::decode($input['split_email_content_1']) != []) {
                $template = EmailCampaignTemplate::where('user_id', $user->id)->where('id', Hashids::decode($input['split_email_content_1'])[0])->first();
                if (!$template) {
                    return response()->json(['errors' =>  ['split_email_content_1' => ['template not found!']], "messageType" => "error", 'status' => 0], 404);
                }
                $input['split_email_content_1'] = Hashids::decode($input['split_email_content_1'])[0];
            } else {
                return response()->json(['errors' =>  ['split_email_content_1' => ['template not found!']], "messageType" => "error", 'status' => 0], 404);
            }
        }

        if (isset($input['split_email_content_2'])) {
            if (Hashids::decode($input['split_email_content_2']) != []) {
                $template = EmailCampaignTemplate::where('user_id', $user->id)->where('id', Hashids::decode($input['split_email_content_2'])[0])->first();
                if (!$template) {
                    return response()->json(['errors' =>  ['split_email_content_2' => ['template not found!']], "messageType" => "error", 'status' => 0], 404);
                }
                $input['split_email_content_2'] = Hashids::decode($input['split_email_content_2'])[0];
            } else {
                return response()->json(['errors' =>  ['split_email_content_2' => ['template not found!']], "messageType" => "error", 'status' => 0], 404);
            }
        }


        // checking if campaign has contacts in group.
        $input['sending_to'] = 3;

        $contacts = [];
        $excluding = 0;
        if ($input['group_ids'] && count(json_decode($input['group_ids'])) != 0) {
            // dd();
            $groups = Group::whereIn('id', array_column(json_decode($input['group_ids']), 'id'))->get();
            foreach ($groups as $group) {
                $col = array_search($group->id, array_column(json_decode($input['group_ids']), 'id'));
                $pivotid = array_column(json_decode($input['group_ids']), 'last')[$col];
                $grp_contacts = $group->contacts()->withPivot('id')->wherePivot('id', '<=', $pivotid)->get()->toArray();
                $contacts = array_merge($contacts, array_column($grp_contacts, 'id'));
            }
        }
        if (isset($input['campaign_id'])) {
            // $includes = CampaignContact::where('type', 2)->where('campaign_id', $id)->pluck('contact_id')->toArray();
            if ($camp) {
                $includes = CampaignContact::where('type', 2)->where('campaign_id', $id)->where('id', '<=', $camp->group_id)->pluck('contact_id')->toArray();
            } else {
                $includes = CampaignContact::where('type', 2)->where('campaign_id', $id)->pluck('contact_id')->toArray();
            }
            $allcontacts = array_unique(array_merge($contacts, $includes));
            $contacts = Contact::whereIn('id', $allcontacts)->get()->toArray();
            $excluding = CampaignExclude::where('type', 2)->where('campaign_id', $id)->get()->count();
        }

        if (count($contacts) - $excluding <= 1) {
            return response()->json(['errors' => ['group_ids' => ['Contacts not found or excluded']], "messageType" => "error", 'status' => 0], 422);
            // return response(['message' => TranslationHandler::getTranslation($request->lang, 'add_contacts'), 'errors' => ['group_id' => [TranslationHandler::getTranslation($request->lang, 'add_min_two_contacts')]]], 422);
        } else {
            $total_contacts = 0;
            if ($request->campaign_type == 3) {
                $total_contacts = (count($contacts) - $excluding) * $request->no_of_time;
            } else {
                $total_contacts = count($contacts) - $excluding;
            }
            // checking user package limits
            if ($id == '')
                $camp = false;
            else
                $camp = EmailCampaign::where('id', $id)->where('status', '!=', 2)->first();
            if ($user->package_subscription_id && !($camp)) {
                $subscription = PackageSubscription::where('id', $user->package_subscription_id)->where('is_active', 1)->first();
                if ($subscription && $subscription->package_id != 9) {
                    // email_limit
                    $emailLimit = $subscription->email_limit;
                    $emailUsed = $subscription->email_used;
                    $sending = $total_contacts;
                    $addingNow = $sending + $emailUsed;
                    if ($addingNow > $emailLimit) {
                        return response(['message' => "Cannot send this Campaign. Sending this would exceed your package limits.", 'code' => 0, 'errors' => ['error_message' => ["Limit Exceeded"]],], 422);
                    } else {
                        // add to used emails
                        if ($user->package_subscription_id) {
                            $subscription = PackageSubscription::where('id', $user->package_subscription_id)->where('is_active', 1)->first();
                            if ($subscription) {
                                $subscription->update(['email_used' => $subscription->email_used + $total_contacts]);
                            }
                        }
                    }
                } else if ($subscription && $subscription->package_id == 9) {
                    // check payment Relief
                    $last_payment = PayAsYouGoPayments::where('package_subscription_id', $user->subscription->id)->where('status', '!=', 1)->first();
                    if ($last_payment && isset($last_payment->timestamp)) {
                        $last_payment = $last_payment->timestamp;
                        $payment_relief_days = (int)settingValue('payment_relief_days');
                        $releif_timestamp = Carbon::now('UTC')->subDays($payment_relief_days)->timestamp;
                        if ($last_payment < $releif_timestamp) {
                            return response(['message' => "You have not paid for your data. We have no choice but to Disable your account. Please contact support for reactivation.", 'code' => 1, 'errors' => ['error_message' => ["Package not paid for!"]],], 422);
                            $user->update([
                                'status' => 0
                            ]);
                        }
                    } else {
                        // add to used emails
                        $email_span1_start = PackageSettingsValue(1, 'start_range');
                        $email_span1_end = PackageSettingsValue(1, 'end_range');
                        $email_span1_price = PackageSettingsValue(1, 'price_without_vat');
                        $email_span2_start = PackageSettingsValue(2, 'start_range');
                        $email_span2_end = PackageSettingsValue(2, 'end_range');
                        $email_span2_price = PackageSettingsValue(2, 'price_without_vat');
                        $email_span3_start = PackageSettingsValue(3, 'start_range');
                        $email_span3_price = PackageSettingsValue(3, 'price_without_vat');

                        for ($i = 0; $i < $total_contacts; $i++) {
                            $subscription->update(['email_used' => $subscription->email_used + 1]);
                            $subscription->emails_paying_for += 1;
                            if ($subscription->email_used >= $email_span1_start && $subscription->email_used <= $email_span1_end) {
                                $subscription->emails_to_pay += ($email_span1_price);
                            } else if ($subscription->email_used >= $email_span2_start && $subscription->email_used <= $email_span2_end) {
                                $subscription->emails_to_pay += ($email_span2_price);
                            } else if ($subscription->email_used >= $email_span3_start) {
                                $subscription->emails_to_pay += ($email_span3_price);
                            }
                            $subscription->save();
                        }
                    }
                }
            }
            // checking user package limits end
        }

        $subject1 = isset($input['split_subject_line_1']) ? $input['split_subject_line_1'] : NULL;
        unset($input['split_subject_line_1']);
        $subject2 = isset($input['split_subject_line_2']) ? $input['split_subject_line_2'] : NULL;
        unset($input['split_subject_line_2']);

        if ($request->campaign_type != 3) {
            $input['no_of_time'] = 0;
            $input['recursive_campaign_type'] = null;
            $input['day_of_month'] = null;
            $input['month_of_year'] = null;
            $input['day_of_week'] = null;
        } else if ($request->campaign_type != 2) {
            $input['schedule_date'] = null;
        }

        // create a unique job code
        do {
            $job_code = Str::random(25);
        } while (SMSCampaign::where("job_code", $job_code)->first() instanceof SMSCampaign);
        $input['job_code'] = $job_code;
        $input['request_source'] = 2;
        $input['subscription_id'] = $user->package_subscription_id;

        $splitCampaign = EmailCampaign::updateOrCreate(
            [
                'user_id' => $user->id,
                'id' => $id,
            ],
            $input
        );

        if ($id == '')
            $done = 'created';
        else
            $done = 'updated';

        if ($splitCampaign->wasRecentlyCreated) {
            // userlogs
            User_log::create([
                'user_id' => $user->id,
                'item_id' => $splitCampaign->id,
                'log_type' => 4,
                'module' => 6,
            ]);
            // create subject rows with this campaign id
            if ($subject1 || $subject2) {
                $sub1 = SplitTestSubject::create([
                    'user_id' => $user->id,
                    'campaign_id' => $splitCampaign->id,
                    'split_subject' => $subject1,
                ]);
                $sub2 = SplitTestSubject::create([
                    'user_id' => $user->id,
                    'campaign_id' => $splitCampaign->id,
                    'split_subject' => $subject2,
                ]);
                $splitCampaign->update(['split_subject_line_1' => $sub1->id, 'split_subject_line_2' => $sub2->id]);
            }
        } elseif (!$splitCampaign->wasRecentlyCreated && $splitCampaign->wasChanged()) {
            // userlogs
            User_log::create([
                'user_id' => $user->id,
                'item_id' => $splitCampaign->id,
                'log_type' => 4,
                'module' => 7,
            ]);
            // update subject rows with this campaign id
            if ($subject1 || $subject2) {
                $sub1 = SplitTestSubject::where(['user_id' => $user->id, 'campaign_id' => $splitCampaign->id])->first();
                if ($sub1) {
                    $sub1->update(['split_subject' => $subject1]);
                } else {
                    $sub1 = SplitTestSubject::create([
                        'user_id' => $user->id,
                        'campaign_id' => $splitCampaign->id,
                        'split_subject' => $subject1,
                    ]);
                }

                $sub2 = SplitTestSubject::where(['user_id' => $user->id, 'campaign_id' => $splitCampaign->id])->skip(1)->first();
                if ($sub2) {
                    $sub2->update(['split_subject' => $subject2]);
                } else {
                    $sub2 = SplitTestSubject::create([
                        'user_id' => $user->id,
                        'campaign_id' => $splitCampaign->id,
                        'split_subject' => $subject2,
                    ]);
                }
                $splitCampaign->update(['split_subject_line_1' => $sub1->id, 'split_subject_line_2' => $sub2->id]);
            }
        }

        if ($request->campaign_type == 1) {
            //schedule immediately

            SplitTestingJob::dispatch($user, $request->all(), $splitCampaign->id, $job_code)->delay(10);
        } elseif ($request->campaign_type == 2) {
            //schedule once

            $scheduleTime = Carbon::parse($request->schedule_date);
            $currentTime = Carbon::parse(now()->format('Y-m-d'));
            $totalDuration = $currentTime->diff($scheduleTime);

            SplitTestingJob::dispatch($user, $request->all(), $splitCampaign->id, $job_code)->delay($totalDuration);
        } elseif ($request->campaign_type == 3) {
            //schedule recursively

            if ($request->recursive_campaign_type == 1) {
                //weekly

                $dayOfWeek = Carbon::parse('next ' . config('constants.days_of_week')[$request->day_of_week]); //->toDateString();
                $currentTime = Carbon::parse(now()->format('Y-m-d'));
                $totalDuration = $currentTime->diff($dayOfWeek);

                SplitTestingJob::dispatch($user, $request->all(), $splitCampaign->id, $job_code)->delay($totalDuration);
            } elseif ($request->recursive_campaign_type == 2) {
                //Monthly

                $month = Carbon::now()->format('F');
                $year = Carbon::now()->format('Y');
                $day = $request->day_of_month;
                $selectedDay = Carbon::parse($month . ' ' . $day . ' ' . $year);
                $currentDay = Carbon::parse(Carbon::now());

                if ($selectedDay->gt($currentDay)) {
                    $selectedDay = $selectedDay;
                } else {
                    $selectedDay = $selectedDay->addMonth();
                }
                $scheduleTime = Carbon::parse($selectedDay);
                $currentTime = Carbon::parse(now()->format('Y-m-d'));
                $totalDuration = $currentTime->diff($scheduleTime);

                SplitTestingJob::dispatch($user, $request->all(), $splitCampaign->id, $job_code)->delay($totalDuration);
            } elseif ($request->recursive_campaign_type == 3) {
                //Yearly
                $month = config('constants.months_of_year')[$request->month_of_year];
                $day = $request->day_of_month;
                $year = date('Y');

                if ($request->month_of_year > Carbon::now()->month) {
                    $selectedDay = Carbon::parse($month . ' ' . $day . ' ' . $year)->startOfMonth();
                } else {
                    $selectedDay = Carbon::parse($month . ' ' . $day . ' ' . $year)->addMonth(12)->startOfMonth();
                }

                $scheduleTime = Carbon::parse($selectedDay);
                $currentTime = Carbon::parse(now()->format('Y-m-d'));
                $totalDuration = $currentTime->diff($scheduleTime);

                SplitTestingJob::dispatch($user, $request->all(), $splitCampaign->id, $job_code)->delay($totalDuration);
            }
        }

        return response()->json(['_id' => $this->encodeId($splitCampaign->id), 'message' => 'Split Campaign ' . $done, "messageType" => "success", 'status' => 1], 200);
    }

    /**
     * Display the specified Split.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function splitShow(Request $request, $id)
    {
        $id = $this->decodeId($id);
        $user = $request->get('user');
        $splitCampaign = EmailCampaign::where('id', $id)->where('is_split_testing', 1)->first();

        if ($splitCampaign && $splitCampaign->user_id == $user->id) {
            return ECampaignResource::collection([$splitCampaign])[0]
                ->additional([
                    'message' => 'Campaign Fetched', "messageType" => "success", 'status' => 1
                ]);
        }
        return response()->json(['errors' =>  ['campaign' => ['Campaign not found!']], "messageType" => "error", 'status' => 0], 404);
    }

    /**
     * Remove the specified Split from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function splitDestroy(Request $request, $id)
    {
        $id = $this->decodeId($id);
        $user = $request->get('user');
        $found = EmailCampaign::where('id', $id)->where('is_split_testing', 1)->where('status', 2)->first();
        if ($found && $found->user_id == $user->id) {
            if ($found->status == 2) {
                $found->delete();
                User_log::create([
                    'user_id' => $user->id,
                    'item_id' => $found->id,
                    'log_type' => 1,
                    'module' => 8,
                ]);
                return response()->json(['message' => "Campaign Deleted successfully", "messageType" => "success", 'status' => 1], 200);
            }
            return response()->json(['errors' =>  ['campaign' => ['Campaign can only be deleted before it is sent or delivered!']], "messageType" => "error", 'status' => 0], 404);
        }
        return response()->json(['errors' =>  ['campaign' => ['Campaign not found or cannot be deleted!']], "messageType" => "error", 'status' => 0], 404);
    }

    /**
     * Display a listing of the report of Split.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function splitReport(Request $request, $id)
    {
        $user = $request->get('user');
        //
        $id = $this->decodeId($id);
        $SplitCampaign = EmailCampaign::where('id', $id)->where('is_split_testing', 1)->whereIn('status', [4, 5, 6])->first();
        if ($SplitCampaign && $SplitCampaign->user_id == $user->id) {
            $reports = CampaignHistory::where('type', 2)->where('campaign_id', $id)->with('sent_to')->paginate(20);
            $send = [];
            foreach ($reports as $repo) {
                $history = $repo->id;
                $clicks1 = $opens1 = $clicks2 = $opens2 = 0;
                // get contacts and divide into two groups.
                $logs = EmailCampaignLogs::where('campaign_id', $SplitCampaign->id)->get();
                if ($logs->count())
                    if ($logs[0]->content_id) {
                        // by content
                        // get unique content ids
                        $cont_ids = $logs->pluck('content_id')->toArray();
                        $contents = array_unique($cont_ids);
                        if ($contents) {
                            $sub_contacts1 = $logs->where('content_id', $contents[0])->pluck('contact_id')->toArray();
                            $clicks1 = EmailCampaignClick::where('history_id', $history)->whereIn('contact_id', $sub_contacts1)->count();
                            // $clicks1 = EmailCampaignClick::where('campaign_id', $EmailCampaign->id)->whereIn('contact_id', $sub_contacts1)->count();
                            $opens1 = EmailCampaignOpen::where('history_id', $history)->whereIn('contact_id', $sub_contacts1)->count();
                            // $opens1 = EmailCampaignOpen::where('campaign_id', $EmailCampaign->id)->whereIn('contact_id', $sub_contacts1)->count();
                            $contacts_in_span1 = Contact::whereIn('id', $sub_contacts1)->get();
                            $contacts1 = ContactResource::collection($contacts_in_span1);

                            if (isset($contents[1])) {
                                $sub_contacts2 = $logs->where('content_id', $contents[1])->pluck('contact_id')->toArray();
                                $clicks2 = EmailCampaignClick::where('history_id', $history)->whereIn('contact_id', $sub_contacts2)->count();
                                // $clicks2 = EmailCampaignClick::where('campaign_id', $EmailCampaign->id)->whereIn('contact_id', $sub_contacts2)->count();
                                $opens2 = EmailCampaignOpen::where('history_id', $history)->whereIn('contact_id', $sub_contacts2)->count();
                                // $opens2 = EmailCampaignOpen::where('campaign_id', $EmailCampaign->id)->whereIn('contact_id', $sub_contacts2)->count();
                                $contacts_in_span2 = Contact::whereIn('id', $sub_contacts2)->get();
                                $contacts2 = ContactResource::collection($contacts_in_span2);
                            } else {
                                // stays 0
                            }
                        } else {
                            // both stays 0
                        }
                    } else {
                        // by subject
                        // get unique subject ids
                        $sub_ids = $logs->pluck('subject_id')->toArray();
                        $uniquesubjects = array_unique($sub_ids);
                        $subjects = [];
                        foreach ($uniquesubjects as $sub) {
                            array_push($subjects, $sub);
                        }
                        if ($subjects) {
                            $sub_contacts1 = $logs->where('subject_id', $subjects[0])->pluck('contact_id')->toArray();
                            $clicks1 = EmailCampaignClick::where('history_id', $history)->whereIn('contact_id', $sub_contacts1)->count();
                            // $clicks1 = EmailCampaignClick::where('campaign_id', $EmailCampaign->id)->whereIn('contact_id', $sub_contacts1)->count();
                            $opens1 = EmailCampaignOpen::where('history_id', $history)->whereIn('contact_id', $sub_contacts1)->count();
                            // $opens1 = EmailCampaignOpen::where('campaign_id', $EmailCampaign->id)->whereIn('contact_id', $sub_contacts1)->count();
                            $contacts_in_span1 = Contact::whereIn('id', $sub_contacts1)->get();
                            $contacts1 = ContactResource::collection($contacts_in_span1);

                            if (isset($subjects[1])) {
                                $sub_contacts2 = $logs->where('subject_id', $subjects[1])->pluck('contact_id')->toArray();
                                $clicks2 = EmailCampaignClick::where('history_id', $history)->whereIn('contact_id', $sub_contacts2)->count();
                                // $clicks2 = EmailCampaignClick::where('campaign_id', $EmailCampaign->id)->whereIn('contact_id', $sub_contacts2)->count();
                                $opens2 = EmailCampaignOpen::where('history_id', $history)->whereIn('contact_id', $sub_contacts2)->count();
                                // $opens2 = EmailCampaignOpen::where('campaign_id', $EmailCampaign->id)->whereIn('contact_id', $sub_contacts2)->count();
                                $contacts_in_span2 = Contact::whereIn('id', $sub_contacts2)->get();
                                $contacts2 = ContactResource::collection($contacts_in_span2);
                            } else {
                                // stays 0
                            }
                        } else {
                            // both stay 0
                        }
                    }
                $splitData = [['name' => "Section 1", 'Contacts' => $contacts1, 'Open Rate' => $opens1, 'Link Clicks' => $clicks1], ['name' => "Section 2", 'Contacts' => $contacts2, 'Open Rate' => $opens2, 'Link Clicks' => $clicks2]];

                $sendData = [
                    '_id' => \Hashids::encode($repo->id),
                    'campaign_id' => \Hashids::encode($repo->campaign_id),
                    'success' => $repo->type == 1 ? $repo->sms_success->count() : $repo->success->count(),
                    'fail' => $repo->type == 1 ? $repo->sms_fail->count() : $repo->fail->count(),
                    // 'sent_to' => ContactResource::collection($repo->type == 1 ? $repo->sms_sent_to : $repo->sent_to),
                    'started_at' => $repo->created_at,
                    'completed_at' => $repo->updated_at,
                ];
                $sendData['splitReport'] = $splitData;
                array_push($send, $sendData);
            }
            // return CampaignHistoryResource::collection($reports)->additional([
            //     'name' => $SplitCampaign->name,
            //     'type' => $SplitCampaign->campaign_type == 3 ? "Recursive" : "OneTime",
            //     'message' => 'Split Campaign Report',
            //     "messageType" => "success",
            //     'status' => 1,
            // ]);
            return response()->json(['message' => "Split report fetched", "messageType" => "success", 'status' => 1, 'report' => $send], 200);
        }
        return response()->json(['errors' =>  ['campaign' => ['Campaign not found!']], "messageType" => "error", 'status' => 0], 404);
    }

    // ==================================================== //
    // ======== Email and Split common FUNCTIONS ========== //
    // ==================================================== //

    /**
     * Stop a specified email and Split campaigns.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function emailStop(Request $request, $id)
    {
        $user = $request->get('user');
        $id = Hashids::decode($id);
        $EmailCampaign = EmailCampaign::where('id', $id)->first();
        if ($EmailCampaign && $EmailCampaign->user_id == $user->id) {

            if ($EmailCampaign->status != 1 && $EmailCampaign->status != 4) {
                return response()->json(['errors' =>  ['campaign' => ['Campaign can not be stopped!']], "messageType" => "error", 'status' => 0], 404);
            }
            if ($EmailCampaign->status == 3 || $EmailCampaign->status == 6) {
                return response()->json(['errors' =>  ['campaign' => ['Campaign already stopped!']], "messageType" => "error", 'status' => 0], 404);
            }

            // create a unique job code
            do {
                $job_code = Str::random(25);
            } while (EmailCampaign::where("job_code", $job_code)->first() instanceof EmailCampaign);

            $contacts = [];
            if ($EmailCampaign->group_ids && count(json_decode($EmailCampaign->group_ids)) != 0) {
                $groups = Group::whereIn('id', array_column(json_decode($EmailCampaign->group_ids), 'id'))->with('contacts')->get();
                foreach ($groups as $group) {
                    // $contacts = array_merge($contacts, $group->contacts->pluck('id')->toArray());
                    $col = array_search($group->id, array_column(json_decode($EmailCampaign->group_ids), 'id'));
                    $pivotid = array_column(json_decode($EmailCampaign->group_ids), 'last')[$col];
                    $grp_contacts = $group->contacts()->withPivot('id')->wherePivot('id', '<=', $pivotid)->get()->toArray();
                    $contacts = array_merge($contacts, array_column($grp_contacts, 'id'));
                }
            }
            // $includes = CampaignContact::where('type', 2)->where('campaign_id', $id)->pluck('contact_id')->toArray();
            $includes = CampaignContact::where('type', 2)->where('campaign_id', $id)->where('id', '<=', $EmailCampaign->group_id)->pluck('contact_id')->toArray();
            $allcontacts = array_unique(array_merge($contacts, $includes));
            $contacts = Contact::whereIn('id', $allcontacts)->get()->toArray();
            $excluding = CampaignExclude::where('type', 2)->where('campaign_id', $id)->get()->count();

            if ($EmailCampaign->campaign_type == 3) {
                $total_contacts = (count($contacts) - $excluding) * $EmailCampaign->no_of_time;
                $history = CampaignHistory::where('type', 2)->where('campaign_id', $EmailCampaign->id)->first();
                if ($history) {
                    $EmailCampaign->update(['status' => 6, 'sending_stopped_at' => now(), 'job_code' => $job_code]);
                    // saving stopped history
                    User_log::create([
                        'user_id' => $EmailCampaign->user_id,
                        'item_id' => $EmailCampaign->id,
                        'module' => 4,
                        'log_type' => 11,
                    ]);
                    $message = "Stopped";
                    // return response("Campaign Stopped", 200);
                } else {
                    $EmailCampaign->update(['status' => 3, 'sending_stopped_at' => now(), 'job_code' => $job_code]);
                    $message = "Disabled";
                }
            } else {
                $EmailCampaign->update(['status' => 3, 'sending_stopped_at' => now(), 'job_code' => $job_code]);
                $total_contacts = count($contacts) - $excluding;
                $message = "Disabled";
            }

            if ($user->package_subscription_id) {
                $subscription = PackageSubscription::where('id', $user->package_subscription_id)->where('is_active', 1)->first();
                if ($subscription && $subscription->package_id != 9) {
                    // sub from to used emails
                    if ($subscription->email_used - $total_contacts < 0)
                        $val = 0;
                    else
                        $val = $subscription->email_used - $total_contacts;
                    $subscription->update(['email_used' => $val]);
                } else if ($subscription && $subscription->package_id == 9 && $EmailCampaign->created_at->startOfWeek()->format('Y-m-d') ==  now()->startOfWeek()->format('Y-m-d')) {
                    // add to used emails
                    $email_span1_price = PackageSettingsValue(1, 'price_without_vat');

                    if ($subscription->email_used - $total_contacts < 0)
                        $val = 0;
                    else
                        $val = $subscription->email_used - $total_contacts;
                    $subscription->update(['email_used' => $val]);
                    $subscription->emails_paying_for -= $total_contacts;
                    if ($subscription->emails_paying_for < 0)
                        $subscription->emails_paying_for = 0;

                    $subscription->emails_to_pay -= ($email_span1_price * $total_contacts);

                    if ($subscription->emails_to_pay < 0)
                        $subscription->emails_to_pay = 0;
                    $subscription->save();
                }
            }
            return response()->json(['message' => $message, "messageType" => "success", 'status' => 1], 200);
        }
        return response()->json(['errors' =>  ['campaign' => ['Campaign not found!']], "messageType" => "error", 'status' => 0], 404);
    }

    // =================================== //
    // ======== RES FUNCTIONS ========== //
    // =================================== //

    /**
     * Display a listing of the res.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function Index(Request $request)
    {
        $user = $request->get('user');
        //
        return response()->json($user, 200);
    }

    /**
     * Store a newly created res in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function Store(Request $request)
    {
        $user = $request->get('user');
        //
        return response()->json($user, 200);
    }

    /**
     * Display the specified res.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function Show(Request $request, $id)
    {
        $user = $request->get('user');
        $id = $this->decodeId($id);
        //
        return response()->json($user, 200);
    }

    /**
     * Update the specified res in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function Update(Request $request, $id)
    {
        $user = $request->get('user');
        $id = $this->decodeId($id);
        //
        return response()->json($user, 200);
    }

    /**
     * Remove the specified res from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function Destroy(Request $request, $id)
    {
        $user = $request->get('user');
        $id = $this->decodeId($id);
        //
        return response()->json($user, 200);
    }
}
