<?php

namespace App\Http\Controllers\Api;

use App\CustomClasses\TranslationHandler;
use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\User;
use App\Http\Controllers\MailController;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ContactsImport;
use App\Exports\ContactsExport;
use Illuminate\Validation\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ContactResource;
use App\Imports\ContactsImportCheck;
use App\Models\Group;
use App\Models\Contact_group;
use App\Models\Notification;
use Hashids;
use App\Jobs\SendMail;
use App\Models\Admin\Package;
use App\Models\Admin\PackageLinkFeature;
use App\Models\CampaignContact;
use App\Models\EmailCampaign;
use App\Models\PayAsYouGoPayments;
use App\Models\SmsCampaign;
use App\Models\User_log;
use Carbon\Carbon;
use GuzzleHttp\Psr7\Message;

class ContactController extends Controller
{

    /**
     * Create a new Contact instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function checkFileImport(Request $request)
    {
        $messages = [
            'file.required' => TranslationHandler::getTranslation($request->lang, 'required')
        ];
        $validator = $request->validate(
            [
                'file' => ['required', 'mimes:csv,txt,xlsx,xls'],
            ],
            $messages
        );

        $import = new ContactsImportCheck;

        $response = Excel::import($import, $request->file('file')->store('temp'));
        if ($import->hasErrors())
            return response(['message' => TranslationHandler::getTranslation($request->lang, 'invalid_data'), 'errors' => ['file' => $import->getErrors()],], 422);

        if ($import->getfields() == NULL)
            return response(['message' => TranslationHandler::getTranslation($request->lang, 'invalid_data'), 'errors' => ['file' => [TranslationHandler::getTranslation($request->lang, 'invalid_data')]],], 422);

        return response(['message' => "The given file has the following fields", 'fields' => $import->getfields()], 200);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function fileImport(Request $request)
    {

        $messages = [
            'file.required' => TranslationHandler::getTranslation($request->lang, 'required'),
            'required' => TranslationHandler::getTranslation($request->lang, 'required'),
            'string' => TranslationHandler::getTranslation($request->lang, 'required'),
            'email.string' => TranslationHandler::getTranslation($request->lang, 'required'),
            'first_name.*.max' => TranslationHandler::getTranslation($request->lang, 'max_35'),
            'last_name.*.max' => TranslationHandler::getTranslation($request->lang, 'max_35'),
            'email.max' => TranslationHandler::getTranslation($request->lang, 'max_35'),
            'email.*.regex' => TranslationHandler::getTranslation($request->lang, 'valid_email'),
            'number.*.required' => TranslationHandler::getTranslation($request->lang, 'required'),
            'number.*.regex' => TranslationHandler::getTranslation($request->lang, 'number_regex'),
            'number.*.max' => TranslationHandler::getTranslation($request->lang, 'number_invalid'),
        ];

        $validator = $request->validate(
            [
                'file' => ['required', 'mimes:csv,txt,xlsx,xls'],
                'first_name' => ['required'],
                'last_name' => ['required'],
                'for_sms' => ['required'],
                'for_email' => ['required'],
                'email' => ['required'],
                'number' => ['required'],
            ],
            $messages
        );

        $import = new ContactsImport($request->all(), $request);
        $contacts = Excel::import($import, $request->file('file')->store('temp'));

        if ($import->getImported()) {
            return ContactResource::collection($import->getImported())
                ->additional([
                    'message' => 'Contacts',
                    'status' => 1,
                ]);
        }
        return response(['message' => TranslationHandler::getTranslation($request->lang, 'invalid_data'), 'errors' => ['invalid_or_limit' => [TranslationHandler::getTranslation($request->lang, "Invalid data or limit exceeded.")]]], 422);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function fileExport(Request $request)
    {
        return Excel::download(new ContactsExport($request), 'contacts-collection.xlsx');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $id = auth()->user()->id;
        $query = Contact::query()->where('user_id', $id);

        if ($s = $request->input('type')) {
            if ($s == 1)
                $query->where('for_sms', 1);
            else if ($s == 2)
                $query->where('for_email', 1);
        }
        if ($s = $request->input('name')) {
            $query->whereRaw('first_name LIKE "%' . $s . '%" ')->orwhereRaw('last_name LIKE "%' . $s . '%" ');
        }
        if ($s = $request->input('email')) {
            $query->whereRaw('email LIKE "%' . $s . '%" ');
        }
        if ($s = $request->input('number')) {
            $query->whereRaw('number LIKE "%' . $s . '%" ');
        }
        if ($s = $request->input('created')) {
            $query->whereRaw('created_at LIKE "%' . $s . '%" ');
        }
        if ($s = $request->input('updated')) {
            $query->whereRaw('updated_at LIKE "%' . $s . '%" ');
        }

        $contacts = $query->where('user_id', $id)->with('groups')->orderBy('created_at', 'DESC')->paginate(10);
        return ContactResource::collection($contacts)
            ->additional([
                'message' => 'Contacts',
                'status' => 1,
            ]);
    }


    // Single contact storing is not being used.
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // public function store(Request $request)
    // {
    //     $data = $request->validate([
    //         'first_name' => ['required', 'string', 'max:35'],
    //         'last_name' => ['required', 'string', 'max:35'],
    //         'for_sms' => ['boolean'],
    //         'for_email' => ['boolean'],
    //     ]);

    //     $final = array_merge($request->all(), ['user_id' => Auth()->user()->id]);

    //     if ($request->for_sms) {
    //         $request->validate([
    //             'number' => ['required', 'regex:/(\+)([1-9]{2})(\d{10})/'],
    //         ]);
    //     }
    //     if ($request->for_email) {
    //         // check email fields
    //         $request->validate([
    //             'email' => ['required', 'string', 'regex:/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/', 'max:65'],
    //         ]);
    //         $find = Contact::where('user_id', Auth()->user()->id)->where('email', $request->email)->first();
    //         if ($find) {
    //             return response(['message' => TranslationHandler::getTranslation($request->lang, 'invalid_data'), 'errors' => ['email' => ['Email already exists']],], 422);
    //         }
    //     }
    //     if (!$request->for_sms && !$request->for_email) {
    //         // if both are unchecked
    //         return response(['message' => TranslationHandler::getTranslation($request->lang, 'invalid_data'), 'errors' => ['for' => [TranslationHandler::getTranslation($request->lang, "please_select_for_email_or_sms")]],], 422);
    //     }

    //     $contact = Contact::create($final);
    //     User_log::create([
    //         'user_id' => auth()->user()->id,
    //         'item_id' => $contact->id,
    //         'log_type' => 1,
    //         'module' => 6,
    //     ]);

    //     // add the contact to default group
    //     // --------------------------------
    //     // $default = Group::where('user_id', auth()->user()->id)->where('name', 'default')->first();
    //     // if ($default) {
    //     //     $data = ['contact_id' => $contact->id, 'group_id' => $default->id, 'user_id' => auth()->user()->id];
    //     //     $cg = Contact_group::create($data);
    //     //     User_log::create([
    //     //         'user_id' => auth()->user()->id,
    //     //         'item_id' => $cg->id,
    //     //         'log_type' => 5,
    //     //         'module' => 6,
    //     //     ]);
    //     // }
    //     return ContactResource::collection([$contact])
    //         ->additional([
    //             'message' => 'Contact',
    //             'status' => 1,
    //         ]);
    // }

    /**
     * Store a many created resources in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeMany(Request $request)
    {
        // validation
        // ----------
        $messages = [
            'required' => TranslationHandler::getTranslation($request->lang, 'required'),
            'string' => TranslationHandler::getTranslation($request->lang, 'required'),
            'email.string' => TranslationHandler::getTranslation($request->lang, 'required'),
            'first_name.*.max' => TranslationHandler::getTranslation($request->lang, 'max_35'),
            'last_name.*.max' => TranslationHandler::getTranslation($request->lang, 'max_35'),
            'email.max' => TranslationHandler::getTranslation($request->lang, 'max_35'),
            'email.*.regex' => TranslationHandler::getTranslation($request->lang, 'valid_email'),
            'number.*.required' => TranslationHandler::getTranslation($request->lang, 'required'),
            'number.*.regex' => TranslationHandler::getTranslation($request->lang, 'number_regex'),
            'number.*.max' => TranslationHandler::getTranslation($request->lang, 'number_invalid'),
        ];
        $data = $request->validate([
            'first_name' => ['required', 'array'],
            'last_name' => ['required', 'array'],
            'for_email' => ['required', 'array'],
            'for_sms' => ['required', 'array'],

            'first_name.*' => ['required', 'string', 'max:35'],
            'last_name.*' => ['required', 'string', 'max:35'],
            'for_email.*' => ['boolean'],
            'for_sms.*' => ['boolean'],
        ], $messages);

        $should_have_emails = count(array_filter($request->for_email, function ($a) {
            return $a == 1;
        }));
        $should_have_sms = count(array_filter($request->for_sms, function ($a) {
            return $a == 1;
        }));
        if ($should_have_emails + $should_have_sms  == 0) {
            return response(['message' => "Please select for Email or SMS", 'errors' => ['error_message' => [TranslationHandler::getTranslation($request->lang, "please_select_for_email_or_sms")]],], 422);
        }

        $empty_emails = count(array_filter(array_unique($request->for_email), function ($a) {
            return $a == '';
        }));
        $empty_sms = count(array_filter(array_unique($request->for_sms), function ($a) {
            return $a == '';
        }));
        if (count(array_unique($request->number)) - $empty_sms != $should_have_sms || count(array_unique($request->email)) - $empty_emails != $should_have_emails) {
            // number or email is repeated somewhere
            return response(['message' => TranslationHandler::getTranslation($request->lang, 'invalid_data'), 'errors' => ['error_message' => [TranslationHandler::getTranslation($request->lang, "values_are_repeating_please_make_sure_each_contact_has_unique_email_and_number")]],], 422);
        }

        // checking user package limits
        // if (Auth()->user()->package_id && Auth()->user()->package_id != 9) {
        //     $package = Package::where('id', Auth()->user()->package_id)->where('status', 1)->orderBy('monthly_price')->first();
        //     if ($package) {
        //         $hasContacts = Contact::where('user_id', Auth()->user()->id)->count();
        //         $foundfeature = PackageLinkFeature::where('package_id', $package->id)->where('feature_id', 3)->first();
        //         if ($foundfeature) {
        //             $limit = $foundfeature->count;
        //             $addingmore = sizeof($request->first_name);
        //             $addingContacts = $addingmore + $hasContacts;
        //             if ($addingContacts > $limit) {
        //                 return response(['message' => TranslationHandler::getTranslation($request->lang, 'package_limit_ecceed_contact'), 'code' => 1, 'errors' => ['error_message' => [TranslationHandler::getTranslation($request->lang, 'limit_exceeded')]],], 422);
        //             }
        //         }
        //     }
        // }
        // checking user package limits end

        for ($i = 0; $i < sizeof($request->first_name); $i++) {
            if ($request->for_sms[$i]) {
                // check sms fields
                $request->validate([
                    'number' => ['required', 'array',],
                    'number.' . $i => ['required', 'regex:/(\+)([1-9]{2})([0-9]{10})/', 'max:13'],
                ], $messages);
                $find = Contact::where('user_id', Auth()->user()->id)->where('number', $request->number[$i])->first();
                if ($find) {
                    return response(['message' => TranslationHandler::getTranslation($request->lang, 'invalid_data'), 'errors' => ['number.' . $i => ['Number already exists']],], 422);
                }
            }
            if ($request->for_email[$i]) {
                // check email fields
                $request->validate([
                    'email' => ['required', 'array',],
                    'email.' . $i => ['required', 'string', 'regex:/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/', 'max:65'],
                ], $messages);
                $find = Contact::where('user_id', Auth()->user()->id)->where('email', $request->email[$i])->first();
                if ($find) {
                    return response(['message' => TranslationHandler::getTranslation($request->lang, 'invalid_data'), 'errors' => ['email.' . $i => ['Email already exists']],], 422);
                }
            }
            if (!$request->for_sms[$i] && !$request->for_email[$i]) {
                // if both are unchecked
                return response(['message' => TranslationHandler::getTranslation($request->lang, 'invalid_data'), 'errors' => ['for.' . $i => [TranslationHandler::getTranslation($request->lang, "please_select_for_email_or_sms")]],], 422);
            }
        }

        // for creating if validated.
        // --------------------------
        $contacts = [];
        for ($i = 0; $i < sizeof($request->first_name); $i++) {
            $final = array_merge(['user_id' => Auth()->user()->id, 'first_name' => $request->first_name[$i], 'last_name' => $request->last_name[$i], 'email' => $request->email[$i], 'country_code' => 00, 'number' => $request->number[$i], 'for_sms' => $request->for_sms[$i], 'for_email' => $request->for_email[$i]]);
            $con = Contact::create($final);
            $hasContacts = Contact::where('user_id', auth()->user()->id)->count();
            if (Auth()->user()->package && Auth()->user()->package_id == 9) {
                // $contacts_span1_start = PackageSettingsValue(7, 'start_range');
                // $contacts_span1_end = PackageSettingsValue(7, 'end_range');
                // $contacts_span1_price = PackageSettingsValue(7, 'price_without_vat');
                // $contacts_span2_start = PackageSettingsValue(8, 'start_range');
                // $contacts_span2_end = PackageSettingsValue(8, 'end_range');
                // $contacts_span2_price = PackageSettingsValue(8, 'price_without_vat');
                // $contacts_span3_start = PackageSettingsValue(9, 'start_range');
                // $contacts_span3_price = PackageSettingsValue(9, 'price_without_vat');
                // Auth()->user()->subscription->contacts_paying_for += 1;
                // if ($hasContacts >= $contacts_span1_start && $hasContacts <= $contacts_span1_end) {
                //     Auth()->user()->subscription->contacts_to_pay += $contacts_span1_price;
                // } else if ($hasContacts >= $contacts_span2_start && $hasContacts <= $contacts_span2_end) {
                //     Auth()->user()->subscription->contacts_to_pay += $contacts_span2_price;
                // } else if ($hasContacts >= $contacts_span3_start) {
                //     Auth()->user()->subscription->contacts_to_pay += $contacts_span3_price;
                // }
                // Auth()->user()->subscription->save();
                // check payment Relief
                $last_payment = PayAsYouGoPayments::where('package_subscription_id', Auth()->user()->subscription->id)->where('status', '!=', 1)->first();
                if ($last_payment && isset($last_payment->timestamp)) {
                    $last_payment = $last_payment->timestamp;
                    $payment_relief_days = (int)settingValue('payment_relief_days');
                    $releif_timestamp = Carbon::now('UTC')->subDays($payment_relief_days)->timestamp;
                    if ($last_payment < $releif_timestamp) {
                        return response(['message' => TranslationHandler::getTranslation($request->lang, 'deactivating_account'), 'code' => 1, 'errors' => ['error_message' => [TranslationHandler::getTranslation($request->lang, 'package_unpaid')]],], 422);
                        Auth()->user()->update([
                            'status' => 0
                        ]);
                    }
                }
            }
            array_push($contacts, $con);
            $add = 0;
            // add the contact to default group or selected list
            // --------------------------------
            if ($request->list) {
                $list = Group::where('user_id', auth()->user()->id)->where('id', Hashids::decode($request->list)[0])->first();
                if ($list && (($con->for_sms == 1 && $list->for_sms == 1) || ($con->for_email == 1 && $list->for_email == 1))) {
                    $find = Contact_group::where('deleted_at', null)->where('user_id', auth()->user()->id)->where('group_id', $list->id)->where('contact_id', $con->id)->first();
                    if ($find) {
                        return response("Already in group", 304);
                    }
                    $data = ['contact_id' => $con->id, 'group_id' => $list->id, 'user_id' => auth()->user()->id];
                    $cg = Contact_group::create($data);
                    User_log::create([
                        'user_id' => auth()->user()->id,
                        'item_id' => $cg->id,
                        'log_type' => 1,
                        'module' => 6,
                    ]);
                } else
                    $add = 1;
            } else
                $add = 1;

            // $default = Group::where('user_id', auth()->user()->id)->where('name', 'default')->first();
            // if ($add && $default) {
            //     $data = ['contact_id' => $con->id, 'group_id' => $default->id, 'user_id' => auth()->user()->id];
            //     $cg = Contact_group::create($data);
            //     User_log::create([
            //         'user_id' => auth()->user()->id,
            //         'item_id' => $cg->id,
            //         'log_type' => 1,
            //         'module' => 6,
            //     ]);
            // }
        }

        return ContactResource::collection($contacts)
            ->additional([
                'message' => 'Contacts',
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
        $contact = Contact::where('user_id', auth()->user()->id)->where('id', $id)->first();
        if ($contact && $contact->user_id == auth()->user()->id)
            return ContactResource::collection([$contact])
                ->additional([
                    'message' => 'Contact',
                    'status' => 1,
                ]);

        return response("Contact not found", 404);
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
            'email.string' => TranslationHandler::getTranslation($request->lang, 'required'),
            'first_name.*.max' => TranslationHandler::getTranslation($request->lang, 'max_35'),
            'last_name.*.max' => TranslationHandler::getTranslation($request->lang, 'max_35'),
            'email.max' => TranslationHandler::getTranslation($request->lang, 'max_35'),
            'email.*.regex' => TranslationHandler::getTranslation($request->lang, 'valid_email'),
            'number.*.required' => TranslationHandler::getTranslation($request->lang, 'required'),
            'number.*.regex' => TranslationHandler::getTranslation($request->lang, 'number_regex'),
            'number.*.max' => TranslationHandler::getTranslation($request->lang, 'number_invalid'),
        ];
        $data = $request->validate([
            'first_name' => ['string', 'max:35'],
            'last_name' => ['string', 'max:35'],
            // 'country_code' => ['integer', 'digits_between:2,5'],
            'number' => ['regex:/(\+)([1-9]{2})(\d{10})/'],
            'email' => ['string', 'regex:/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/', 'max:65'],
        ], $messages);

        $id = Hashids::decode($id)[0];

        if ($request->for_sms) {
            // check sms fields
            $request->validate([
                // 'country_code' => ['required', 'integer', 'digits_between:2,5'],
                'number' => ['required', 'regex:/(\+)([1-9]{2})(\d{10})/'],
            ]);
        }
        if ($request->for_email) {
            // check email fields
            $request->validate([
                'email' => ['required', 'string', 'regex:/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/', 'max:65'],
            ]);
        }
        if (!$request->for_sms && !$request->for_email) {
            // if both are unchecked
            return response(['message' => TranslationHandler::getTranslation($request->lang, 'invalid_data'), 'errors' => ['for' => [TranslationHandler::getTranslation($request->lang, "please_select_for_email_or_sms")]],], 422);
        }


        $contact = Contact::where('id', $id)->first();
        $contact->update($request->all());
        User_log::create([
            'user_id' => auth()->user()->id,
            'item_id' => $contact->id,
            'log_type' => 1,
            'module' => 7,
        ]);

        // add the contact to a selected list
        // ----------------------------------
        if ($request->list) {
            $list = Group::where('user_id', auth()->user()->id)->where('id', Hashids::decode($request->list)[0])->first();
            if ($list && (($contact->for_sms == 1 && $list->for_sms == 1) || ($contact->for_email == 1 && $list->for_email == 1))) {
                $find = Contact_group::where('deleted_at', null)->where('user_id', auth()->user()->id)->where('group_id', $list->id)->where('contact_id', $contact->id)->first();
                if (!$find) {
                    $data = ['contact_id' => $contact->id, 'group_id' => $list->id, 'user_id' => auth()->user()->id];
                    $cg = Contact_group::create($data);
                    User_log::create([
                        'user_id' => auth()->user()->id,
                        'item_id' => $cg->id,
                        'log_type' => 1,
                        'module' => 6,
                    ]);
                }
            }
        }

        return ContactResource::collection([$contact])
            ->additional([
                'message' => 'Contact',
                'status' => 1,
            ]);
    }

    public function updateMany(Request $request)
    {
        $messages = [
            'required' => TranslationHandler::getTranslation($request->lang, 'required'),
            'string' => TranslationHandler::getTranslation($request->lang, 'required'),
            'email.string' => TranslationHandler::getTranslation($request->lang, 'required'),
            'first_name.*.max' => TranslationHandler::getTranslation($request->lang, 'max_35'),
            'last_name.*.max' => TranslationHandler::getTranslation($request->lang, 'max_35'),
            'email.max' => TranslationHandler::getTranslation($request->lang, 'max_65'),
            'email.*.email' => TranslationHandler::getTranslation($request->lang, 'valid_email'),
            'number.*.required' => TranslationHandler::getTranslation($request->lang, 'required'),
            'number.*.regex' => TranslationHandler::getTranslation($request->lang, 'number_regex'),
            'number.*.max' => TranslationHandler::getTranslation($request->lang, 'number_invalid'),
        ];
        $data = $request->validate([
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
            // 'number.*' => ['regex:/(\+)([1-9]{2})(\d{10})/', 'max:13'],
            // 'email.*' => ['string', 'email', 'max:65'],
            'for_email.*' => ['boolean'],
            'for_sms.*' => ['boolean'],
        ], $messages);

        $contacts = [];
        for ($i = 0; $i < sizeof($request->id); $i++) {
            $final = array_merge(['user_id' => Auth()->user()->id, 'first_name' => $request->first_name[$i], 'last_name' => $request->last_name[$i], 'email' => $request->email[$i], 'country_code' => 00, 'number' => $request->number[$i], 'for_sms' => $request->for_sms[$i], 'for_email' => $request->for_email[$i]]);
            $id = Hashids::decode($request->id[$i])[0];
            $contact = Contact::where('id', $id)->first();

            if ($request->for_sms[$i]) {
                // check sms fields
                $request->validate([
                    'number' => ['required', 'array',],
                    'number.' . $i => ['required', 'regex:/(\+)([1-9]{2})(\d{10})/'],
                ], $messages);
                $find = Contact::where('user_id', Auth()->user()->id)->where('number', $request->number[$i])->first();
                if ($find && $find->id != $contact->id) {
                    return response(['message' => TranslationHandler::getTranslation($request->lang, 'invalid_data'), 'errors' => ['number.' . $i => ['Number already exists']],], 422);
                }
            }
            if ($request->for_email[$i]) {
                // check email fields
                $request->validate([
                    'email' => ['required', 'array',],
                    'email.' . $i => ['required', 'string', 'email', 'max:65'],
                ], $messages);
                $find = Contact::where('user_id', Auth()->user()->id)->where('email', $request->email[$i])->first();
                if ($find && $find->id != $contact->id) {
                    return response(['message' => TranslationHandler::getTranslation($request->lang, 'invalid_data'), 'errors' => ['email.' . $i => ['Email already exists']],], 422);
                }
            }
            if (!$request->for_sms[$i] && !$request->for_email[$i]) {
                // if both are unchecked
                return response(['message' => TranslationHandler::getTranslation($request->lang, 'invalid_data'), 'errors' => ['for.' . $i => [TranslationHandler::getTranslation($request->lang, "please_select_for_email_or_sms")]],], 422);
            }

            $contact->update($final);
            array_push($contacts, $contact);
            User_log::create([
                'user_id' => auth()->user()->id,
                'item_id' => $contact->id,
                'log_type' => 1,
                'module' => 7,
            ]);


            // add the contact to a selected list
            // ----------------------------------
            if ($request->list) {
                $list = Group::where('user_id', auth()->user()->id)->where('id', Hashids::decode($request->list)[0])->first();
                if ($list && (($contact->for_sms == 1 && $list->for_sms == 1) || ($contact->for_email == 1 && $list->for_email == 1))) {
                    $find = Contact_group::where('deleted_at', null)->where('user_id', auth()->user()->id)->where('group_id', $list->id)->where('contact_id', $contact->id)->first();
                    if (!$find) {
                        $data = ['contact_id' => $contact->id, 'group_id' => $list->id, 'user_id' => auth()->user()->id];
                        $cg = Contact_group::create($data);
                        User_log::create([
                            'user_id' => auth()->user()->id,
                            'item_id' => $cg->id,
                            'log_type' => 1,
                            'module' => 6,
                        ]);
                    }
                }
            }
        }

        return ContactResource::collection($contacts)
            ->additional([
                'message' => 'Contacts',
                'status' => 1,
            ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $id = Hashids::decode($id)[0];
        $contact = Contact::where('id', $id)->first();
        if ($contact) {
            $contact->delete();
            User_log::create([
                'user_id' => auth()->user()->id,
                'item_id' => $contact->id,
                'log_type' => 1,
                'module' => 8,
            ]);
        }
        $response = [
            'message' => "Contact deleted successfully"
        ];

        return response($response, 201);
    }

    public function destroyMany(Request $request)
    {
        $messages = [
            'required' => TranslationHandler::getTranslation($request->lang, 'required'),
            'string' => TranslationHandler::getTranslation($request->lang, 'required'),
        ];
        $data = $request->validate([
            'id' => ['required', 'array'],
            'id.*' => ['required', 'string'],
        ], $messages);

        for ($i = 0; $i < sizeof($request->id); $i++) {
            $id = Hashids::decode($request->id[$i])[0];
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
                    User_log::create([
                        'user_id' => auth()->user()->id,
                        'item_id' => $contact->id,
                        'log_type' => 1,
                        'module' => 8,
                    ]);
                    // $response = [
                    //     'message' => "Contacts deleted successfully"
                    // ];

                    // return response($response, 201);
                }
                // return response(TranslationHandler::getTranslation($request->lang, 'group_in_use'), 409);
            }
        }
        $response = [
            'message' => "Contacts deleted successfully"
        ];

        return response($response, 201);
    }

    public function subscribe($id)
    {
        $id = Hashids::decode($id)[0];
        $contact = Contact::where('id', $id)->first();
        if ($contact) {
            $contact->update(['subscribed' => 1, 'confirmed_at' => now()]);
            $response = [
                'message' => "Contact subscribed successfully"
            ];
            // notifying the user of subscription
            $user = User::where('id', $contact->user_id)->first();
            Notification::create([
                'user_id' => $contact->user_id,
                'item_id' => $contact->id,
                'module' => 1,
                'notification_type' => 1,
                'notification_text' => "your_contact_subscribed '" . $contact->first_name . " " . $contact->last_name . "'",
                'redirect_to' => "/contacts/" . \Hashids::encode($contact->id),
            ]);
            User_log::create([
                'user_id' => $contact->user_id,
                'item_id' => $contact->id,
                'module' => 1,
                'log_type' => 1,
            ]);
            SendMail::dispatch(User::where('id', $contact->user_id)->first()->email, TranslationHandler::getTranslation($user->language, 'Contact') . " " . TranslationHandler::getTranslation($user->language, 'Subscribed'), TranslationHandler::getTranslation($user->language, 'Contact') . " " . $contact->first_name . " " . $contact->last_name . " " . TranslationHandler::getTranslation($user->language, 'Subscribed'));


            return response($response, 201);
        }
    }

    public function unsubscribe($id)
    {
        $id = Hashids::decode($id)[0];
        $contact = Contact::where('id', $id)->first();
        if ($contact) {
            $contact->update(['subscribed' => 0, 'unsubscribed_at' => now()]);
            $response = [
                'message' => "Contact unsubscribed successfully"
            ];

            // notifying the user of subscription
            $user = User::where('id', $contact->user_id)->first();
            Notification::create([
                'user_id' => $contact->user_id,
                'item_id' => $contact->id,
                'module' => 2,
                'notification_type' => 1,
                'notification_text' => "your_contact_unsubscribed '" . $contact->first_name . " " . $contact->last_name . "'",
                'redirect_to' => "/contacts/" . \Hashids::encode($contact->id),
            ]);
            User_log::create([
                'user_id' => $contact->user_id,
                'item_id' => $contact->id,
                'module' => 2,
                'log_type' => 1,
            ]);
            SendMail::dispatch(User::where('id', $contact->user_id)->first()->email, 'Contact Unsubscribed', "Your contact " . $contact->first_name . " " . $contact->last_name . " has Unsubscribed.");


            return response($response, 201);
        }
    }

    // public function confirmed($id)
    // {
    //     if (!auth()->user()) {
    //         return response("",401);
    //     }
    //     Contact::where('id', $id)->first()->update(['confirmed_at' => now()]);
    //     $response = [
    //         'message' => "Contact confirmed successfully"
    //     ];

    //     return response($response, 201);
    // }

    public function activate($id)
    {
        Contact::where('id', $id)->first()->update(['status' => 1]);
        $response = [
            'message' => "Contact is activated successfully"
        ];

        return response($response, 201);
    }

    public function deactivate($id)
    {
        Contact::where('id', $id)->first()->update(['status' => 0]);
        $response = [
            'message' => "Contact is deactivated successfully"
        ];

        return response($response, 201);
    }

    public function contactsInfo()
    {
        $id = auth()->user()->id;
        $myDate = date("Y-m-d", strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . "-1 day"));
        $contacts = Contact::where('user_id', $id)->get();
        $new_time = Contact::where('user_id', $id)->latest()->first();
        if ($new_time) {
            $new_time = $new_time->created_at;
        } else {
            $new_time = 0;
        }
        $new_contacts = $contacts->where('created_at', '>=', $new_time)->where('created_at', '>=', $myDate)->count();
        $subscribed = $contacts->where('subscribed', 1)->count();
        $total_contacts = $contacts->count();
        $deleted_contacts = Contact::onlyTrashed()->where('user_id', $id)->count();

        $response = [
            'subscribed' => $subscribed,
            'deleted' => $deleted_contacts,
            'total' => $total_contacts,
            'new' => $new_contacts,
            'message' => "Contact details fetched"
        ];

        return response($response, 201);
    }

    public function canAddContacts(Request $request)
    {
        $id = auth()->user()->id;
        // checking user package limits
        // if (Auth()->user()->package_id && Auth()->user()->package_id != 9) {
        //     $package = Package::where('id', Auth()->user()->package_id)->where('status', 1)->orderBy('monthly_price')->first();
        //     if ($package) {
        //         $hasContacts = Contact::where('user_id', $id)->count();
        //         $foundfeature = PackageLinkFeature::where('package_id', $package->id)->where('feature_id', 3)->first();
        //         if ($foundfeature) {
        //             $limit = $foundfeature->count;
        //             if ($hasContacts + 1 > $limit) {
        //                 return response(['message' => TranslationHandler::getTranslation($request->lang, 'package_limit_ecceed_contact'), 'code' => 0, 'errors' => ['limit' => [TranslationHandler::getTranslation($request->lang, 'limit_exceeded')]],], 422);
        //             } else {
        //                 return response(['message' => "Can Add", 'contacts' => $limit - $hasContacts, 'code' => 1], 200);
        //             }
        //         }
        //     }
        // }
        return response(['message' => "Can Add", 'contacts' => "∞", 'code' => 1], 200);
    }
}
