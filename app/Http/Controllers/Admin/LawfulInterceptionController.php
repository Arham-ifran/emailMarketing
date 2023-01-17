<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobs\ArchiveUserAllDataJob;
use App\Jobs\ArchiveUserFilesJob;
use App\Models\Admin\PackageSubscription;
use App\Models\Admin\Payment;
use App\Models\EmailCampaign;
use App\Models\SmsCampaign;
use App\Models\User;
use DataTables;
use Session;
use DB;
use File;
use Storage;
use Hashids;
use PDF;
use Carbon\Carbon;
use ZipArchive;

class LawfulInterceptionController extends Controller
{
      /**
       * Create a new LawfulInterceptionController instance.
       *
       * @return void
       */

      public function index(Request $request)
      {
            if (!have_right(133))
                  access_denied();

            $data = [];

            if ($request->ajax()) {
                  $db_record = new User();

                  if ($request->has('search') && !empty($request->search)) {
                        $db_record = $db_record->where(function ($q) use ($request) {
                              $q->where('name', 'LIKE', '%' . $request->search . '%')
                                    ->orWhere('email', 'LIKE', '%' . $request->search . '%');
                        });
                  } else {
                        $db_record =  $db_record->whereNull('id');
                  }

                  $datatable = Datatables::of($db_record);
                  $datatable = $datatable->addIndexColumn();

                  $datatable = $datatable->editColumn('status', function ($row) {
                        $status = '<span class="label label-danger">Disable</span>';
                        if ($row->status == 1) {
                              $status = '<span class="label label-success">Active</span>';
                        } else if ($row->status == 2) {
                              $status = '<span class="label label-warning">Unverified</span>';
                        } else if ($row->status == 3) {
                              $status = '<span class="label label-danger">Deleted</span>';
                        }

                        return $status;
                  });
                  $datatable = $datatable->addColumn('action', function ($row) {
                        $actions = '<span class="actions">';

                        if (have_right(134)) {
                              $actions .= '&nbsp;<a title="User Details PDF" class="btn btn-primary" target="_blank" href="' . url("admin/lawful-interception/user-details-pdf/" . Hashids::encode($row->id)) . '"><i class="fa fa-user"></i></a>';
                        }

                        if (have_right(135)) {
                              $actions .= '&nbsp;<a title="User Payments PDF" class="btn btn-primary" target="_blank" href="' . url("admin/lawful-interception/user-payments-pdf/" . Hashids::encode($row->id)) . '"><i class="fa fa-credit-card-alt"></i></a>';
                        }

                        if (have_right(136)) {
                              $actions .= '&nbsp;<a title="User Subscriptions PDF" class="btn btn-primary" target="_blank" href="' . url("admin/lawful-interception/user-subscriptions-pdf/" . Hashids::encode($row->id)) . '"><i class="fa fa-list"></i></a>';
                        }

                        if (have_right(137)) {
                              $actions .= '&nbsp;<a title="User SMS Campaigns PDF" class="btn btn-primary" target="_blank" href="' . url("admin/lawful-interception/user-sms-pdf/" . Hashids::encode($row->id)) . '"><i class="fa fa-comments"></i></a>';
                        }

                        if (have_right(138)) {
                              $actions .= '&nbsp;<a title="User Email Campaigns PDF" class="btn btn-primary" target="_blank" href="' . url("admin/lawful-interception/user-email-pdf/" . Hashids::encode($row->id)) . '"><i class="fa fa-envelope"></i></a>';
                        }

                        if (have_right(139)) {
                              $actions .= '&nbsp;<a title="User Split Campaigns PDF" class="btn btn-primary" target="_blank" href="' . url("admin/lawful-interception/user-split-pdf/" . Hashids::encode($row->id)) . '"><i class="fa fa-envelope"></i></a>';
                        }

                        if (have_right(140)) {
                              $actions .= '&nbsp;<a title="Download All Data" class="btn btn-primary lawful-download-user-data" data-id="' . Hashids::encode($row->id) . '" data-action="user-all-data" href="#"><i class="fa fa-download"></i></a>';
                        }

                        $actions .= '</span>';
                        return $actions;
                  });

                  $datatable = $datatable->rawColumns(['status', 'action']);
                  $datatable = $datatable->make(true);
                  return $datatable;
            }

            return view('admin.lawful-interception.index')->with($data);
      }

      public function userDetailsPdf($id)
      {
            if (!have_right(134))
                  access_denied();

            $id = Hashids::decode($id)[0];
            $user = User::find($id);
            $data['user'] = $user;

            $pdf = PDF::loadView('admin.lawful-interception.user_details_pdf', $data);
            return $pdf->stream($user->email . '-details.pdf');
            // return $pdf->download($user->email.'-user-details.pdf');
      }

      public function checkUserTempFile($id)
      {
            $id = Hashids::decode($id)[0];
            $user = User::find($id);
            $userfilename = $user->temp_zip_file;
            if (isset($userfilename)) {
                  return response()->json([
                        'status' => 1,
                        'message' => 'File name is saved.'
                  ], 200, ['Content-Type' => 'application/json']);
            } else {
                  return response()->json([
                        'status' => 2,
                        'message' => 'File name not saved yet.'
                  ], 200, ['Content-Type' => 'application/json']);
            }
      }

      public function userPaymentsPdf($id)
      {
            if (!have_right(135))
                  access_denied();

            $id = Hashids::decode($id)[0];
            $user = User::find($id);
            $data['payments'] = Payment::where('user_id', $id)->where('status', 1)->orderBy('created_at', 'DESC')->get();

            if (count($data['payments']) > 0) {
                  $pdf = PDF::loadView('admin.lawful-interception.user_payments_pdf', $data);
                  return $pdf->stream($user->email . '-payments.pdf');
            } else {
                  Session::flash('flash_danger', 'No record found for this user.');
                  return redirect()->back();
            }
      }

      public function userSMSCampaigns($id)
      {
            if (!have_right(137))
                  access_denied();

            $id = Hashids::decode($id)[0];
            $user = User::find($id);
            $data['smscampaigns'] = SmsCampaign::where('user_id', $id)->where('status', '!=', 1)->orderBy('created_at', 'DESC')->get();

            if (count($data['smscampaigns']) > 0) {
                  $pdf = PDF::loadView('admin.lawful-interception.user_sms_pdf', $data);
                  return $pdf->stream($user->email . '-sms.pdf');
            } else {
                  Session::flash('flash_danger', 'No record found for this user.');
                  return redirect()->back();
            }
      }

      public function userEmailCampaigns($id)
      {
            if (!have_right(138))
                  access_denied();

            $id = Hashids::decode($id)[0];
            $user = User::find($id);
            $data['emailcampaigns'] = EmailCampaign::where('user_id', $id)->where('is_split_testing', 0)->where('status', '!=', 2)->orderBy('created_at', 'DESC')->get();

            if (count($data['emailcampaigns']) > 0) {
                  $pdf = PDF::loadView('admin.lawful-interception.user_email_pdf', $data);
                  return $pdf->stream($user->email . '-email.pdf');
            } else {
                  Session::flash('flash_danger', 'No record found for this user.');
                  return redirect()->back();
            }
      }

      public function userSplitCampaigns($id)
      {
            if (!have_right(139))
                  access_denied();

            $id = Hashids::decode($id)[0];
            $user = User::find($id);
            $data['splitcampaigns'] = EmailCampaign::where('user_id', $id)->where('is_split_testing', 1)->where('status', '!=', 2)->orderBy('created_at', 'DESC')->get();

            if (count($data['splitcampaigns']) > 0) {
                  $pdf = PDF::loadView('admin.lawful-interception.user_split_pdf', $data);
                  return $pdf->stream($user->email . '-split.pdf');
            } else {
                  Session::flash('flash_danger', 'No record found for this user.');
                  return redirect()->back();
            }
      }

      public function userSubscriptionsPdf($id)
      {
            if (!have_right(136))
                  access_denied();

            $id = Hashids::decode($id)[0];
            $user = User::find($id);
            $data['subscriptions'] = PackageSubscription::where('user_id', $id)->orderBy('created_at', 'DESC')->get();

            if (count($data['subscriptions']) > 0) {
                  $pdf = PDF::loadView('admin.lawful-interception.user_subscriptions_pdf', $data);
                  return $pdf->stream($user->email . '-subscriptions.pdf');
            } else {
                  Session::flash('flash_danger', 'No record found for this user.');
                  return redirect()->back();
            }
      }

      public function archiveUserData($id, Request $request)
      {
            $id = Hashids::decode($id)[0];
            $user = User::find($id);

            $userfilename = $user->temp_zip_file;

            // Check if request is for archiving user's files or user's all data
            if ($request->action == "user-all-data") {
                  # delete directory
                  if (File::exists(public_path() . '/storage/temp/user-data-' . $user->email)) {
                        File::deleteDirectory(public_path() . '/storage/temp/user-data-' . $user->email);
                  }
                  ArchiveUserAllDataJob::dispatch($id);
                  return response()->json([
                        'status' => 1,
                        'action' => 'user-all-data',
                        'message' => 'Job processing.'
                  ], 200, ['Content-Type' => 'application/json']);
            }
      }

      public function downloadAllData($id)
      {
            if (!have_right(140))
                  access_denied();

            $id = Hashids::decode($id)[0];
            $user = User::find($id);
            $userfilename = $user->temp_zip_file;

            if (!empty($userfilename)) {
                  $tmp_file = public_path() . '/storage/temp/' . $userfilename;

                  # delete directory
                  if (File::exists(public_path() . '/storage/temp/user-data-' . $user->email)) {
                        File::deleteDirectory(public_path() . '/storage/temp/user-data-' . $user->email);
                  }

                  $filename = $user->email . '-data-' . date('Y-m-d') . '.zip';

                  if (file_exists($tmp_file)) {
                        # send the file to the browser as a download
                        header('Content-disposition: attachment; filename="' . $filename . '"');
                        header('Content-type: application/zip');
                        readfile($tmp_file);
                        unlink($tmp_file);
                        $user->update([
                              'temp_zip_file' => null
                        ]);
                  } else {
                        Session::flash('flash_danger', 'No record found for this user.');
                        return redirect()->back();
                  }
            } else {
                  Session::flash('flash_danger', 'No record found for this user.');
                  return redirect()->back();
            }
      }
}
