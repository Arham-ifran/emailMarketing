<?php

namespace App\Http\Controllers\Api;

use App\CustomClasses\TranslationHandler;
use App\Exports\ReportContactsExport;
use App\Http\Controllers\Controller;
use App\Http\Resources\CampaignHistoryResource;
use App\Http\Resources\ContactResource;
use App\Http\Resources\SmsCampaignPDFResource;
use Illuminate\Http\Request;
use App\Models\SmsCampaign;
use App\Models\SmsCampaignRecursion;
use App\Models\SmsCampaignSchedule;
use App\Models\Contact;
use App\Models\Group;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\SmsCampaignResource;
use Hashids;
use App\Jobs\MessageCampaignJob;
use App\Jobs\SendMail;
use App\Models\Admin\PackageSubscription;
use App\Models\CampaignContact;
use App\Models\CampaignExclude;
use App\Models\CampaignHistory;
use App\Models\PayAsYouGoPayments;
use App\Models\User;
use App\Models\User_log;
use Carbon\Carbon;
use Facade\FlareClient\Report;
use Twilio\TwiML\Voice\Start;
use PDF;
use Twilio\TwiML\Voice\Sms;
//use GuzzleHttp\Client;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class SmsCampaignController extends Controller
{
    /**
     * Create a new SmsCampaign instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth:api');
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $id = auth()->user()->id;
        $query = SmsCampaign::query()->where('user_id', $id);

        if ($s = $request->input('name')) {
            $query->whereRaw('name LIKE "%' . $s . '%" ');
        }
        if ($s = $request->input('message')) {
            $query->whereRaw('message LIKE "%' . $s . '%" ');
        }
        if ($s = $request->input('created')) {
            $query->whereRaw('created_at LIKE "%' . $s . '%" ');
        }
        if ($s = $request->input('updated')) {
            $query->whereRaw('updated_at LIKE "%' . $s . '%" ');
        }
        if ($s = $request->input('status')) {
            $query->whereRaw('status LIKE "%' . $s . '%" ');
        }

        $SmsCampaigns = $query->where('user_id', $id)->orderBy('created_at', 'DESC')->paginate(10);
        return SmsCampaignResource::collection($SmsCampaigns)
            ->additional([
                'message' => 'SMS Campaign',
                'status' => 1,
            ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // takes an old or creates new campaign
    public function store(Request $request)
    {
        $user = auth()->user();
        $input = $request->all();
        $campaign_id = '';

        if (isset($input['campaign_id'])) {
            $campaign_id = Hashids::decode($input['campaign_id'])[0];
        }

        if ($input["campaign_status"] == 1 || $input["campaign_testing"]) {

            $messages = [
                'name.required' => TranslationHandler::getTranslation($request->lang, 'required'),
                'message.required' => TranslationHandler::getTranslation($request->lang, 'required'),
                'sender_name.required' => TranslationHandler::getTranslation($request->lang, 'required'),
                'sender_number.required' => TranslationHandler::getTranslation($request->lang, 'required'),
                'type.required' => TranslationHandler::getTranslation($request->lang, 'required'),
                'schedule_date.required' => TranslationHandler::getTranslation($request->lang, 'required'),
                'recursive_campaign_type.required' => TranslationHandler::getTranslation($request->lang, 'required'),
                'day_of_week.required' => TranslationHandler::getTranslation($request->lang, 'required'),
                'day_of_month.required' => TranslationHandler::getTranslation($request->lang, 'required'),
                'month_of_year.required' => TranslationHandler::getTranslation($request->lang, 'required'),
                'group_ids.required' => TranslationHandler::getTranslation($request->lang, 'required'),
                'no_of_time.required' => TranslationHandler::getTranslation($request->lang, 'required'),
            ];

            $validation_rules = [

                'name' => 'required|string|max:250',
                'message' => 'required|string|max:250',
                'sender_name' => 'required|string|max:65',
                'sender_number' => ['nullable', 'regex:/(\+)([1-9]{2})(\d{10})/'],
                'type' => 'required',
                'schedule_date' => Rule::requiredIf($request->type == 2),
                'no_of_time' => Rule::requiredIf($request->type == 3),
                'recursive_campaign_type' => Rule::requiredIf($request->type == 3),
                'day_of_week' => Rule::requiredIf($request->recursive_campaign_type == 1),
                'day_of_month' => Rule::requiredIf($request->recursive_campaign_type == 2 || $request->recursive_campaign_type == 3),
                'month_of_year' => Rule::requiredIf($request->recursive_campaign_type == 3),
                // 'group_id' => Rule::requiredIf($request->campaign_testing == true),
                'numbers_list' => Rule::requiredIf($request->campaign_testing == true),
            ];

            $request->validate($validation_rules, $messages);

            $input['message'] = $input['message'] . "\n\n" . TranslationHandler::getTranslation($request->lang, 'Sender Name') . ": " . $input['sender_name'];
            if ($input['sender_number']) {
                $input['message'] = $input['message'] . "\n" . TranslationHandler::getTranslation($request->lang, 'reply_to_number') . ": " . $input['sender_number'];
            }

            if ($input["campaign_testing"]) {
                // sending test sms to all received phone numbers.
                $this->sendTestCampaign($input['message'], $input["numbers_list"]);
                // exit
                return response()->json([
                    'status' => 1,
                    'message' => TranslationHandler::getTranslation($request->lang, 'test_campaign_sent'),
                ], 200, ['Content-Type' => 'application/json']);
            }
            $input['status'] = 5;
        } else {
            $input['status'] = 1;
        }

        $input['sending_to'] = 0;

        if ($campaign_id == '')
            $camp = false;
        else
            $camp = SmsCampaign::where('id', $campaign_id)->where('status', '!=', 1)->first();

        unset($input['campaign_id']);

        if (!$camp) {
            $cc = CampaignContact::where('type', 1)->where('campaign_id', $campaign_id)->orderBy('id', 'desc')->first();
            if ($cc)
                $input['group_id'] = $cc->id;
            else
                $input['group_id'] = 0;
        } else {
            $input['group_id'] = $camp->group_id;
        }

        // decoding each group_id
        $grp_ids = [];
        foreach ($input['group_ids'] as $grp) {
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
        }
        $input['group_ids'] = json_encode($grp_ids);

        if ($input["campaign_status"] == 1) {
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
                return response(['message' => TranslationHandler::getTranslation($request->lang, 'add_contacts'), 'errors' => ['group_id' => [TranslationHandler::getTranslation($request->lang, 'add_min_one_contact')]]], 422);
            } else {
                // checking user package limits
                if ($campaign_id == '')
                    $camp = false;
                else
                    $camp = SmsCampaign::where('id', $campaign_id)->where('status', '!=', 1)->first();
                if (Auth()->user()->package_subscription_id && !($camp)) {
                    $subscription = PackageSubscription::where('id', Auth()->user()->package_subscription_id)->where('is_active', 1)->first();
                    if ($subscription && $subscription->package_id != 9) {
                        // sms_limit
                        $smsLimit = $subscription->sms_limit;
                        $smsUsed = $subscription->sms_used;
                        $sending = $total_contacts;
                        $addingNow = $sending + $smsUsed;
                        if ($addingNow > $smsLimit) {
                            return response(['message' => TranslationHandler::getTranslation($request->lang, 'package_limit_ecceed_campaign'), 'code' => 1, 'errors' => ['error_message' => [TranslationHandler::getTranslation($request->lang, 'limit_exceeded')]],], 422);
                        } else {
                            // add to used SMS
                            if (Auth()->user()->package_subscription_id) {
                                $subscription = PackageSubscription::where('id', Auth()->user()->package_subscription_id)->where('is_active', 1)->first();
                                if ($subscription) {
                                    $subscription->update(['sms_used' => $subscription->sms_used + $total_contacts]);
                                }
                            }
                        }
                    } else if ($subscription && $subscription->package_id == 9) {
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
        }

        // create a unique job code
        do {
            $job_code = Str::random(25);
        } while (SMSCampaign::where("job_code", $job_code)->first() instanceof SMSCampaign);
        $input['job_code'] = $job_code;
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
        if ($smsCampaign->wasRecentlyCreated)
            User_log::create([
                'user_id' => $user->id,
                'item_id' => $smsCampaign->id,
                'log_type' => 3,
                'module' => 6,
            ]);
        else if (!$smsCampaign->wasRecentlyCreated && $smsCampaign->wasChanged())
            User_log::create([
                'user_id' => $user->id,
                'item_id' => $smsCampaign->id,
                'log_type' => 3,
                'module' => 7,
            ]);

        if ($input["campaign_status"] == 1) {

            if ($smsCampaign->status != 3) {

                if (!$smsCampaign->wasRecentlyCreated) {
                    // del prev job before adding new
                    $smsCampaign->update(['status' => 5]);
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
            } else {
                return response(TranslationHandler::getTranslation($request->lang, 'campaign_already_sent'), 404);
            }
        }

        $response = [
            'data' => SmsCampaignResource::collection([$smsCampaign])[0],
            'message' => TranslationHandler::getTranslation($request->lang, 'campaign_created'),
            'status' => 1
        ];

        return response($response, 201);
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
        $SmsCampaign = SmsCampaign::where('id', $id)->first();
        // print_r($SmsCampaign);
        // print_r($SmsCampaign->user_id . "  " . auth()->user()->id);
        if ($SmsCampaign && $SmsCampaign->user_id == auth()->user()->id) {
            return SmsCampaignResource::collection([$SmsCampaign])
                ->additional([
                    'message' => 'SMS Campaign',
                    'status' => 1,
                ]);
        }
        return response("", 401);
    }

    /**
     * Display the specified resource report.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function reportHistories(Request $request, $id)
    {
        $id = Hashids::decode($id)[0];
        $SmsCampaign = SmsCampaign::where('id', $id)->whereIn('status', [2, 3, 6])->first();
        if ($SmsCampaign) {
            $allreports = CampaignHistory::where('type', 1)->where('campaign_id', $id)->paginate(10);

            return CampaignHistoryResource::collection($allreports)->additional([
                'campaign' => SmsCampaignResource::collection([$SmsCampaign])[0],
                'message' => 'SMS Campaign Report',
                'status' => 1,
            ]);
        }
        return response("id not found", 401);
    }

    /**
     * Display the specified resource report.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function report(Request $request, $id, $history)
    {
        $id = Hashids::decode($id)[0];
        $SmsCampaign = SmsCampaign::where('id', $id)->whereIn('status', [2, 3, 6])->first();
        $history = Hashids::decode($history)[0];
        $report = CampaignHistory::where('type', 1)->where('id', $history)->first();
        if ($SmsCampaign  && $report) {
            $all_sent_to = $sent_to = $success = $fail = [];
            $allreport = CampaignHistory::where('type', 1)->where('id', $history)->with('sms_sent_to', 'sms_success', 'sms_fail')->first();

            // modules: 0:home, 1:sent_to, 2:success,

            // data used in home tab and contact activities tab
            // tab1 data
            $sent_to_total = $allreport->sms_sent_to->count();
            $success_total = $allreport->sms_success->count();
            $fail_total = $allreport->sms_fail->count();

            // data used in contact activities tab
            if ($request->module != 0) {
                // $fail = $allreports[0]->sms_fail()->paginate(10);

                if ($request->module == 1) {
                    if ($request->csv == 1) {
                        return Excel::download(new ReportContactsExport($report->sms_sent_to()->get(), $request), 'sent_to.xlsx');
                    }
                    $sent_to = $allreport->sms_sent_to()->where('number', 'LIKE', '%' . $request->search . '%')->paginate(10);
                }
                if ($request->module == 2) {
                    if ($request->csv == 1) {
                        return Excel::download(new ReportContactsExport($report->sms_success, $request), 'success.xlsx');
                    }
                    $success = $allreport->sms_success()->where('number', 'LIKE', '%' . $request->search . '%')->paginate(10);
                }
                if ($request->module == 3) {
                    if ($request->csv == 1) {
                        return Excel::download(new ReportContactsExport($report->sms_fail, $request), 'fail.xlsx');
                    }
                    $fail = $allreport->sms_fail()->where('number', 'LIKE', '%' . $request->search . '%')->paginate(10);
                }
            }

            return CampaignHistoryResource::collection([$report])->additional([
                'campaign' => SmsCampaignResource::collection([$SmsCampaign])[0],
                'sent_to' => ContactResource::collection($sent_to),
                'success' =>  ContactResource::collection($success),
                'fail' => ContactResource::collection($fail),
                'all_sent_to' => $all_sent_to,
                // tab1 data
                'report' => CampaignHistoryResource::collection([$report])[0],
                'totalSent' => $sent_to_total,
                'totalSuccesses' => $success_total,
                'totalFails' => $fail_total,
                'message' => 'SMS Campaign Report',
                'status' => 1,
            ]);
        }
        return response("id not found", 401);
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

        $found = SmsCampaign::where('id', $id)->first();
        if ($found && $found->user_id == auth()->user()->id) {
            $found->delete();
            User_log::create([
                'user_id' => auth()->user()->id,
                'item_id' => $found->id,
                'log_type' => 3,
                'module' => 8,
            ]);

            $response = [
                'message' => TranslationHandler::getTranslation($request->lang, 'campaign_deleted')
            ];

            return response($response, 201);
        }
        return response("", 401);
    }

    public function destroyMany(Request $request)
    {
        $data = $request->validate([
            'id' => ['required', 'array'],
            'id.*' => ['required', 'integer'],
        ]);

        for ($i = 0; $i < sizeof($request->id); $i++) {
            $id = Hashids::decode($request->id[$i])[0];
            $SmsCampaign = SmsCampaign::where('id', $id)->first();
            if ($SmsCampaign && $SmsCampaign->user_id == auth()->user()->id) {
                $SmsCampaign->delete();
                User_log::create([
                    'user_id' => auth()->user()->id,
                    'item_id' => $SmsCampaign->id,
                    'log_type' => 3,
                    'module' => 8,
                ]);
            }
        }
        $response = [
            'message' => TranslationHandler::getTranslation($request->lang, 'campaign_deleted')
        ];

        return response($response, 201);
    }

    public function info()
    {
        $id = Auth()->user()->id;
        $myDate = date("Y-m-d", strtotime(date("Y-m-d", strtotime(date("Y-m-d"))) . "-1 day"));
        $smsCampaigns = SmsCampaign::where('user_id', $id)->get();
        $new_time = smsCampaign::where('user_id', $id)->latest()->first();
        if ($new_time) {
            $new_time = $new_time->created_at;
        } else {
            $new_time = 0;
        }
        $new_smsCampaigns = $smsCampaigns->where('created_at', '>=', $new_time)->where('created_at', '>=', $myDate)->count();
        $existing_smsCampaigns = $smsCampaigns->where('created_at', '>=', $myDate)->count();
        $total_smsCampaigns = $smsCampaigns->count();
        $deleted_smsCampaigns = smsCampaign::onlyTrashed()->where('user_id', $id)->count();

        $response = [
            'sent' => $smsCampaigns->where('status', '3')->count(),
            'scheduled' => $smsCampaigns->where('type', 2)->where('status', 5)->count(),
            'recursive' => $smsCampaigns->where('type', 3)->whereIn('status', [5, 2])->count(),
            'draft' => $smsCampaigns->where('status', 1)->count(),
            'existing' => $existing_smsCampaigns,
            'deleted' => $deleted_smsCampaigns,
            'total' => $total_smsCampaigns,
            'new' => $new_smsCampaigns,
            'message' => "smsCampaigns details fetched"
        ];

        return response($response, 201);
    }

    /**
     * Stop a specified campaign.
     *
     * @return \Illuminate\Http\Response
     */
    public function stop($id)
    {
        $user = auth()->user();
        $id = Hashids::decode($id)[0];
        $SmsCampaign = SmsCampaign::where('id', $id)->first();
        if ($SmsCampaign && $SmsCampaign->user_id == $user->id) {

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
                    // return response("Campaign Stopped", 200);
                } else {
                    $SmsCampaign->update(['status' => 4, 'sending_stopped_at' => now(),  'sending_started_at' => now(), 'sending_completed_at' => now(), 'job_code' => $job_code]);
                }
            } else {
                $SmsCampaign->update(['status' => 4, 'sending_stopped_at' => now(),  'sending_started_at' => now(), 'sending_completed_at' => now(), 'job_code' => $job_code]);
                $total_contacts = count($contacts) - $excluding;
            }

            if (Auth()->user()->package_subscription_id) {
                $subscription = PackageSubscription::where('id', Auth()->user()->package_subscription_id)->where('is_active', 1)->first();
                if ($subscription && $subscription->package_id != 9) {
                    // sub from to used emails
                    if ($subscription->sms_used - $total_contacts < 0)
                        $val = 0;
                    else
                        $val = $subscription->sms_used - $total_contacts;
                    $subscription->update(['sms_used' => $val]);
                } else if ($subscription && $subscription->package_id == 9 && $SmsCampaign->created_at->startOfWeek()->format('Y-m-d') ==  now()->startOfWeek()->format('Y-m-d')) {
                    // add to used emails

                    $sms_span1_start = PackageSettingsValue(4, 'start_range');
                    $sms_span1_end = PackageSettingsValue(4, 'end_range');
                    $sms_span1_price = PackageSettingsValue(4, 'price_without_vat');
                    $sms_span2_start = PackageSettingsValue(5, 'start_range');
                    $sms_span2_end = PackageSettingsValue(5, 'end_range');
                    $sms_span2_price = PackageSettingsValue(5, 'price_without_vat');
                    $sms_span3_start = PackageSettingsValue(6, 'start_range');
                    $sms_span3_price = PackageSettingsValue(6, 'price_without_vat');

                    if ($subscription->sms_used - $total_contacts < 0)
                        $val = 0;
                    else
                        $val = $subscription->sms_used - $total_contacts;
                    $subscription->update(['sms_used' => $val]);
                    $subscription->sms_paying_for -= $total_contacts;
                    if ($subscription->sms_paying_for < 0)
                        $subscription->sms_paying_for = 0;
                    // if ($subscription->sms_used >= $sms_span1_start && $subscription->sms_used <= $sms_span1_end) {
                    $subscription->sms_to_pay -= $sms_span1_price;
                    // } else if ($subscription->sms_used >= $sms_span2_start && $subscription->sms_used <= $sms_span2_end) {
                    //     $subscription->sms_to_pay += $sms_span2_price;
                    // } else if ($subscription->sms_used >= $sms_span3_start) {
                    //     $subscription->sms_to_pay += $sms_span3_price;
                    // }
                    if ($subscription->sms_to_pay < 0)
                        $subscription->sms_to_pay = 0;
                    $subscription->save();
                }
            }

            // $SmsCampaign->update(['status' => 4, 'sending_stopped_at' => now(),  'sending_started_at' => now(), 'sending_completed_at' => now(), 'job_code' => $job_code]);
            // saving stopped history
            User_log::create([
                'user_id' => $SmsCampaign->user_id,
                'item_id' => $SmsCampaign->id,
                'module' => 3,
                'log_type' => 12,
            ]);
            return response("Campaign Stopped", 200);
        }
        return response("Campaign not found", 404);
    }

    private function sendTestCampaign($message, $number_list)
    {

        $sid = settingValue('twilio_sid');
        $token = settingValue('twilio_auth_token');
        $twilioClient = new Client($sid, $token);
        foreach ($number_list as $number) {
            try {
                $twilioClient->messages->create(
                    $number,
                    [
                        'from' => settingValue('twilio_number'),
                        'body' => $message
                    ]
                );
            } catch (\Throwable $e) {
                //throw $th;
            }
        }
    }
}
