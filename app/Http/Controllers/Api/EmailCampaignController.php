<?php

namespace App\Http\Controllers\Api;

use App\CustomClasses\TranslationHandler;
use App\Exports\ReportClicksExport;
use App\Exports\ReportContactsExport;
use App\Http\Controllers\Controller;
use App\Http\Resources\EmailCampaignResource;
use App\Http\Resources\GroupResource;
use App\Http\Resources\CampaignHistoryResource;
use App\Http\Resources\ContactResource;
use App\Jobs\EmailCampaignJob;
use App\Jobs\SendCapmaignMail;
use App\Models\Admin\Package;
use App\Models\Admin\PackageLinkFeature;
use App\Models\Admin\PackageSubscription;
use App\Models\CampaignContact;
use App\Models\CampaignExclude;
use App\Models\CampaignHistory;
use App\Models\Contact;
use App\Models\EmailCampaign;
use App\Models\EmailCampaignClick;
use App\Models\EmailCampaignLogs;
use App\Models\EmailCampaignOpen;
use App\Models\EmailCampaignTemplate;
use App\Models\Group;
use App\Models\PayAsYouGoPayments;
use App\Models\SplitTestSubject;
use App\Models\User;
use App\Models\User_log;
use Carbon\Carbon;
use Hashids;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use PDF;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class EmailCampaignController extends Controller
{
    /**
     * Create a new EmailCampaignController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Store Cloud Data
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $input = $request->all();
        if ($input["campaign_status"] == 1 || $input["campaign_testing"]) {
            $messages = [
                'name.required' => TranslationHandler::getTranslation($request->lang, 'required'),
                'subject.required' => TranslationHandler::getTranslation($request->lang, 'required'),
                'sender_name.required' => TranslationHandler::getTranslation($request->lang, 'required'),
                'sender_email.required' => TranslationHandler::getTranslation($request->lang, 'required'),
                'reply_to_email.required' => TranslationHandler::getTranslation($request->lang, 'required'),
                'campaign_type.required' => TranslationHandler::getTranslation($request->lang, 'required'),
                'schedule_date.required' => TranslationHandler::getTranslation($request->lang, 'required'),
                'recursive_campaign_type.required' => TranslationHandler::getTranslation($request->lang, 'required'),
                'day_of_week.required' => TranslationHandler::getTranslation($request->lang, 'required'),
                'day_of_month.required' => TranslationHandler::getTranslation($request->lang, 'required'),
                'month_of_year.required' => TranslationHandler::getTranslation($request->lang, 'required'),
                // 'day_of_week_year.required' => TranslationHandler::getTranslation($request->lang, 'required'),
                'group_id.required' => TranslationHandler::getTranslation($request->lang, 'required'),
                'template_id.required' => TranslationHandler::getTranslation($request->lang, 'required'),
            ];

            $validation_rules = [
                'name' => 'required|string|max:250',
                'subject' => 'required|string|max:250',
                'sender_name' => 'required|string|max:65',
                'sender_email' => ['required', 'regex:/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/', 'max:65'],
                'reply_to_email' => ['nullable', 'regex:/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/', 'max:65'],
                'campaign_type' => 'required',
                // 'group_id' => 'required',
                'template_id' => 'required',
                'schedule_date' => Rule::requiredIf($request->campaign_type == 2),
                'recursive_campaign_type' => Rule::requiredIf($request->campaign_type == 3),
                'no_of_time' => Rule::requiredIf($request->campaign_type == 3),
                'day_of_week' => Rule::requiredIf($request->recursive_campaign_type == 1),
                'day_of_month' => Rule::requiredIf($request->recursive_campaign_type == 2 || $request->recursive_campaign_type == 3),
                'month_of_year' => Rule::requiredIf($request->recursive_campaign_type == 3),
                // 'day_of_week_year' => Rule::requiredIf($request->recursive_campaign_type == 3),
                'emails_list' => Rule::requiredIf($request->campaign_testing == true),
            ];
            $request->validate($validation_rules, $messages);

            //$input['user_id'] = auth()->user()->id;
            if ($input["campaign_testing"]) {
                // sending test email to received email addresses.
                $this->sendTestCampaign($input['template_id'], $input["subject"], $input["emails_list"], $input["sender_name"], $input["sender_email"], $input["reply_to_email"]);
                // exit
                return response()->json([
                    'status' => 1,
                    'message' => TranslationHandler::getTranslation($request->lang, 'test_campaign_sent'),
                ], 200, ['Content-Type' => 'application/json']);
            }
            $input['status'] = 1;
        } else {
            $input['status'] = 2;
        }

        $auth = auth()->user();
        $id = '';
        if (isset($input['campaign_id']))
            $id = Hashids::decode($input['campaign_id'])[0];

        if ($id == '')
            $camp = false;
        else
            $camp = EmailCampaign::where('id', $id)->where('status', '!=', 2)->first();

        unset($input['campaign_id']);

        if (!$camp) {
            $cc = CampaignContact::where('type', 2)->where('campaign_id', $id)->orderBy('id', 'desc')->first();
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
                return response(['message' => TranslationHandler::getTranslation($request->lang, 'add_contacts'), 'errors' => ['group_id' => [TranslationHandler::getTranslation($request->lang, 'add_min_one_contact')]]], 422);
            } else {
                // checking user package limits
                if (Auth()->user()->package_subscription_id && !($camp)) {
                    $subscription = PackageSubscription::where('id', Auth()->user()->package_subscription_id)->where('is_active', 1)->first();
                    if ($subscription && $subscription->package_id != 9) {
                        // email_limit
                        $emailLimit = $subscription->email_limit;
                        $emailUsed = $subscription->email_used;
                        $sending = $total_contacts;
                        $addingNow = $sending + $emailUsed;
                        if ($addingNow > $emailLimit) {
                            return response(['message' => TranslationHandler::getTranslation($request->lang, 'package_limit_ecceed_campaign'), 'code' => 1, 'errors' => ['error_message' => [TranslationHandler::getTranslation($request->lang, 'limit_exceeded')]],], 422);
                        } else {
                            // add to used emails
                            if (Auth()->user()->package_subscription_id) {
                                $subscription = PackageSubscription::where('id', Auth()->user()->package_subscription_id)->where('is_active', 1)->first();
                                if ($subscription) {
                                    $subscription->update(['email_used' => $subscription->email_used + $total_contacts]);
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
        }

        // create a unique job code
        do {
            $job_code = Str::random(25);
        } while (EmailCampaign::where("job_code", $job_code)->first() instanceof EmailCampaign);
        $input['job_code'] = $job_code;
        $input['subscription_id'] = $auth->package_subscription_id;

        $emailCampaign = EmailCampaign::updateOrCreate(
            [
                'user_id' => $auth->id,
                'id' => $id,
            ],
            $input
        );

        if ($input["campaign_status"] == 1) {
            if ($request->campaign_type == 1) {
                //schedule immediately

                EmailCampaignJob::dispatch($auth, $request->all(), $emailCampaign->id, $job_code);
            } elseif ($request->campaign_type == 2) {
                //schedule once

                $scheduleTime = Carbon::parse($request->schedule_date);
                $currentTime = Carbon::parse(now()->format('Y-m-d'));
                //$totalDuration = $scheduleTime->diffInMinutes($currentTime);
                $totalDuration = $currentTime->diff($scheduleTime);
                //$length = $finishTime->diffInMinutes($startTime);
                EmailCampaignJob::dispatch($auth, $request->all(), $emailCampaign->id, $job_code)->delay($totalDuration);
            } elseif ($request->campaign_type == 3) {
                //schedule recursively

                if ($request->recursive_campaign_type == 1) {
                    //weekly
                    $dayOfWeek = Carbon::parse('next ' . config('constants.days_of_week')[$request->day_of_week]); //->toDateString();
                    $currentTime = Carbon::parse(now()->format('Y-m-d'));
                    $totalDuration = $currentTime->diff($dayOfWeek);

                    EmailCampaignJob::dispatch($auth, $request->all(), $emailCampaign->id, $job_code)->delay($totalDuration);
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

                    EmailCampaignJob::dispatch($auth, $request->all(), $emailCampaign->id, $job_code)->delay($totalDuration);
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

                    EmailCampaignJob::dispatch($auth, $request->all(), $emailCampaign->id, $job_code)->delay($totalDuration);
                }
            }
        }

        return response()->json([
            'data' => EmailCampaignResource::collection([$emailCampaign->makeHidden(['track_opens', 'track_clicks', 'status', 'id'])])[0],
            'status' => 1,
            'message' => TranslationHandler::getTranslation($request->lang, 'campaign_created'),
        ], 200, ['Content-Type' => 'application/json']);
    }

    /**
     * Edit email campaign .
     * @var id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Request $request)
    {
        $result = EmailCampaign::where('status', '!=', 3)->find(Hashids::decode($request->_id)[0]);

        if ($result['is_split_testing'] == 1 && $result['split_test_param'] == 1) {
            $sub1 = SplitTestSubject::where('id', $result['split_subject_line_1'])->first();
            if ($sub1)
                $result['split_subject_line_1'] = $sub1->split_subject;
            $sub2 = SplitTestSubject::where('id', $result['split_subject_line_2'])->first();
            if ($sub2)
                $result['split_subject_line_2'] = $sub2->split_subject;
        }

        return response()->json([
            'data' => EmailCampaignResource::collection([$result->makeHidden(['track_opens', 'track_clicks', 'status', 'id'])])[0],
            // 'data' => $result->makeHidden(['track_opens', 'track_clicks', 'status']),
            'status' => 1,
            'message' => "Edit Campaign!",
        ], 200, ['Content-Type' => 'application/json']);
    }

    /**
     * Display a listing of the campaign reports.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function campaignReportListing(Request $request)
    {
        $reports = EmailCampaign::where(['user_id' => auth()->user()->id, 'is_split_testing' => 0]);
        if ($request->has('filt_status') && !empty($request->filt_status)) {
            $reports = $reports->where('status', $request->filt_status);
        }

        if ($request->has('filt_camp_name') && !empty($request->filt_camp_name)) {
            $reports = $reports->where('name', 'LIKE', '%' . $request->filt_camp_name . '%');
        }

        if ($request->has('filter_date') && !empty($request->filter_date)) {
            $reports = $reports->whereDate('created_at', Carbon::parse($request->filter_date));
        }
        $reports = $reports->orderBy('created_at', 'DESC')->paginate(10);
        return EmailCampaignResource::collection($reports)
            ->additional([
                'message' => 'Email Campaign Reports',
                'status' => 1,
            ]);
    }

    public function getUserGroups(Request $request)
    {
        $groups = auth()->user()->groups;
        $templates = EmailCampaignTemplate::where('user_id', auth()->user()->id)->get();

        return response()->json([
            'data' => ['groups' => GroupResource::collection($groups), 'templates' => $templates],
            'status' => 1,
            'message' => "Contact Groups!",
        ], 200, ['Content-Type' => 'application/json']);
    }

    public function campaignCounter(Request $req)
    {
        $campaigns = EmailCampaign::where('status', '!=', 3)->where(['user_id' => auth()->user()->id, 'is_split_testing' => 0])->get();

        $total = $campaigns->count();
        $draft = $campaigns->where('status', 2)->count();
        $deleted = EmailCampaign::onlyTrashed()->where('user_id', auth()->user()->id)->count();
        $sent = $campaigns->where('status', 5)->count();
        $scheduled = $campaigns->where('campaign_type', 2)->where('status', 1)->count();
        $recursive = $campaigns->where('campaign_type', 3)->whereIn('status', [1, 4])->count();

        return response()->json([
            'status' => 1,
            'data' => [
                'total' => $total,
                'draft' => $draft,
                'recursive' => $recursive,
                'scheduled' => $scheduled,
                'sent' => $sent,
                'deleted' => $deleted,
            ],
            'message' => 'Your campaigns stats!',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        $id = Hashids::decode($id);
        if (isset($id[0])) {
            $EmailCampaign = EmailCampaign::where(['id' => $id[0], 'user_id' => auth()->user()->id])->first();

            if ($EmailCampaign)
                $EmailCampaign->delete();
        }

        return response()->json([
            'status' => 1,
            'message' => TranslationHandler::getTranslation($request->lang, 'campaign_deleted'),
        ]);
    }

    private function sendTestCampaign($template, $subject, $email_list, $sender_name, $sender_email, $reply_to_email)
    {
        foreach ($email_list as $email) {
            $emailTemplate = EmailCampaignTemplate::where('id', $template)->first();
            SendCapmaignMail::dispatch([], [$sender_name, $sender_email, $reply_to_email], $email, $subject, $emailTemplate->html_content);
        }
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
        $EmailCampaign = EmailCampaign::where('id', $id)->where('status', '>=', 4)->first();
        if ($EmailCampaign) {
            $allreports = CampaignHistory::where('type', 2)->where('campaign_id', $id)->paginate(10);

            return CampaignHistoryResource::collection($allreports)->additional([
                'campaign' => EmailCampaignResource::collection([$EmailCampaign])[0],
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
        $EmailCampaign = EmailCampaign::where('id', $id)->where('status', '>=', 4)->first();
        $splitData = [];
        $history = Hashids::decode($history)[0];
        $report = CampaignHistory::where('type', 2)->where('id', $history)->first();
        if ($EmailCampaign && $report) {
            $all_sent_to = $sent_to = $success = $fail = $bounces = $unsubscribers = $unopeners = $opens = $clicks = $opensData = $clicksData = $splitData = [];
            $clicks_total = $totalOpens = $sent_to_total = $success_total =  $fail_total =  $bounces_total = $totalUniqueOpens = $totalUniqueClicks = $unsubscribers_total = 0;

            $allreport = CampaignHistory::where('type', 2)->where('id', $history)->with('sent_to', 'success', 'fail', 'bounces', 'unsubscribers')->first();

            // modules: 0:home, 1:sent_to, 2:success, 3:unopened, 4:opendata, 5:clickdata, 6:unsubscribers, 7:clicklogs, 8:bounces, 9:splitData

            // data used in home tab and contact activities tab
            if ($request->module != 7 && $request->module != 8 && $request->module != 9) {
                // tab1 data
                $sent_to_total = $allreport->sent_to->count();
                $success_total = $allreport->success->count();
                $fail_total = $allreport->fail->count();
                $bounces_total = $allreport->bounces->count();
                $openss = EmailCampaignOpen::where('history_id', $history);
                // $openss = EmailCampaignOpen::where('campaign_id', $id);


                $totalOpens = $openss->count();
                $openss = $openss->pluck('contact_id');
                $totalUniqueOpens = count(array_unique($openss->toArray()));
                $clickss = EmailCampaignClick::where('history_id', $history)->pluck('contact_id');
                // $clickss = EmailCampaignClick::where('campaign_id', $id)->pluck('contact_id');
                $totalUniqueClicks = count(array_unique($clickss->toArray()));

                // tab2 Data
                $unsubscribers_total = $allreport->unsubscribers()->count();
                if ($request->module == 0)
                    $opens = EmailCampaignOpen::where('history_id', $history)->with('contact')->orderBy('created_at', 'ASC')->get();
                // $opens = EmailCampaignOpen::where('campaign_id', $id)->with('contact')->orderBy('created_at', 'ASC')->get();
            }

            if ($request->module == 7) {
                $clicks = EmailCampaignClick::where('history_id', $history)->with('contact')->whereRelation('contact', 'email', 'LIKE', '%' . $request->search . '%')->orderBy('created_at', 'ASC')->get();
                // $clicks = EmailCampaignClick::where('campaign_id', $id)->with('contact')->orderBy('created_at', 'ASC')->get();
                $clicks_total = EmailCampaignClick::where('campaign_id', $id)->count();
                if ($request->csv == 1) {
                    return Excel::download(new ReportClicksExport($clicks, $request), 'report-clicks.xlsx');
                }
            }

            if ($request->module == 8) {
                $bounces_total = $allreport->bounces()->count();
                $bounces = $allreport->bounces()->where('email', 'LIKE', '%' . $request->search . '%')->paginate(10);
                if ($request->csv == 1) {
                    return Excel::download(new ReportContactsExport($allreport->bounces, $request), 'bounces.xlsx');
                }
            }

            // data used in contact activities tab
            if ($request->module != 0 && $request->module != 7 && $request->module != 8 && $request->module != 9) {

                $allopens = EmailCampaignOpen::where('history_id', $history);
                // $allopens = EmailCampaignOpen::where('campaign_id', $id);
                $opensContacts = $allopens->pluck('contact_id');
                $allclicks = EmailCampaignClick::where('history_id', $history);
                // $allclicks = EmailCampaignClick::where('campaign_id', $id);
                $clicksContacts = $allclicks->pluck('contact_id');
                $uniqueOpens = array_unique($opensContacts->toArray());
                $uniqueClicks = array_unique($clicksContacts->toArray());
                // dd($uniqueOpens, $uniqueClicks);
                $opensData = [];
                $clicksData = [];
                // $fail = $allreports[0]->fail()->paginate(10);

                if ($request->module == 1) {
                    if ($request->csv == 1) {
                        return Excel::download(new ReportContactsExport($report->sent_to()->get(), $request), 'sent_to.xlsx');
                    }
                    $sent_to = $allreport->sent_to()->where('email', 'LIKE', '%' . $request->search . '%')->paginate(10);
                }
                if ($request->module == 2) {
                    if ($request->csv == 1) {
                        return Excel::download(new ReportContactsExport($report->success, $request), 'success.xlsx');
                    }
                    $success = $allreport->success()->where('email', 'LIKE', '%' . $request->search . '%')->paginate(10);
                }
                if ($request->module == 22) {
                    if ($request->csv == 1) {
                        return Excel::download(new ReportContactsExport($report->fail, $request), 'fail.xlsx');
                    }
                    $fail = $allreport->fail()->where('email', 'LIKE', '%' . $request->search . '%')->paginate(10);
                }
                if ($request->module == 3) {
                    $unopeners = $allreport->success()->whereNotIn('contact_id', $uniqueOpens)->where('email', 'LIKE', '%' . $request->search . '%')->paginate(10);
                    if ($request->csv == 1) {
                        return Excel::download(new ReportContactsExport($report->success()->whereNotIn('contact_id', $uniqueOpens)->get(), $request), 'unopeners.xlsx');
                    }
                }
                if ($request->module == 4) {
                    if ($request->csv == 1) {
                        $contacts_data = Contact::whereIn('id', $uniqueOpens)->get();
                        return Excel::download(new ReportContactsExport($contacts_data, $request), 'openers.xlsx');
                    }
                    $uniqueOpens = Contact::whereIn('id', $uniqueOpens)->where('email', 'LIKE', '%' . $request->search . '%')->paginate(10)->pluck('id')->toArray();
                    foreach ($uniqueOpens as $id) {
                        $opens_copy = clone $allopens;
                        $data = json_encode($opens_copy->where('contact_id', $id)->pluck('created_at')->toArray());
                        $contact = Contact::where('id', $id)->first();
                        array_push($opensData, ['contact' => $contact, 'opens' => $data]);
                    }
                }
                if ($request->module == 5) {
                    if ($request->csv == 1) {
                        $contacts_data = Contact::whereIn('id', $uniqueClicks)->get();
                        return Excel::download(new ReportContactsExport($contacts_data, $request), 'clickers.xlsx');
                    }
                    $uniqueClicks = Contact::whereIn('id', $uniqueClicks)->where('email', 'LIKE', '%' . $request->search . '%')->paginate(10)->pluck('id')->toArray();
                    foreach ($uniqueClicks as $id) {
                        $click_copy = clone $allclicks;
                        $data = json_encode($click_copy->where('contact_id', $id)->pluck('created_at')->toArray());
                        $contact = Contact::where('id', $id)->first();
                        array_push($clicksData, ['contact' => $contact, 'clicks' => $data]);
                    }
                }
                if ($request->module == 6) {
                    $unsubscribers = $allreport->unsubscribers()->where('email', 'LIKE', '%' . $request->search . '%')->paginate(10);
                    if ($request->csv == 1) {
                        return Excel::download(new ReportContactsExport($allreport->unsubscribers, $request), 'unsubscribers.xlsx');
                    }
                }
            }

            if ($request->module == 9) {
                $all_sent_to = $allreport->sent_to()->get();
                $clicks1 = $opens1 = $clicks2 = $opens2 = 0;
                // get contacts and divide into two groups.
                $logs = EmailCampaignLogs::where('campaign_id', $EmailCampaign->id)->get();
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

                            if (isset($contents[1])) {
                                $sub_contacts2 = $logs->where('content_id', $contents[1])->pluck('contact_id')->toArray();
                                $clicks2 = EmailCampaignClick::where('history_id', $history)->whereIn('contact_id', $sub_contacts2)->count();
                                // $clicks2 = EmailCampaignClick::where('campaign_id', $EmailCampaign->id)->whereIn('contact_id', $sub_contacts2)->count();
                                $opens2 = EmailCampaignOpen::where('history_id', $history)->whereIn('contact_id', $sub_contacts2)->count();
                                // $opens2 = EmailCampaignOpen::where('campaign_id', $EmailCampaign->id)->whereIn('contact_id', $sub_contacts2)->count();
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

                            if (isset($subjects[1])) {
                                $sub_contacts2 = $logs->where('subject_id', $subjects[1])->pluck('contact_id')->toArray();
                                $clicks2 = EmailCampaignClick::where('history_id', $history)->whereIn('contact_id', $sub_contacts2)->count();
                                // $clicks2 = EmailCampaignClick::where('campaign_id', $EmailCampaign->id)->whereIn('contact_id', $sub_contacts2)->count();
                                $opens2 = EmailCampaignOpen::where('history_id', $history)->whereIn('contact_id', $sub_contacts2)->count();
                                // $opens2 = EmailCampaignOpen::where('campaign_id', $EmailCampaign->id)->whereIn('contact_id', $sub_contacts2)->count();
                            } else {
                                // stays 0
                            }
                        } else {
                            // both stay 0
                        }
                    }
                $splitData = [['name' => "Section 1", TranslationHandler::getTranslation($request->lang, 'Open Rate') => $opens1, TranslationHandler::getTranslation($request->lang, 'Link Clicks') => $clicks1], ['name' => "Section 2", TranslationHandler::getTranslation($request->lang, 'Open Rate') => $opens2, TranslationHandler::getTranslation($request->lang, 'Link Clicks') => $clicks2]];
            }

            return CampaignHistoryResource::collection([$report])->additional([
                'campaign' => EmailCampaignResource::collection([$EmailCampaign])[0],
                'sent_to' => ContactResource::collection($sent_to),
                'success' =>  ContactResource::collection($success),
                'fail' => ContactResource::collection($fail),
                'bounces' => ContactResource::collection($bounces),
                'unsubscribers' => ContactResource::collection($unsubscribers),
                'unopeners' => ContactResource::collection($unopeners),
                'opens' => $opens,
                'clickLogs' => $clicks,
                'opensData' => $opensData,
                'clicksData' => $clicksData,
                'splitData' => $splitData,
                'totalClicks' => $clicks_total,
                'all_sent_to' => $all_sent_to,
                // tab1 data
                'report' => CampaignHistoryResource::collection([$report])[0],
                'totalOpens' => $totalOpens,
                'totalSent' => $sent_to_total,
                'totalSuccesses' => $success_total,
                'totalFails' => $fail_total,
                'totalBounces' => $bounces_total,
                'totalUniqueOpens' => $totalUniqueOpens,
                'totalUniqueClicks' => $totalUniqueClicks,
                'totalUnsubscribers' => $unsubscribers_total,
                'message' => 'SMS Campaign Report',
                'status' => 1,
            ]);
        }
        return response("id not found", 401);
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
        $EmailCampaign = EmailCampaign::where('id', $id)->first();
        if ($EmailCampaign && $EmailCampaign->user_id == $user->id) {

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
                // if ($EmailCampaign->created_at->format('m') ==  date('m')) {
                // dd("stop and give back");
                $total_contacts = (count($contacts) - $excluding) * $EmailCampaign->no_of_time;
                // }
                // recursive
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
                    // return response("Campaign Stopped", 200);
                } else {
                    $EmailCampaign->update(['status' => 3, 'sending_stopped_at' => now(), 'job_code' => $job_code]);
                }
            } else {
                $EmailCampaign->update(['status' => 3, 'sending_stopped_at' => now(), 'job_code' => $job_code]);
                $total_contacts = count($contacts) - $excluding;
            }

            if (Auth()->user()->package_subscription_id) {
                $subscription = PackageSubscription::where('id', Auth()->user()->package_subscription_id)->where('is_active', 1)->first();
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

            // $EmailCampaign->update(['status' => 3, 'sending_stopped_at' => now(), 'job_code' => $job_code]);
            // saving stopped history
            User_log::create([
                'user_id' => $EmailCampaign->user_id,
                'item_id' => $EmailCampaign->id,
                'module' => 4,
                'log_type' => 12,
            ]);
            return response("Campaign Stopped", 200);
        }
        return response("Campaign not found", 404);
    }
}
