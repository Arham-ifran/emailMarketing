<?php

namespace App\Http\Controllers\Api;

use App\CustomClasses\TranslationHandler;
use App\Http\Controllers\Controller;
use App\Http\Resources\EmailCampaignResource;
use App\Http\Resources\CampaignHistoryResource;
use App\Jobs\SendCapmaignMail;
use App\Jobs\SplitTestingJob;
use App\Models\Admin\PackageSubscription;
use App\Models\CampaignContact;
use App\Models\CampaignExclude;
use App\Models\CampaignHistory;
use App\Models\Contact;
use App\Models\EmailCampaign;
use App\Models\EmailCampaignTemplate;
use App\Models\Group;
use App\Models\PayAsYouGoPayments;
use App\Models\SplitTestSubject;
use App\Models\User;
use App\Models\User_log;
use Carbon\Carbon;
use Hashids;
use PDF;
use Hashids\Hashids as HashidsHashids;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class SplitTestingController extends Controller
{
    /**
     * Create a new SplitTestingController instance.
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
                'split_test_param.required' => TranslationHandler::getTranslation($request->lang, 'select_one_split'),
            ];

            $validation_rules = [
                'name' => 'required|string|max:250',
                'subject' => Rule::requiredIf($request->split_test_param == 2),
                'sender_name' => 'required|string|max:65',
                'sender_email' => ['required', 'regex:/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/', 'max:65'],
                'reply_to_email' => ['nullable', 'regex:/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/', 'max:65'],
                'campaign_type' => 'required',
                // 'group_ids' => 'required|array',
                'split_test_param' => 'required',
                'template_id' => Rule::requiredIf($request->split_test_param == 1),
                'split_subject_line_1' => Rule::requiredIf($request->split_test_param == 1),
                'split_subject_line_2' => Rule::requiredIf($request->split_test_param == 1),
                'split_email_content_1' => Rule::requiredIf($request->split_test_param == 2),
                'split_email_content_2' => Rule::requiredIf($request->split_test_param == 2),
                'schedule_date' => Rule::requiredIf($request->campaign_type == 2),
                'recursive_campaign_type' => Rule::requiredIf($request->campaign_type == 3),
                'no_of_time' => Rule::requiredIf($request->campaign_type == 3),
                'day_of_week' => Rule::requiredIf($request->recursive_campaign_type == 1),
                'day_of_month' => Rule::requiredIf($request->recursive_campaign_type == 2),
                'month_of_year' => Rule::requiredIf($request->recursive_campaign_type == 3),
                // 'day_of_week_year' => Rule::requiredIf($request->recursive_campaign_type == 3),
                'emails_list' => Rule::requiredIf($request->campaign_testing == true),
            ];

            $request->validate($validation_rules, $messages);
            //$input['user_id'] = auth()->user()->id;
            if ($input["campaign_testing"]) {
                if (count($input["emails_list"]) < 2) {
                    // send error
                    return response(['message' => TranslationHandler::getTranslation($request->lang, 'add_more_emails'), 'errors' => ['emails_list' => [TranslationHandler::getTranslation($request->lang, 'add_min_two_contacts')]]], 422);
                }
                $subject = $input["subject"] ? $input["subject"] : $input["split_subject_line_1"];
                $subject2 = $input["subject"] ? $input["subject"] : $input["split_subject_line_2"];
                $template = $input["template_id"] ? $input["template_id"] : $input["split_email_content_1"];
                $template2 = $input["template_id"] ? $input["template_id"] : $input["split_email_content_2"];
                // sending test email to received email addresses.
                $this->sendTestCampaign($template, $subject, $template2, $subject2, $input["emails_list"], $input["sender_name"], $input["sender_email"], $input["reply_to_email"]);
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

        $input['is_split_testing'] = 1;

        $id = $input['campaign_id'];
        if ($id) {
            $id = Hashids::decode($id)[0];
        }

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

        $auth = auth()->user();

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
            if ($id != '') {
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
                return response(['message' => TranslationHandler::getTranslation($request->lang, 'add_contacts'), 'errors' => ['group_id' => [TranslationHandler::getTranslation($request->lang, 'add_min_two_contacts')]]], 422);
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

        $subject1 = isset($input['split_subject_line_1']) ? $input['split_subject_line_1'] : NULL;
        unset($input['split_subject_line_1']);
        $subject2 = isset($input['split_subject_line_2']) ? $input['split_subject_line_2'] : NULL;
        unset($input['split_subject_line_2']);

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

        if ($emailCampaign->wasRecentlyCreated) {
            // userlogs
            User_log::create([
                'user_id' => $auth->id,
                'item_id' => $emailCampaign->id,
                'log_type' => 4,
                'module' => 6,
            ]);
            // create subject rows with this campaign id
            if ($subject1 || $subject2) {
                $sub1 = SplitTestSubject::create([
                    'user_id' => $auth->id,
                    'campaign_id' => $emailCampaign->id,
                    'split_subject' => $subject1,
                ]);
                $sub2 = SplitTestSubject::create([
                    'user_id' => $auth->id,
                    'campaign_id' => $emailCampaign->id,
                    'split_subject' => $subject2,
                ]);
                $emailCampaign->update(['split_subject_line_1' => $sub1->id, 'split_subject_line_2' => $sub2->id]);
            }
        } elseif (!$emailCampaign->wasRecentlyCreated && $emailCampaign->wasChanged()) {
            // userlogs
            User_log::create([
                'user_id' => auth()->user()->id,
                'item_id' => $emailCampaign->id,
                'log_type' => 4,
                'module' => 7,
            ]);
            // update subject rows with this campaign id
            if ($subject1 || $subject2) {
                $sub1 = SplitTestSubject::where(['user_id' => $auth->id, 'campaign_id' => $emailCampaign->id])->first();
                if ($sub1) {
                    $sub1->update(['split_subject' => $subject1]);
                } else {
                    $sub1 = SplitTestSubject::create([
                        'user_id' => $auth->id,
                        'campaign_id' => $emailCampaign->id,
                        'split_subject' => $subject1,
                    ]);
                }

                $sub2 = SplitTestSubject::where(['user_id' => $auth->id, 'campaign_id' => $emailCampaign->id])->skip(1)->first();
                if ($sub2) {
                    $sub2->update(['split_subject' => $subject2]);
                } else {
                    $sub2 = SplitTestSubject::create([
                        'user_id' => $auth->id,
                        'campaign_id' => $emailCampaign->id,
                        'split_subject' => $subject2,
                    ]);
                }
                $emailCampaign->update(['split_subject_line_1' => $sub1->id, 'split_subject_line_2' => $sub2->id]);
            }
        }

        if ($input["campaign_status"] == 1) {
            if ($request->campaign_type == 1) {
                //schedule immediately

                SplitTestingJob::dispatch($auth, $request->all(), $emailCampaign->id, $job_code)->delay(10);
            } elseif ($request->campaign_type == 2) {
                //schedule once

                $scheduleTime = Carbon::parse($request->schedule_date);
                $currentTime = Carbon::parse(now()->format('Y-m-d'));
                $totalDuration = $currentTime->diff($scheduleTime);

                SplitTestingJob::dispatch($auth, $request->all(), $emailCampaign->id, $job_code)->delay($totalDuration);
            } elseif ($request->campaign_type == 3) {
                //schedule recursively

                if ($request->recursive_campaign_type == 1) {
                    //weekly

                    $dayOfWeek = Carbon::parse('next ' . config('constants.days_of_week')[$request->day_of_week]); //->toDateString();
                    $currentTime = Carbon::parse(now()->format('Y-m-d'));
                    $totalDuration = $currentTime->diff($dayOfWeek);

                    SplitTestingJob::dispatch($auth, $request->all(), $emailCampaign->id, $job_code)->delay($totalDuration);
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

                    SplitTestingJob::dispatch($auth, $request->all(), $emailCampaign->id, $job_code)->delay($totalDuration);
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

                    SplitTestingJob::dispatch($auth, $request->all(), $emailCampaign->id, $job_code)->delay($totalDuration);
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

        $result = EmailCampaign::find(Hashids::decode($request->_id)[0]);

        $result['split_subject_line_1'] = SplitTestSubject::where('id', $result['split_subject_line_1'])->first();
        $result['split_subject_line_2'] = SplitTestSubject::where('id', $result['split_subject_line_2'])->first();

        return response()->json([
            'data' => EmailCampaignResource::collection([$result->makeHidden(['track_opens', 'track_clicks', 'status', 'id'])])[0],
            // 'data' => $result->makeHidden(['track_opens', 'track_clicks', 'status']),
            'status' => 1,
            'message' => "Edit Campaign!",
        ], 200, ['Content-Type' => 'application/json']);
    }

    /**
     * Display a listing of the split testing campaign.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    //public function campaignReportListing(Request $request)
    public function index(Request $request)
    {

        $reports = EmailCampaign::where(['user_id' => auth()->user()->id, 'is_split_testing' => 1]);

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
                'message' => 'Split testing listing!',
                'status' => 1,
            ]);
    }

    public function getUserGroups(Request $request)
    {
        $groups = auth()->user()->groups;

        return response()->json([
            'data' => $groups,
            'status' => 1,
            'message' => "Contact Groups!",
        ], 200, ['Content-Type' => 'application/json']);
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

    public function campaignCounter(Request $req)
    {

        $campaigns = EmailCampaign::where(['user_id' => auth()->user()->id, 'is_split_testing' => 1])->get();

        $total = $campaigns->count();
        $draft = $campaigns->where('status', 2)->count();
        $deleted = $campaigns->where('status', 3)->count();
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

    private function sendTestCampaign($template, $subject, $template2, $subject2, $email_list, $sender_name, $sender_email, $reply_to_email)
    {
        $contactConversion = (int)((50 / 100) * count($email_list));
        $i = 0;
        foreach ($email_list as $email) {
            if ($contactConversion >= ($i + 1)) {
                $emailTemplate = EmailCampaignTemplate::where('id', $template)->first();
                SendCapmaignMail::dispatch([], [$sender_name, $sender_email, $reply_to_email], $email, $subject, $emailTemplate->html_content);
            } else {
                $emailTemplate = EmailCampaignTemplate::where('id', $template2)->first();
                SendCapmaignMail::dispatch([], [$sender_name, $sender_email, $reply_to_email], $email, $subject2, $emailTemplate->html_content);
            }
            $i++;
        }
    }

    /**
     * Display the specified resource report.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function report($id)
    {
        $id = Hashids::decode($id)[0];
        $EmailCampaign = EmailCampaign::where('id', $id)->where('status', '>=', 4)->first();
        if ($EmailCampaign && $EmailCampaign->user_id == auth()->user()->id) {
            $allreports = CampaignHistory::where('type', 2)->where('campaign_id', $id)->get();
            $reports = CampaignHistory::where('type', 2)->where('campaign_id', $id)->paginate(20);

            return CampaignHistoryResource::collection($reports)->additional([
                'allreports' => CampaignHistoryResource::collection($allreports),
                'campaign' => EmailCampaignResource::collection([$EmailCampaign])[0],
                'message' => 'Split Campaign Report',
                'status' => 1,
            ]);
        }
        return response("id not found", 401);
    }

    public function getSubject($id)
    {
        $subject = SplitTestSubject::find($id);
        return $subject;
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
                // recursive
                // if ($EmailCampaign->created_at->format('m') ==  date('m')) {
                // dd("stop and give back");
                $total_contacts = (count($contacts) - $excluding) * $EmailCampaign->no_of_time;
                // }
                $history = CampaignHistory::where('type', 2)->where('campaign_id', $EmailCampaign->id)->first();
                if ($history) {
                    $EmailCampaign->update(['status' => 6, 'sending_stopped_at' => now(), 'job_code' => $job_code]);
                    // saving stopped history
                    User_log::create([
                        'user_id' => $EmailCampaign->user_id,
                        'item_id' => $EmailCampaign->id,
                        'module' => 6,
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
                    $email_span1_start = PackageSettingsValue(1, 'start_range');
                    $email_span1_end = PackageSettingsValue(1, 'end_range');
                    $email_span1_price = PackageSettingsValue(1, 'price_without_vat');
                    $email_span2_start = PackageSettingsValue(2, 'start_range');
                    $email_span2_end = PackageSettingsValue(2, 'end_range');
                    $email_span2_price = PackageSettingsValue(2, 'price_without_vat');
                    $email_span3_start = PackageSettingsValue(3, 'start_range');
                    $email_span3_price = PackageSettingsValue(3, 'price_without_vat');

                    if ($subscription->email_used - $total_contacts < 0)
                        $val = 0;
                    else
                        $val = $subscription->email_used - $total_contacts;
                    $subscription->update(['email_used' => $val]);
                    $subscription->emails_paying_for -= $total_contacts;
                    if ($subscription->emails_paying_for < 0)
                        $subscription->emails_paying_for = 0;

                    // if ($subscription->email_used <= $email_span1_end) {
                    $subscription->emails_to_pay -= ($email_span1_price * $total_contacts);
                    // } else if ($subscription->email_used <= $email_span2_end) {
                    //     $subscription->emails_to_pay -= ($email_span2_price * $total_contacts);
                    // } else if ($subscription->email_used >= $email_span3_start) {
                    //     $subscription->emails_to_pay -= ($email_span3_price * $total_contacts);
                    // }

                    if ($subscription->emails_to_pay < 0)
                        $subscription->emails_to_pay = 0;
                    $subscription->save();
                }
            }

            // saving stopped history
            User_log::create([
                'user_id' => $EmailCampaign->user_id,
                'item_id' => $EmailCampaign->id,
                'module' => 6,
                'log_type' => 12,
            ]);
            return response("Campaign Stopped", 200);
        }
        return response("Campaign not found", 404);
    }
}
