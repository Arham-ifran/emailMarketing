<?php

namespace App\Http\Controllers;

use App\CustomClasses\TranslationHandler;
use App\Http\Resources\ContactResource;
use App\Http\Resources\EmailCampaignResource;
use App\Http\Resources\PackageResource;
use App\Http\Resources\SmsCampaignResource;
use App\Models\Admin\Package;
use App\Models\Admin\PackageSubscription;
use App\Models\CampaignHistory;
use App\Models\Contact;
use App\Models\EmailCampaign;
use App\Models\EmailCampaignClick;
use App\Models\Group;
use App\Models\SmsCampaign;
use App\Models\SmsCampaignLogs;
use App\Models\EmailCampaignLogs;
use App\Models\EmailCampaignOpen;
use App\Models\EmailCampaignTemplate;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PharIo\Manifest\Email;
use Hashids;
use PDF;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['track', 'click']]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    /**
     * Get the application dashboard data.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function dashboard(Request $request)
    {
        $id = Auth()->user()->id;

        // contacts and groups data on dashboard
        $allcontacts = Contact::where('user_id', $id)->get();
        $allgroups = Group::where('user_id', $id)->get();
        $templates = EmailCampaignTemplate::where('user_id', $id)->count();
        $subscribers = $allcontacts->where('subscribed', 1)->count();
        $unsubscribers = $allcontacts->where('subscribed', 0)->count();
        $contacts = $allcontacts->count();
        $groups = $allgroups->count();

        // sms dashboard data
        $smsData = DB::table('sms_logs')->where('user_id', $id)->where(DB::raw('MONTH(created_at)'), '=', date("m"))->orderBy('created_at', 'desc')
            ->select(DB::raw("DATE_FORMAT(created_at,'%d/%m') as name"), DB::raw("(COUNT(sent_at)) as '" . TranslationHandler::getTranslation($request->lang, 'Sent') . "'"), DB::raw("(COUNT(failed_at)) as '" . TranslationHandler::getTranslation($request->lang, 'failed') . "'"))
            ->orderBy('created_at')->groupBy(DB::raw("DAY(created_at)"))->take(14)->get();

        // foreach ($smsData as $row) {
        //     $row->name = TranslationHandler::getTranslation($request->lang, $row->name);
        // }

        $smsCampaigns = SmsCampaign::where('user_id', $id)->get();
        $total_smsCampaigns = $smsCampaigns->count();
        $sent_smsCampaigns = $smsCampaigns->where('status', 3)->count();
        $scheduled_smsCampaigns = $smsCampaigns->where('type', 2)->where('status', 5)->count();
        $recursive_smsCampaigns = $smsCampaigns->where('type', 3)->whereIn('status', [5, 2])->count();
        $draft_smsCampaigns = $smsCampaigns->where('status', 1)->count();

        $smsCampaignsData = [
            ['name' => TranslationHandler::getTranslation($request->lang, 'Total'), 'value' => $total_smsCampaigns],
            ['name' => TranslationHandler::getTranslation($request->lang, 'Sent'), 'value' => $sent_smsCampaigns],
            ['name' => TranslationHandler::getTranslation($request->lang, 'Drafts'), 'value' => $draft_smsCampaigns],
            ['name' => TranslationHandler::getTranslation($request->lang, 'Scheduled'), 'value' => $scheduled_smsCampaigns],
            ['name' => TranslationHandler::getTranslation($request->lang, 'Recursive'), 'value' => $recursive_smsCampaigns],
        ];

        // Email data
        $emailData1 = [];
        $date = now();

        for ($i = 13; $i >= 0; $i--) {
            array_push($emailData1, ['name' => now()->subDays($i)->format('d/m'), 'date' => now()->subDays($i)->format('Y-m-d')]);
        }

        $click_text = TranslationHandler::getTranslation($request->lang, 'Link Clicks');
        $open_text = TranslationHandler::getTranslation($request->lang, 'Open Rate');

        $emailData = [];
        $camps = EmailCampaign::where('user_id', $id)->pluck('id')->toArray();
        foreach ($emailData1 as $day) {
            $dt =  Carbon::parse($day['date'])->format('Y-m-d');
            $clicks = EmailCampaignClick::whereIn('campaign_id', $camps)->whereDate('created_at', '=', $dt)->count();
            $opens = EmailCampaignOpen::whereIn('campaign_id', $camps)->whereDate('created_at', '=', $dt)->count();
            array_push($emailData, array_merge($day, [$click_text => $clicks, $open_text => $opens]));
        }

        // $emailData = DB::table('email_campaigns')->where('user_id', $id)->where(DB::raw('MONTH(created_at)'), '=', date("m"))->orderBy('created_at', 'desc')
        //     ->select(DB::raw("DATE_FORMAT(created_at,'%d/%m') as name"), DB::raw("(SUM(track_opens)) as '" . TranslationHandler::getTranslation($request->lang, 'Open Rate') . "'"), DB::raw("(SUM(track_clicks)) as '" . TranslationHandler::getTranslation($request->lang, 'Link Clicks') . "'"))
        //     ->orderBy('created_at')->groupBy(DB::raw("DAY(created_at)"))->take(14)->get();

        // foreach ($emailData as $row) {
        //     $row->name = TranslationHandler::getTranslation($request->lang, $row->name);
        // }

        $emailCampaigns = EmailCampaign::where('user_id', $id)->where('is_split_testing', 0)->where('status', '!=', 3)->get();
        $total_emailCampaigns = $emailCampaigns->count();
        $sent_emailCampaigns = $emailCampaigns->where('status', 5)->count();
        $scheduled_emailCampaigns = $emailCampaigns->where('campaign_type', 2)->where('status', 1)->count();
        $recursive_emailCampaigns = $emailCampaigns->where('campaign_type', 3)->whereIn('status', [1, 4])->count();
        $draft_emailCampaigns = $emailCampaigns->where('status', 2)->count();
        // $split_emailCampaigns = $emailCampaigns->where('is_split_testing', 1)->count();

        $emailCampaignsData = [
            ['name' => TranslationHandler::getTranslation($request->lang, 'Total'), 'value' => $total_emailCampaigns],
            ['name' => TranslationHandler::getTranslation($request->lang, 'Sent'), 'value' => $sent_emailCampaigns],
            ['name' => TranslationHandler::getTranslation($request->lang, 'Drafts'), 'value' => $draft_emailCampaigns],
            ['name' => TranslationHandler::getTranslation($request->lang, 'Scheduled'), 'value' => $scheduled_emailCampaigns],
            ['name' => TranslationHandler::getTranslation($request->lang, 'Recursive'), 'value' => $recursive_emailCampaigns],
            // ['name' => 'Split Testing', 'value' => $split_emailCampaigns],
        ];

        $splitData = [];
        $splitCampaigns = [];
        $splits = EmailCampaign::where('user_id', $id)->where('status', '!=', 3)->where('is_split_testing', 1)->whereIn('status', [4, 5])->orderBy('created_at', 'desc')->get();
        foreach ($splits as $split) {
            $clicks1 = $opens1 = $clicks2 = $opens2 = 0;
            if ($split) {
                // get contacts and divide into two groups.
                $logs = EmailCampaignLogs::where('user_id', $id)->where('campaign_id', $split->id)->get();
                if ($logs)
                    if ($logs[0]->content_id) {
                        // by content
                        // get unique content ids
                        $cont_ids = $logs->pluck('content_id')->toArray();
                        $contents = array_unique($cont_ids);
                        if ($contents) {
                            $sub_contacts1 = $logs->where('content_id', $contents[0])->pluck('contact_id')->toArray();
                            $clicks1 = EmailCampaignClick::where('campaign_id', $split->id)->whereIn('contact_id', $sub_contacts1)->count();
                            $opens1 = EmailCampaignOpen::where('campaign_id', $split->id)->whereIn('contact_id', $sub_contacts1)->count();

                            if (isset($contents[1])) {
                                $sub_contacts2 = $logs->where('content_id', $contents[1])->pluck('contact_id')->toArray();
                                $clicks2 = EmailCampaignClick::where('campaign_id', $split->id)->whereIn('contact_id', $sub_contacts2)->count();
                                $opens2 = EmailCampaignOpen::where('campaign_id', $split->id)->whereIn('contact_id', $sub_contacts2)->count();
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
                            $clicks1 = EmailCampaignClick::where('campaign_id', $split->id)->whereIn('contact_id', $sub_contacts1)->count();
                            $opens1 = EmailCampaignOpen::where('campaign_id', $split->id)->whereIn('contact_id', $sub_contacts1)->count();

                            if (isset($subjects[1])) {
                                $sub_contacts2 = $logs->where('subject_id', $subjects[1])->pluck('contact_id')->toArray();
                                $clicks2 = EmailCampaignClick::where('campaign_id', $split->id)->whereIn('contact_id', $sub_contacts2)->count();
                                $opens2 = EmailCampaignOpen::where('campaign_id', $split->id)->whereIn('contact_id', $sub_contacts2)->count();
                            } else {
                                // stays 0
                            }
                        } else {
                            // both stay 0
                        }
                    }
            }
            // $splitData = [[['name' => "Section 1", 'Open Rate' => $opens1, 'Link Clicks' => $clicks1], ['name' => "Section 2", 'Open Rate' => $opens2, 'Link Clicks' => $clicks2]]];
            array_push($splitData, [['name' => TranslationHandler::getTranslation($request->lang, 'Section 1'), TranslationHandler::getTranslation($request->lang, 'Open Rate') => $opens1, TranslationHandler::getTranslation($request->lang, 'Link Clicks') => $clicks1], ['name' => TranslationHandler::getTranslation($request->lang, 'Section 2'), TranslationHandler::getTranslation($request->lang, 'Open Rate') => $opens2, TranslationHandler::getTranslation($request->lang, 'Link Clicks') => $clicks2]]);
            array_push($splitCampaigns, $split->name);
            // $splitData = [['name' => $split->name, 'Open Rate' => $split->track_opens, 'Link Clicks' => $split->track_clicks]];
        }

        $response = [
            'contacts' => $contacts,
            'lists' => $groups,
            'templates' => $templates,
            'subscribers' => $subscribers,
            'unsubscribers' => $unsubscribers,
            'smsData' => $smsData,
            'smsCampaignsData' => $smsCampaignsData,
            'emailData' => $emailData,
            'emailCampaignsData' => $emailCampaignsData,
            'splitCampaigns' => $splitCampaigns,
            'splitData' => $splitData,
            'message' => "dashboard",
        ];
        return response($response, 201);
    }

    /**
     * Get the application analytics.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function analytics(Request $request)
    {
        // code
        $id = Auth()->user()->id;

        $recent_sms_campaigns = NULL;
        $recent_email_campaigns = NULL;
        $recent_split_campaigns = NULL;

        // sms campaign analytics
        $allcontacts = Contact::where('user_id', $id)->get();
        $smsCampaigns = SmsCampaign::where('user_id', $id)->with('reports')->orderBy('id', 'desc')->get();
        $sms_success = SmsCampaignLogs::where('user_id', $id)->where('sent_at', '!=', NULL)->count();
        $sms_fail = SmsCampaignLogs::where('user_id', $id)->where('failed_at', '!=', NULL)->count();
        $CompletedSmsCampaigns = $smsCampaigns->whereIn('status', [2, 3, 6]);
        if ($request->module == 0 || $request->module == 2) {
            $recent_sms_campaigns = SmsCampaign::where('user_id', $id)->orderBy('id', 'desc')->whereIn('status', [2, 3, 6]);
            if ($request->has('filt_status') && !empty($request->filt_status)) {
                $recent_sms_campaigns = $recent_sms_campaigns->where('status', $request->filt_status);
            }

            if ($request->has('filt_camp_name') && !empty($request->filt_camp_name)) {
                $recent_sms_campaigns = $recent_sms_campaigns->where('name', 'LIKE', '%' . $request->filt_camp_name . '%');
            }

            if ($request->has('filter_date') && !empty($request->filter_date)) {
                $recent_sms_campaigns = $recent_sms_campaigns->whereDate('created_at', Carbon::parse($request->filter_date));
            }
            $recent_sms_campaigns = $recent_sms_campaigns->paginate(5);
        }
        $total_smsCampaigns = $smsCampaigns->count();
        $sent_smsCampaigns = $CompletedSmsCampaigns->count();

        // dd($request->module);
        // subscribers
        $subscribers = $allcontacts->where('subscribed', 1);

        // email campaign analytics
        $emailCampaigns = EmailCampaign::where('user_id', $id)->where('is_split_testing', 0)->where('status', '!=', 3)->orderBy('id', 'desc')->get();
        $total_email_sent = EmailCampaignLogs::where('user_id', $id)->where('failed_at', NULL)->get()->count();
        $total_click_rate = $emailCampaigns->sum('track_clicks');
        $total_open_rate = $emailCampaigns->sum('track_opens');
        $sent_email_campaigns = EmailCampaign::where('user_id', $id)->where('is_split_testing', 0)->orderBy('id', 'desc')->whereIn('status', [4, 5, 6]);
        $email_avg_click_rate = array_sum($sent_email_campaigns->pluck('track_clicks')->toArray());
        $email_avg_open_rate = array_sum($sent_email_campaigns->pluck('track_opens')->toArray());
        $total_email_campaigns = $sent_email_campaigns->count();
        if ($request->module == 0 || $request->module == 1) {
            if ($request->has('filt_status') && !empty($request->filt_status)) {
                $sent_email_campaigns = $sent_email_campaigns->where('status', $request->filt_status);
            }

            if ($request->has('filt_camp_name') && !empty($request->filt_camp_name)) {
                $sent_email_campaigns = $sent_email_campaigns->where('name', 'LIKE', '%' . $request->filt_camp_name . '%');
            }

            if ($request->has('filter_date') && !empty($request->filter_date)) {
                $sent_email_campaigns = $sent_email_campaigns->whereDate('created_at', Carbon::parse($request->filter_date));
            }
            $recent_email_campaigns = $sent_email_campaigns->paginate(5);
        }

        // split testing analytics
        $splitCampaigns = EmailCampaign::where(['user_id' => $id, 'is_split_testing' => 1])->where('status', '!=', 3)->orderBy('id', 'desc')->get();
        $total_split_email_sent = EmailCampaignLogs::where('user_id', $id)->where('failed_at', NULL)->whereIn('campaign_id', $splitCampaigns->pluck('id')->toArray())->get()->count();
        $total_split_click_rate = $splitCampaigns->sum('track_clicks');
        $total_split_open_rate = $splitCampaigns->sum('track_opens');
        $sent_split_campaigns = EmailCampaign::where(['user_id' => $id, 'is_split_testing' => 1])->orderBy('id', 'desc')->whereIn('status', [4, 5, 6]);
        $total_split_campaigns = $sent_split_campaigns->count();
        $split_avg_click_rate = array_sum($sent_split_campaigns->pluck('track_clicks')->toArray());
        $split_avg_open_rate = array_sum($sent_split_campaigns->pluck('track_opens')->toArray());
        if ($request->module == 0 || $request->module == 3) {
            if ($request->has('filt_status') && !empty($request->filt_status)) {
                $sent_split_campaigns = $sent_split_campaigns->where('status', $request->filt_status);
            }

            if ($request->has('filt_camp_name') && !empty($request->filt_camp_name)) {
                $sent_split_campaigns = $sent_split_campaigns->where('name', 'LIKE', '%' . $request->filt_camp_name . '%');
            }

            if ($request->has('filter_date') && !empty($request->filter_date)) {
                $sent_split_campaigns = $sent_split_campaigns->whereDate('created_at', Carbon::parse($request->filter_date));
            }
            $recent_split_campaigns = $sent_split_campaigns->paginate(5);
        }

        $response = [
            // sms
            'total_sms_campaigns' => $total_smsCampaigns,
            'sms_sent' => $sent_smsCampaigns,
            'sms_sent_successfilly' => $sms_success,
            'sms_sending_fails' => $sms_fail,
            'sms_campaigns' => $recent_sms_campaigns ? SmsCampaignResource::collection($recent_sms_campaigns) : [],
            // subscribers
            'subscribers' => ContactResource::collection($subscribers),
            // 'total_unsubscribers' => $total_unsubscribers,
            // email
            'total_email_sent' => $total_email_sent,
            'email_avg_click_rate' => $email_avg_click_rate,
            'email_avg_open_rate' => $email_avg_open_rate,
            'total_email_campaigns_sent' => $total_email_campaigns,
            'total_click_rate' => $total_click_rate,
            'total_open_rate' => $total_open_rate,
            'email_campaigns' => $recent_email_campaigns ? EmailCampaignResource::collection($recent_email_campaigns) : [],
            // split
            'total_split_email_sent' => $total_split_email_sent,
            'split_avg_click_rate' => $split_avg_click_rate,
            'split_avg_open_rate' => $split_avg_open_rate,
            'total_split_campaigns_sent' => $total_split_campaigns,
            'total_split_click_rate' => $total_split_click_rate,
            'total_split_open_rate' => $total_split_open_rate,
            'split_campaigns' => $recent_split_campaigns ? EmailCampaignResource::collection($recent_split_campaigns) : [],
            // 'lists' => $groups,
            // 'unsubscribers' => $unsubscribers,
            'message' => "analytics"
        ];
        return response($response, 201);
    }

    /**
     * Get the User Api data.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function apiData()
    {
        $id = Auth()->user()->id;
        $user = User::where('id', $id)->first();
        $data = [
            'name' => $user->name,
            'email' => $user->email,
            'endpoint_urls' => json_decode($user->endpoint_urls),
            'secret_key' => $user->secret_key,
            'api_token' => $user->api_token,
            'api_status' => $user->api_status,
        ];
        return response(['data' => $data], 200);
    }

    /**
     * Update the User Api data.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function updateApiData(Request $request)
    {

        $id = Auth()->user()->id;
        $messages = [
            'endpoint_urls.array' => translationByDeepL("This must be an array", $request->lang),
            'api_status.integer' => TranslationHandler::getTranslation($request->lang, 'required'),
            'api_status.min' => TranslationHandler::getTranslation($request->lang, 'required'),
            'api_status.max' => TranslationHandler::getTranslation($request->lang, 'required'),
        ];
        $data = $request->validate([
            'endpoint_urls' => 'array',
            'api_status' => ['integer', 'min:1', 'max:2'],
        ]);
        if (isset($data['endpoint_urls'])) {
            $data['endpoint_urls'] = json_encode($data['endpoint_urls']);
        } else {
            $data['endpoint_urls'] = json_encode([]);
        }
        $user = User::where('id', $id)->first();
        if ($user) {
            $user->update($data);
            $data = [
                'name' => $user->name,
                'email' => $user->email,
                'endpoint_urls' => json_decode($user->endpoint_urls),
                'secret_key' => $user->secret_key,
                'api_token' => $user->api_token,
                'api_status' => $user->api_status,
            ];
            return response(['data' => $data], 200);
        }
        return response(['message' => "User not found", 'errors' => ['user' => ['user not found']]], 422);
    }

    /**
     * Refresh the User Api token.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function refreshApiToken()
    {
        $api_token = '';
        do {
            $api_token = Str::random(132);
        } while (User::where("api_token", $api_token)->first() instanceof User);

        $id = Auth()->user()->id;

        $user = User::where('id', $id)->first();
        if ($user) {
            $user->update(['api_token' => $api_token]);
            return response(['data' => ['new_api_token' => $api_token]], 200);
        }
        return response(['message' => "User not found", 'errors' => ['user' => ['user not found']]], 422);
    }

    /**
     * Refresh the User Api key.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function refreshApiKey()
    {
        do {
            $secret_key = Str::random(20);
        } while (User::where("secret_key", $secret_key)->first() instanceof User);

        $id = Auth()->user()->id;

        $user = User::where('id', $id)->first();
        if ($user) {
            $user->update(['secret_key' => $secret_key]);
            return response(['data' => ['new_secret_key' => $secret_key]], 200);
        }
        return response(['message' => "User not found", 'errors' => ['user' => ['user not found']]], 422);
    }

    public function track(Request $request)
    {
        $params = $request->all();

        $campaign_id = Hashids::decode($params['_id'])[0];
        $contact_id = Hashids::decode($params['referal_id'])[0];
        $history_id = Hashids::decode($params['history_id'])[0];
        $campaign = EmailCampaign::find($campaign_id);
        $history = CampaignHistory::find($history_id);
        $contact = Contact::find($contact_id);

        if ($campaign && $contact && $history) {
            EmailCampaignOpen::create(['campaign_id' => $campaign->id, 'contact_id' => $contact->id, 'history_id' => $history->id, 'created_at' => now()]);
            $campaign->update(['track_opens' => $campaign->track_opens + 1]);
        }

        \Log::info(json_encode($params));
    }

    public function click(Request $request)
    {
        $params = $request->all();
        $campaign_id = Hashids::decode($params['_id'])[0];
        $contact_id = Hashids::decode($params['referal_id'])[0];
        $history_id = Hashids::decode($params['history_id'])[0];
        $campaign = EmailCampaign::find($campaign_id);
        $contact = Contact::find($contact_id);
        $history = CampaignHistory::find($history_id);
        $redirect_link = $params['redirect_to'];

        if ($campaign && $contact && $history) {
            EmailCampaignClick::create(['campaign_id' => $campaign->id, 'link' => $redirect_link, 'contact_id' => $contact->id, 'history_id' => $history->id, 'created_at' => now()]);
            $campaign->update(['track_clicks' => $campaign->track_clicks + 1]);
        }
        return redirect()->away($redirect_link);
    }

    public function downloadApiDocument()
    {
        return response()->download(public_path('downloadables/api_documentation.pdf'));
    }

    public function getUserPackage()
    {
        $user = Auth()->user();
        $subscription = NULL;
        if ($user->package_id)
            return PackageResource::collection(Package::where('id', $user->package_id)->where('status', 1)->orderBy('monthly_price')->get())[0]
                ->additional([
                    'message' => 'Packages Listing',
                    'status'  => 1
                ]);

        return response([
            'data' => NULL,
            'message' => 'Packages Listing',
            'status'  => 1
        ], 404);
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        return response("Done", 200);
    }
}
