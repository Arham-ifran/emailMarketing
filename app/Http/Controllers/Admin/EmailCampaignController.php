<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ReportClicksExport;
use App\Exports\ReportContactsExport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\EmailCampaignResource;
use App\Http\Resources\CampaignHistoryResource;
use App\Http\Resources\ContactResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\Admin\Country;
use App\Models\Admin\EmailTemplate;
use App\Models\CampaignHistory;
use App\Models\Contact;
use App\Models\EmailCampaign;
use App\Models\EmailCampaignClick;
use App\Models\EmailCampaignLogs;
use App\Models\EmailCampaignOpen;
use App\Models\Group;
use App\Models\SmsCampaign;
use Carbon\Carbon;
use Auth;
use Hashids;
use File;
use Storage;
use Session;
use Hash;
use DB;
use DataTables;
use Maatwebsite\Excel\Facades\Excel;

class EmailCampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        if (!have_right(35))
            access_denied();

        if ($request->ajax()) {
            $db_record = new EmailCampaign();
            $db_record = $db_record->where('is_split_testing', 0);


            if ($request->has('search') && $request->search != "") {
                $users = User::where('name', 'LIKE', '%' . $request->search . '%')->orWhere('email', 'LIKE', '%' . $request->search . '%')->get('id');
                if ($request->has('userid') && $request->userid != "") {
                    $db_record = $db_record->where('name', 'LIKE', '%' . $request->search . '%');
                } else {
                    $db_record = $db_record->whereIn('user_id', $users)->orWhere('name', 'LIKE', '%' . $request->search . '%');
                }
            }

            if ($request->has('userid') && $request->userid != "") {
                $userid = Hashids::decode($request->userid);
                if ($userid[0])
                    $db_record = $db_record->where('user_id', $userid[0]);
            }

            if ($request->has('status') && $request->status != "") {
                $db_record = $db_record->where('status', $request->status);
            }

            $db_record =  $db_record->orderBy('created_at', 'DESC');
            $datatable = DataTables::of($db_record);
            $datatable = $datatable->addIndexColumn();
            $datatable = $datatable->addColumn('sending_type', function ($row) {
                if ($row->campaign_type && $row->campaign_type == 1)
                    return "Immidiate";
                else if ($row->campaign_type && $row->campaign_type == 2)
                    return "Scheduled";
                else if ($row->campaign_type && $row->campaign_type == 3)
                    return "Recursive";
                else
                    return "";
            });
            $datatable = $datatable->editColumn('status', function ($row) {
                $status = '<span class="label label-danger">Disable</span>';
                if ($row->status == 1 || $row->status == 4 || $row->status == 5) {
                    $status = '<span class="label label-success">' . (($row->status == 1) ? 'Active' : ($row->status == 4 ? 'Sending' : 'Sent')) . '</span>';
                } else if ($row->status == 2) {
                    $status = '<span class="label label-warning">Draft</span>';
                } else if ($row->status == 3) {
                    $status = '<span class="label label-danger">Disabled</span>';
                } else if ($row->status == 7 || $row->status == 6) {
                    $status = '<span class="label label-info">' . (($row->status == 6) ? 'Stopped' : 'Processing') . '</span>';
                }
                return $status;
            });
            $datatable = $datatable->addColumn('action', function ($row) {
                $actions = '<span class="actions">';

                if (have_right(36) && ($row->status == 4 || $row->status == 5)) {
                    $actions .= '&nbsp;<a class="btn btn-primary" target="_blank" href="' . url("admin/email-campaigns/" . Hashids::encode($row->id) . '/view') . '" title="View Report"><i class="fa fa-list-alt"></i></a>';
                }

                $actions .= '</span>';
                return $actions;
            });

            $datatable = $datatable->addColumn('user_email', function ($row) {
                $user = User::where('id', $row->user_id)->first();
                $user_email = "";
                if ($user) {
                    $user_email = $user->email;
                }
                return $user_email;
            });

            $datatable = $datatable->rawColumns(['status', 'action']);
            $datatable = $datatable->make(true);
            return $datatable;
        }

        $data = [];
        return view('admin.email-campaign.index', $data);
    }


    /**
     * Show the report of the resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function report($id)
    {
        if (!have_right(36))
            access_denied();

        $id = Hashids::decode($id)[0];
        $data['action'] = "View";
        $campaign = EmailCampaign::findOrFail($id);
        $data['campaign'] = EmailCampaignResource::collection([$campaign])[0];
        // other data
        $allreports = CampaignHistory::where('type', 2)->where('campaign_id', $id)->get();
        $data['reports'] = CampaignHistoryResource::collection($allreports)->resolve();
        $data['group'] = Group::get();
        return view('admin.email-campaign.report')->with($data);
    }

    /**
     * Show the report details of the resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function history(Request $request, $id, $hid)
    {
        if (!have_right(35))
            access_denied();
        $id = Hashids::decode($id)[0];
        $history = Hashids::decode($hid)[0];

        if ($request->ajax()) {

            if ($request->has('module') && $request->module != "") {
                if ($request->module == 1) { // sent to
                    $db_record = new EmailCampaignLogs();
                    $db_record = $db_record->where('history_id', $history);
                    $db_record = $db_record->pluck('contact_id')->toArray();
                } elseif ($request->module == 2) { // success
                    $db_record = new EmailCampaignLogs();
                    $db_record = $db_record->where('history_id', $history)->where('sent_at', '!=', NULL);
                    $db_record = $db_record->pluck('contact_id')->toArray();
                } elseif ($request->module == 22) { // fail
                    $db_record = new EmailCampaignLogs();
                    $db_record = $db_record->where('history_id', $history)->where('failed_at', '!=', NULL);
                    $db_record = $db_record->pluck('contact_id')->toArray();
                } elseif ($request->module == 3) { //unopeners
                    $allopens = EmailCampaignOpen::where('history_id', $history);
                    $opens = $allopens->pluck('contact_id');
                    $uniqueOpens = array_unique($opens->toArray());
                    $db_record = new EmailCampaignLogs();
                    $db_record = $db_record->where('history_id', $history)->where('sent_at', '!=', NULL);
                    $db_record = $db_record->whereNotIn('contact_id', $uniqueOpens);
                    $db_record = $db_record->pluck('contact_id')->toArray();
                } elseif ($request->module == 4) { //openers
                    $allopens = EmailCampaignOpen::where('history_id', $history);
                    $opens = $allopens->pluck('contact_id');
                    $uniqueOpens = array_unique($opens->toArray());
                    $db_record = new EmailCampaignLogs();
                    $db_record = $db_record->where('history_id', $history)->where('sent_at', '!=', NULL);
                    $db_record = $db_record->whereIn('contact_id', $uniqueOpens);
                    $db_record = $db_record->pluck('contact_id')->toArray();
                } elseif ($request->module == 5) { //clicks
                    $allclicks = EmailCampaignClick::where('history_id', $history);
                    $clicks = $allclicks->pluck('contact_id');
                    $uniqueClicks = array_unique($clicks->toArray());
                    $db_record = new EmailCampaignLogs();
                    $db_record = $db_record->where('history_id', $history)->where('sent_at', '!=', NULL);
                    $db_record = $db_record->whereIn('contact_id', $uniqueClicks);
                    $db_record = $db_record->pluck('contact_id')->toArray();
                } elseif ($request->module == 6) { //unsubscribers
                    $db_record = new EmailCampaignLogs();
                    $db_record = $db_record->where('history_id', $history);
                    $db_record = $db_record->pluck('contact_id')->toArray();
                    $contacts = new Contact();
                    $db_record = $contacts->where('subscribed', 0)->whereIn('id', $db_record)->pluck('id')->toArray();
                } elseif ($request->module == 7) { // click logs
                    $db_record = EmailCampaignClick::where('history_id', $history)->with('contact');
                    $datatable = DataTables::of($db_record);
                    $datatable = $datatable->addIndexColumn();
                    $datatable = $datatable->make(true);
                    return $datatable;
                } elseif ($request->module == 8) { // bounced
                    $db_record = new EmailCampaignLogs();
                    $db_record = $db_record->where('history_id', $history)->where('bounced_at', '!=', NULL);
                    $db_record = $db_record->pluck('contact_id')->toArray();
                }
            }

            $contacts = new Contact();
            $db_record = $contacts->whereIn('id', $db_record);
            $datatable = DataTables::of($db_record);
            $datatable = $datatable->addIndexColumn();
            $datatable = $datatable->make(true);
            return $datatable;
        }

        $data = [];
        $campaign = EmailCampaign::where('id', $id)->first();
        $history = CampaignHistory::where('type', 2)->where('id', $history)->first();
        $data['campaign'] = $campaign;
        $data['history'] = json_encode(CampaignHistoryResource::collection([$history])[0]);

        $openss = EmailCampaignOpen::where('history_id', $history->id);
        $totalOpens = $openss->count();
        $openss = $openss->pluck('contact_id');
        $totalUniqueOpens = count(array_unique($openss->toArray()));
        $data['totalOpens'] = $totalOpens;
        $data['uniqueOpens'] = $totalUniqueOpens;

        $clickss = EmailCampaignClick::where('history_id', $history->id);
        $clickss = $clickss->pluck('contact_id');
        $totalUniqueClicks = count(array_unique($clickss->toArray()));
        $data['uniqueClicks'] = $totalUniqueClicks;

        $opensData = EmailCampaignOpen::where('history_id', $history->id)->select('id', 'created_at')
            // $opensData = EmailCampaignOpen::select('id', 'created_at')
            ->get()
            ->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('d-m-y'); // grouping by days
            });
        $keys = array_keys($opensData->toArray());
        $data['opensDataKeys'] = $keys;
        $vals = [];
        foreach ($opensData as $openday) {
            array_push($vals, count($openday->toArray()));
        };
        $data['opensData'] = $vals;

        return view('admin.email-campaign.history', $data);
    }

    /**
     * Display the specified resource report.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function reportDataDownload(Request $request, $id, $history)
    {
        $id = Hashids::decode($id)[0];
        $EmailCampaign = EmailCampaign::where('id', $id)->where('status', '>=', 4)->first();
        $history = Hashids::decode($history)[0];
        $report = CampaignHistory::where('type', 2)->where('id', $history)->first();
        if ($EmailCampaign && $report) {
            $allreport = CampaignHistory::where('type', 2)->where('id', $history)->with('sent_to', 'success', 'fail', 'bounces', 'unsubscribers')->first();

            // modules: 1:sent_to, 2:success, 3:unopened, 4:opendata, 5:clickdata, 6:unsubscribers, 7:clicklogs, 8:bounces

            if ($request->module == 7) {
                $clicks = EmailCampaignClick::where('history_id', $history)->with('contact')->whereRelation('contact', 'email', 'LIKE', '%' . $request->search . '%')->orderBy('created_at', 'ASC')->get();
                return Excel::download(new ReportClicksExport($clicks, $request), 'report-clicks.xlsx');
            }
            if ($request->module == 8) {
                return Excel::download(new ReportContactsExport($allreport->bounces, $request), 'bounced.xlsx');
            }

            $allopens = EmailCampaignOpen::where('history_id', $history);
            $opensContacts = $allopens->pluck('contact_id');
            $allclicks = EmailCampaignClick::where('history_id', $history);
            $clicksContacts = $allclicks->pluck('contact_id');
            $uniqueOpens = array_unique($opensContacts->toArray());
            $uniqueClicks = array_unique($clicksContacts->toArray());

            if ($request->module == 1) {
                return Excel::download(new ReportContactsExport($report->sent_to()->get(), $request), 'sent_to.xlsx');
            }
            if ($request->module == 2) {
                return Excel::download(new ReportContactsExport($report->success, $request), 'success.xlsx');
            }
            if ($request->module == 22) {
                return Excel::download(new ReportContactsExport($report->fail, $request), 'fail.xlsx');
            }
            if ($request->module == 3) {
                return Excel::download(new ReportContactsExport($report->success()->whereNotIn('contact_id', $uniqueOpens)->get(), $request), 'unopens.xlsx');
            }
            if ($request->module == 4) {
                $contacts_data = Contact::whereIn('id', $uniqueOpens)->get();
                return Excel::download(new ReportContactsExport($contacts_data, $request), 'opens.xlsx');
            }
            if ($request->module == 5) {
                $contacts_data = Contact::whereIn('id', $uniqueClicks)->get();
                return Excel::download(new ReportContactsExport($contacts_data, $request), 'clickers.xlsx');
            }
            if ($request->module == 6) {
                return Excel::download(new ReportContactsExport($allreport->unsubscribers, $request), 'subscribers.xlsx');
            }
        }
    }
}
