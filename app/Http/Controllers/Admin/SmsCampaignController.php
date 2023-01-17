<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ReportContactsExport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CampaignHistoryResource;
use App\Http\Resources\SmsCampaignResource;
use App\Models\User;
use App\Models\CampaignHistory;
use App\Models\Contact;
use App\Models\Group;
use App\Models\SmsCampaign;
use App\Models\SmsCampaignLogs;
use Hashids;
use DB;
use DataTables;
use Maatwebsite\Excel\Facades\Excel;
use Twilio\TwiML\Voice\Sms;

class SmsCampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        if (!have_right(33))
            access_denied();

        if ($request->ajax()) {
            $db_record = new SmsCampaign();

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
            $datatable = Datatables::of($db_record);
            $datatable = $datatable->addIndexColumn();
            $datatable = $datatable->editColumn('status', function ($row) {
                $status = '<span class="label label-danger">Disable</span>';
                if ($row->status == 5 || $row->status == 2 || $row->status == 3) {
                    $status = '<span class="label label-success">' . (($row->status == 5) ? 'Active' : ($row->status == 2 ? 'Sending' : 'Sent')) . '</span>';
                } else if ($row->status == 1) {
                    $status = '<span class="label label-warning">Draft</span>';
                } else if ($row->status == 4) {
                    $status = '<span class="label label-danger">Disabled</span>';
                } else if ($row->status == 6 || $row->status == 7) {
                    $status = '<span class="label label-info">' . (($row->status == 6) ? 'Stopped' : 'Processing') . '</span>';
                }
                return $status;
            });
            $datatable = $datatable->addColumn('sending_type', function ($row) {
                if ($row->type && $row->type == 1)
                    return "Immidiate";
                else if ($row->type && $row->type == 2)
                    return "Scheduled";
                else if ($row->type && $row->type == 3)
                    return "Recursive";
                else
                    return "";
            });
            $datatable = $datatable->addColumn('action', function ($row) {
                $actions = '<span class="actions">';

                if (have_right(34) && ($row->status == 2 || $row->status == 3)) {
                    $actions .= '&nbsp;<a class="btn btn-primary" target="_blank" href="' . url("admin/sms-campaigns/" . Hashids::encode($row->id) . '/view') . '" title="View Report"><i class="fa fa-list-alt"></i></a>';
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
        $package_id = '';

        $data['package_id'] = $package_id;
        $data['packages'] = [];

        return view('admin.sms-campaign.index', $data);
    }


    /**
     * Show the report of the resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function report($id)
    {
        if (!have_right(34))
            access_denied();

        $id = Hashids::decode($id)[0];
        $data['action'] = "View";
        $campaign = SmsCampaign::findOrFail($id);
        $data['campaign'] = SmsCampaignResource::collection([$campaign])[0];
        // other data
        $allreports = CampaignHistory::where('type', 1)->where('campaign_id', $id)->get();
        $data['reports'] = CampaignHistoryResource::collection($allreports)->resolve();
        $data['group'] = Group::get();
        return view('admin.sms-campaign.report')->with($data);
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
                    $db_record = new SmsCampaignLogs();
                    $db_record = $db_record->where('history_id', $history);
                    $db_record = $db_record->pluck('contact_id')->toArray();
                } elseif ($request->module == 2) { // success
                    $db_record = new SmsCampaignLogs();
                    $db_record = $db_record->where('history_id', $history)->where('sent_at', '!=', NULL);
                    $db_record = $db_record->pluck('contact_id')->toArray();
                } elseif ($request->module == 3) { // fail
                    $db_record = new SmsCampaignLogs();
                    $db_record = $db_record->where('history_id', $history)->where('failed_at', '!=', NULL);
                    $db_record = $db_record->pluck('contact_id')->toArray();
                }
            }

            // dd($id, $history);

            $contacts = new Contact();
            $db_record = $contacts->whereIn('id', $db_record);
            $datatable = DataTables::of($db_record);
            $datatable = $datatable->addIndexColumn();
            $datatable = $datatable->make(true);
            return $datatable;
        }

        $data = [];
        $campaign = SmsCampaign::where('id', $id)->first();
        $data['campaign'] = $campaign;
        $history = CampaignHistory::where('type', 1)->where('id', $history)->first();
        $data['history'] = json_encode(CampaignHistoryResource::collection([$history])[0]);
        return view('admin.sms-campaign.history', $data);
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
        $SmsCampaign = SmsCampaign::where('id', $id)->first();
        $history = Hashids::decode($history)[0];
        $report = CampaignHistory::where('type', 1)->where('id', $history)->first();
        if ($SmsCampaign && $report) {
            $allreport = CampaignHistory::where('type', 1)->where('id', $history)->with('sms_sent_to', 'sms_success', 'sms_fail',)->first();

            // modules: 1:sent_to, 2:success

            if ($request->module == 1) {
                return Excel::download(new ReportContactsExport($allreport->sms_sent_to, $request), 'sent_to.xlsx');
            }
            if ($request->module == 2) {
                return Excel::download(new ReportContactsExport($allreport->sms_success, $request), 'success.xlsx');
            }
            if ($request->module == 3) {
                return Excel::download(new ReportContactsExport($allreport->sms_fail, $request), 'fail.xlsx');
            }
        }
    }
}
