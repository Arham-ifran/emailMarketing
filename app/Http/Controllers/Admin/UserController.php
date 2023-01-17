<?php

namespace App\Http\Controllers\Admin;

use App\CustomClasses\PaymentHandler;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\Admin\Country;
use App\Models\Admin\EmailTemplate;
use App\Models\EmailCampaign;
use App\Models\SmsCampaign;
use App\Jobs\SendMail;
use App\Models\Admin\Notification;
use App\Models\Admin\Package;
use App\Models\Admin\PackageSubscription;
use App\Models\Admin\Payment;
use App\Models\Admin\PaymentGatewaySetting;
use App\Models\Admin\Timezone;
use App\Models\CampaignContact;
use App\Models\CampaignExclude;
use App\Models\CampaignHistory;
use App\Models\Contact;
use App\Models\Contact_group;
use App\Models\EmailCampaignClick;
use App\Models\EmailCampaignLogs;
use App\Models\EmailCampaignOpen;
use App\Models\EmailCampaignTemplate;
use App\Models\EmailSendingLog;
use App\Models\Group;
use App\Models\PayAsYouGoPayments;
use App\Models\SmsCampaignLogs;
use App\Models\SmsTemplate;
use App\Models\SplitTestSubject;
use App\Models\User_log;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Auth;
use Hashids;
use File;
use Storage;
use Session;
use Hash;
use DB;
use DataTables;
use Config;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        if (!have_right(11))
            access_denied();

        if ($request->ajax()) {
            $db_record = new User();

            if ($request->has('status') && $request->status != "") {
                $db_record = $db_record->where('status', $request->status);
            }

            $db_record =  $db_record->orderBy('created_at', 'DESC');
            $datatable = Datatables::of($db_record);
            $datatable = $datatable->addIndexColumn();
            $datatable = $datatable->addColumn('country', function ($row) {
                $country = Country::where('id', $row->country_id)->first();
                if ($country)
                    return $country->name;
                else
                    return "";
            });
            $datatable = $datatable->addColumn('email_campaigns', function ($row) {
                $email_campaigns = EmailCampaign::where('user_id', $row->id)->where('is_split_testing', 0)->get()->count();
                if ($email_campaigns)
                    return "<span class='actions'> <a class='btn btn-primary' href='" . url("admin/email-campaigns?user=" . Hashids::encode($row->id)) . "' title='View Email Campaigns'>" . $email_campaigns . "</a> </span>";
                else
                    return $email_campaigns;
            });
            $datatable = $datatable->addColumn('sms_campaigns', function ($row) {
                $sms_campaigns = SmsCampaign::where('user_id', $row->id)->get()->count();
                if ($sms_campaigns)
                    return "<span class='actions'> <a class='btn btn-primary' href='" . url("admin/sms-campaigns?user=" . Hashids::encode($row->id)) . "' title='View SMS Campaigns'>" . $sms_campaigns . "</a> </span>";
                else
                    return $sms_campaigns;
            });
            $datatable = $datatable->addColumn('split_campaigns', function ($row) {
                $split_campaigns = EmailCampaign::where('user_id', $row->id)->where('is_split_testing', 1)->get()->count();
                if ($split_campaigns)
                    return '<span class="actions"> <a class="btn btn-primary" href="' . url("admin/split-campaigns?user=" . Hashids::encode($row->id)) . '" title="View Split Campaigns">' . $split_campaigns . '</a> </span>';
                else
                    return $split_campaigns;
            });
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

                if (have_right(131)) {
                    $actions .= '&nbsp;<a class="btn btn-primary" href="' . url("admin/package-payments?user=" . Hashids::encode($row->id)) . '" title="View Package Payments"><i class="fa fa-credit-card-alt"></i></a>';
                }

                // if (have_right(132)) {
                //     $actions .= '&nbsp;<a class="btn btn-primary" href="' . url("admin/pay-as-you-go-package-payments?user=" . Hashids::encode($row->id)) . '" title="View PayAsYouGo Package Payments"><i class="fa fa-credit-card-alt"></i></a>';
                // }

                if (have_right(132)) {
                    $actions .= '&nbsp;<a class="btn btn-primary" href="' . url("admin/packages/subscriptions/all?user=" . Hashids::encode($row->id)) . '" title="View Subscriptions"><i class="fa fa-tasks"></i></a>';
                }

                if (have_right(15)) {
                    $actions .= '&nbsp;<a class="btn btn-primary" href="' . url("admin/users/packages/" . Hashids::encode($row->id)) . '" title="Upgrade/Downgrade Package"><i class="fa fa-sliders"></i></a>';
                }

                if (have_right(33)) {
                    $actions .= '&nbsp;<a class="btn btn-primary" href="' . url("admin/sms-campaigns?user=" . Hashids::encode($row->id)) . '" title="View SMS Campaigns"><i class="fa fa-comments"></i></a>';
                }

                if (have_right(35)) {
                    $actions .= '&nbsp;<a class="btn btn-primary" href="' . url("admin/email-campaigns?user=" . Hashids::encode($row->id)) . '" title="View Email Campaigns"><i class="fa fa-envelope"></i></a>';
                }

                if (have_right(97)) {
                    $actions .= '&nbsp;<a class="btn btn-primary" href="' . url("admin/split-campaigns?user=" . Hashids::encode($row->id)) . '" title="View Split Campaigns"><i class="fa fa-envelope"></i></a>';
                }

                if (have_right(13)) {
                    $actions .= '&nbsp;<a class="btn btn-primary" href="' . url("admin/users/" . Hashids::encode($row->id) . '/edit') . '" title="Edit"><i class="fa fa-pencil-square-o"></i></a>';
                }

                if (have_right(14)) {
                    $actions .= '&nbsp;<form id="delete_' . Hashids::encode($row->id) . '" method="POST" action="' . url("admin/users/" . Hashids::encode($row->id)) . '" accept-charset="UTF-8" style="display:inline">';
                    $actions .= '<input type="hidden" name="_method" value="DELETE">';
                    $actions .= '<input name="_token" type="hidden" value="' . csrf_token() . '">';
                    $actions .= '<button class="btn btn-danger" type="button" onclick=openDeletePopup("delete_' . Hashids::encode($row->id) . '") title="Delete">';
                    $actions .= '<i class="fa fa-trash"></i>';
                    $actions .= '</button>';
                    $actions .= '</form>';
                }

                $actions .= '</span>';
                return $actions;
            });

            $datatable = $datatable->rawColumns(['status', 'action', 'sms_campaigns', 'email_campaigns', 'split_campaigns']);
            $datatable = $datatable->make(true);
            return $datatable;
        }

        $data = [];
        $package_id = '';

        $data['package_id'] = $package_id;
        $data['packages'] = [];

        return view('admin.users.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!have_right(12))
            access_denied();

        $data['user'] = new User();
        $data['languages'] = []; //Language::where('status',1)->whereNull('deleted_at')->get();
        $data['timezones'] = Timezone::all();
        $data['countries'] = Country::get();
        $data['action'] = "Add";
        return view('admin.users.form')->with($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!have_right(13))
            access_denied();

        $id = Hashids::decode($id)[0];
        $data['action'] = "Edit";
        $data['user'] = User::findOrFail($id);
        $data['languages'] = []; //Language::where('status',1)->whereNull('deleted_at')->get();
        $data['timezones'] = Timezone::all();
        $data['countries'] = Country::get();
        return view('admin.users.form')->with($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();

        if ($input['action'] == 'Add') {
            $validator = Validator::make($request->all(), [
                'email' => ['required', 'string', 'max:100', Rule::unique('users')],
                'name' => ['required', 'string', 'max:100'],
                'password' => 'required|string|min:8|max:30',
            ]);

            if ($validator->fails()) {
                Session::flash('flash_danger', $validator->messages());
                return redirect()->back()->withInput();
            }

            $input['original_password'] = $input['password'];
            $input['password'] = Hash::make($input['password']);
            $input['language'] = "en";

            $model = new User();
            $flash_message = 'User has been created successfully.';
        } else {
            $validator = Validator::make($request->all(), [
                'email' => ['required', 'string', Rule::unique('users')->ignore($input['id'])],
                'name' => ['required', 'string', 'max:100'],
                'password' => 'required|string|min:8|max:30',
            ]);

            if ($validator->fails()) {
                Session::flash('flash_danger', $validator->messages());
                return redirect()->back()->withInput();
            }

            if (!empty($input['password'])) {
                $input['original_password'] = $input['password'];
                $input['password'] = Hash::make($input['password']);
            } else {
                unset($input['password']);
            }

            // $input['country_id'] = $input['country'];

            $model = User::findOrFail($input['id']);
            $flash_message = 'User has been updated successfully.';
        }

        $model->fill($input);
        $model->disabled_at = ($input['status'] == "0") ? date("Y-m-d H:i:s") : Null;
        $model->deleted_at = ($input['status'] == "3") ? date("Y-m-d H:i:s") : Null;
        $model->save();

        if ($input['action'] == 'Add') {

            // ****************************************************//
            // Send Email About User Creation  //
            // *************************************************** //

            $email_template = EmailTemplate::where('type', 'user_created_by_admin')->first();
            $email_template = transformEmailTemplateModel($email_template, $model->language);
            $name = $model->name;
            $email = $model->email;
            $password = $model->original_password;
            $link = url('/verify-account/' . Hashids::encode($model->id));
            $subject = $email_template['subject'];
            $content = $email_template['content'];

            $search = array("{{name}}", "{{password}}", "{{link}}", "{{app_name}}");
            $replace = array($name, $password, $link, settingValue('site_title'));
            $content = str_replace($search, $replace, $content);

            SendMail::dispatch($email, $subject, $content);

            // =======================
            // Personal Package (FREE)
            // ========================
            $package = Package::find(2); // Free Package
            $end_date = Null;
            $on_trial = 0;
            $type = 1;

            $packageLinkedFeatures = $package->linkedFeatures->pluck('count', 'feature_id')->toArray();

            $features = $package->linkedFeatures->pluck('feature_id')->toArray();
            $counts = $package->linkedFeatures->pluck('count')->toArray();

            $totalemails = 0;
            $totalsms = 0;
            if (count($features)) {
                $index = array_search(1, $features); //total_emails
                if ($index >= 0)
                    $totalemails = $counts[$index];

                $index = array_search(2, $features); //total_sms
                if ($index >= 0)
                    $totalsms = $counts[$index];

                $index = array_search(3, $features); //total_contacts
                if ($index >= 0)
                    $total_contacts = $counts[$index];
            }

            $packageSubscription = PackageSubscription::create([
                'user_id'       =>  $model->id,
                'package_id'    =>  $package->id,
                'price'         =>  0,
                'features'      =>  empty($package->linkedFeatures) ? '' : json_encode($packageLinkedFeatures),
                'description'   =>  $package->description,
                'type'          =>  $type,
                'start_date'    =>  Carbon::now('UTC')->timestamp,
                'end_date'      =>  $end_date,
                'payment_option' =>  1,
                'is_active'     =>  1,
                'contact_limit' => $total_contacts,
                'email_limit' =>  $totalemails,
                'email_used' => 0,
                'sms_limit' => $totalsms,
                'sms_used' => 0
            ]);

            do {
                $api_token = Str::random(132);
                $secret_key = Str::random(20);
            } while (User::where("api_token", $api_token)->orWhere("secret_key", $secret_key)->first() instanceof User);

            $model->update([
                'package_id' => $package->id,
                'package_subscription_id' => $packageSubscription->id,
                'on_trial' => $on_trial,
                'package_recurring_flag' => 0,
                'secret_key' => $secret_key,
                'api_token' => $api_token,
                'timezone' => 'UTC'
            ]);
            // =======================
            // Personal Package (FREE)  DONE
            // ========================
        }

        $request->session()->flash('flash_success', $flash_message);
        return redirect('admin/users');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        if (!have_right(14))
            access_denied();

        $id = Hashids::decode($id)[0];

        // tables having user data:
        CampaignContact::where('user_id', $id)->delete();
        CampaignExclude::where('user_id', $id)->delete();
        CampaignHistory::where('user_id', $id)->delete();
        Contact_group::where('user_id', $id)->delete();
        Contact::where('user_id', $id)->delete();
        EmailCampaign::where('user_id', $id)->delete();
        // EmailCampaignClick::where('user_id', $id)->delete();
        // EmailCampaignOpen::where('user_id', $id)->delete();
        EmailCampaignLogs::where('user_id', $id)->delete();
        EmailCampaignTemplate::where('user_id', $id)->delete();
        EmailSendingLog::where('user_id', $id)->delete();
        Group::where('user_id', $id)->delete();
        Notification::where('user_id', $id)->delete();
        PackageSubscription::where('user_id', $id)->delete();
        Payment::where('user_id', $id)->delete();
        PayAsYouGoPayments::where('user_id', $id)->delete();
        SmsCampaign::where('user_id', $id)->delete();
        SmsCampaignLogs::where('user_id', $id)->delete();
        SmsTemplate::where('user_id', $id)->delete();
        SplitTestSubject::where('user_id', $id)->delete();
        User_log::where('user_id', $id)->delete();
        User::destroy($id);
        Session::flash('flash_success', 'User has been deleted successfully.');

        if ($request->has('page') && $request->page == 'dashboard') {
            return redirect('admin/dashboard');
        } else {
            return redirect('admin/users');
        }
    }

    public function payments(Request $request, $id)
    {
        if (!have_right(18))
            access_denied();

        $data = [];
        $data['id'] = $id;
        $id = Hashids::decode($id)[0];
        $data['user'] = User::find($id);

        if ($request->ajax()) {
            $db_record = MigrationPayment::where('user_id', $id)->whereNotNull('created_at')->orderBy('created_at', 'DESC'); //->get();

            if ($request->has('search') && !empty($request->search)) {
                $db_record = $db_record->where('net_price', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('vat_amount', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('extra_data_price', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('scan_price', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('total_amount', 'LIKE', '%' . $request->search . '%');
            } else {

                $db_record =  $db_record->whereNotNull('id');
            }

            $datatable = Datatables::of($db_record);
            $datatable = $datatable->addIndexColumn();

            // $datatable = $datatable->editColumn('item', function($row)
            // {
            //     $payload = json_decode($row->payload,true);
            //     // return $payload["subscription_desc"];
            //     return $row->item;
            // });

            $datatable = $datatable->addColumn('package_title', function ($row) {
                return 'Pay-as-you-Go';
            });

            $datatable = $datatable->editColumn('net_price', function ($row) {
                return '<sup>' . config('constants.currency')['symbol'] . '</sup>' . $row->net_price;
            });

            $datatable = $datatable->editColumn('vat_amount', function ($row) {
                return '<sup>' . config('constants.currency')['symbol'] . '</sup>' . $row->vat_amount;
            });

            $datatable = $datatable->editColumn('extra_data_price', function ($row) {
                return '<sup>' . config('constants.currency')['symbol'] . '</sup>' . $row->extra_data_price;
            });

            $datatable = $datatable->editColumn('scan_price', function ($row) {
                return '<sup>' . config('constants.currency')['symbol'] . '</sup>' . $row->scan_price;
            });

            $datatable = $datatable->editColumn('total_amount', function ($row) {
                return '<sup>' . config('constants.currency')['symbol'] . '</sup>' . $row->total_amount;
            });

            $datatable = $datatable->editColumn('created_at', function ($row) {
                return Carbon::createFromTimeStamp(strtotime($row->created_at), "UTC")->tz(Timezone::where('name', $row->user->timezone)->first()->utc_offset)->format('d M, Y');
            });

            $datatable = $datatable->editColumn('payment_method', function ($row) {
                if ($row->status == 1) {
                    $payment_method = '<span class="label label-primary">Mollie</span>';
                } else {
                    $payment_method = '<span class="label label-primary">N/A</span>';
                }

                return $payment_method;
            });

            $datatable = $datatable->addColumn('vouchers', function ($row) {
                return !empty($row->voucher_ids) ? implode(', ', \App\Models\Voucher::whereIn('id', explode(',', $row->voucher_ids))->pluck('voucher')->toArray()) : 'N/A';
            });

            $datatable = $datatable->addColumn('payment_date', function ($row) {
                if ($row->status == 1) {
                    return !empty($row->timestamp) ? Carbon::createFromTimeStamp($row->timestamp, "UTC")->tz(Timezone::where('name', $row->user->timezone)->first()->utc_offset)->format('d M, Y') : 'N/A';
                } else {
                    return 'N/A';
                }
            });

            $datatable = $datatable->addColumn('transaction_id', function ($row) {
                if ($row->status == 1) {
                    return !empty($row->txn_id) ? $row->txn_id : 'N/A';
                } else {
                    return 'N/A';
                }
            });

            $datatable = $datatable->editColumn('status', function ($row) {
                $status = 'Pending';

                switch ($row->status) {
                    case 1:
                        $status = '<span className="badge badge-success">Paid</span>';
                        break;
                    case 2:
                        $status = '<span className="badge badge-primary">Open</span>';
                        break;
                    case 3:
                    case 5:
                        $status = '<span className="badge badge-secondary">Pending</span>';
                        break;
                    case 4:
                        $status = '<span className="badge badge-danger">Failed</span>';
                        break;
                        // case 5:
                        //     $status = '<span className="badge badge-warning">{t('expired')}</span>';
                        //     break;
                    case 6:
                        $status = '<span className="badge badge-info">Cancelled</span>';
                        break;
                    case 7:
                        $status = '<span className="badge badge-light">Refunds</span>';
                        break;
                    case 8:
                        $status = '<span className="badge badge-dark">Chargebacks</span>';
                        break;
                }
                return $status;
            });

            $datatable = $datatable->addColumn('action', function ($row) {
                $actions = '<span class="actions">';

                if (have_right(104)) {
                    $actions .= '&nbsp;<a title="Download Invoice" class="btn btn-primary" href="' . url("/api/migrations/download-payment-invoice/" . Hashids::encode($row->id)) . '"><i class="fa fa-download"></i></a>';
                }

                $actions .= '</span>';
                return $actions;
            });

            $datatable = $datatable->rawColumns(['net_price', 'vat_amount', 'extra_data_price', 'scan_price', 'total_amount', 'payment_method', 'status', 'action']);
            $datatable = $datatable->make(true);
            return $datatable;
        }

        return view('admin.users.payments', $data);
    }

    public function sendPassword($id)
    {
        $user = User::find(Hashids::decode($id)[0]);
        $name = $user->name;
        $email = $user->email;

        $email_template = EmailTemplate::where('type', 'send_password')->first();
        $email_template = transformEmailTemplateModel($email_template, $user->language);
        $subject = $email_template['subject'];
        $content = $email_template['content'];

        $search = array("{{name}}", "{{password}}", "{{app_name}}");
        $replace = array($name, $user->original_password, settingValue('site_title'));
        $content  = str_replace($search, $replace, $content);

        sendMail::dispatch($email, $subject, $content);

        Session::flash('flash_success', 'Password has been sent successfully.');
        return redirect('admin/users/' . $id . '/edit');
    }

    /**
     * Show the user subscription.
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function subscriptions(Request $request, $id)
    {
        if (!have_right(20))
            access_denied();

        $data = [];
        $data['id'] = $id;
        $id = Hashids::decode($id)[0];
        $data['user'] = User::find($id);

        if ($request->ajax()) {
            $db_record = PackageSubscription::with('package');

            if ($request->has('search') && !empty($request->search)) {
                $db_record = $db_record->whereHas('package', function ($q) use ($request) {
                    $q->where('title', 'LIKE', '%' . $request->search . '%');
                })->where('user_id', $id);
            } else {
                $db_record =  $db_record->whereNotNull('id');
            }

            $db_record = $db_record->where('user_id', $id)->orderBy('created_at', 'DESC');

            $datatable = Datatables::of($db_record);
            $datatable = $datatable->addIndexColumn();
            $datatable = $datatable->addColumn('package_title', function ($row) {
                return $row->package_title;
            });
            $datatable = $datatable->editColumn('price', function ($row) {
                return '<sup>' . config('constants.currency')['symbol'] . '</sup>' . $row->price;
            });
            $datatable = $datatable->editColumn('type', function ($row) {
                $type = '';

                if ($row->package_id == 1)
                    $type = '<span class="label label-primary">Trial</span>';
                else if ($row->package_id == 2)
                    $type = '<span class="label label-primary">Free</span>';
                else {
                    $type = '<span class="label label-success">Paid</span>';
                }

                return $type;
            });
            $datatable = $datatable->editColumn('start_date', function ($row) {
                return Carbon::createFromTimeStamp($row->start_date, "UTC")->tz(session('timezone'))->format('d M, Y');
            });
            $datatable = $datatable->editColumn('end_date', function ($row) {
                return (!empty($row->end_date)) ? Carbon::createFromTimeStamp($row->end_date, "UTC")->tz(session('timezone'))->format('d M, Y') : 'Lifetime';
            });
            $datatable = $datatable->addColumn('status', function ($row) {
                $currentTimestamp = Carbon::now('UTC')->timestamp;
                $status = '';

                if ($row->id == $row->user->package_subscription_id) {
                    if (empty($row->end_date) || $row->end_date > $currentTimestamp)
                        $status = '<span class="label label-success">Active</span>';
                    else
                        $status = '<span class="label label-warning">Expired</span>';
                } else
                    $status = '<span class="label label-danger">In-Active</span>';

                return $status;
            });

            $datatable = $datatable->rawColumns(['type', 'price', 'status']);
            $datatable = $datatable->make(true);
            return $datatable;
        }

        return view('admin.users.subscriptions', $data);
    }

    public function packages(Request $request, $id)
    {
        if (!have_right(15))
            access_denied();

        $user = User::find(Hashids::decode($id)[0]);
        $data['user'] = $user;

        $data['pending_payments'] = false;

        // if pay as you go package
        if ($user->package_id == 9) {
            $payments = PayAsYouGoPayments::where('user_id', $user->id)->where('status', '!=', 1)->count();
            if ($payments) {
                $data['pending_payments'] = true;
            } else {
                // checking non generated pending data payments
                if ($user->subscription) {
                    $emails_to_pay_for = $user->subscription->emails_to_pay;
                    $sms_to_pay_for = $user->subscription->sms_to_pay;
                    // $contacts_to_pay_for = $user->subscription->contacts_to_pay;

                    // $total_price = $emails_to_pay_for + $sms_to_pay_for + $contacts_to_pay_for;
                    $total_price = $emails_to_pay_for + $sms_to_pay_for;
                    $vat_percentage = settingValue('vat');

                    if (!empty($user->country_id) && $user->country->apply_default_vat == 0 && $user->country->status == 1) {
                        $vat_percentage =  $user->country->vat;
                    }
                    $total_amount_charged = $total_price + ($total_price * ($vat_percentage / 100));

                    if ($total_amount_charged > 0) {
                        $data['pending_payments'] = true;
                    }
                }
            }
        }

        // $data['packages'] = Package::whereNotIn('id', [1])->where('status', 1)->orderBy('monthly_price')->get();
        $data['packages'] = Package::whereNotIn('id', [1])->where('status', 1)->orderBy('created_at', 'ASC')->get();

        $subscription = $user->subscription;
        $currentTimestamp = Carbon::now('UTC')->timestamp;

        if (!empty($subscription->end_date) && $subscription->end_date < $currentTimestamp) {
            //************************//
            // Subscribe Free Package //
            //************************//

            $user->update([
                'on_hold_package_id' =>  $subscription->package_id,
                'prev_package_subscription_id' => $subscription->id,
                'package_updated_by_admin' => 0,
                'unpaid_package_email_by_admin' => 0,
                'is_expired' => 0,
                'expired_package_disclaimer' => 1
            ]);

            $package = Package::find(2);
            $package_activated = activatePackage($user->id, $package);

            if ($package_activated == 1) {
                // ****************************************************//
                // Send Email About Package downraded to free package  //
                // *************************************************** //

                $email_template = EmailTemplate::where('type', 'package_downgrade_after_subscription_expired')->first();
                $email_template = transformEmailTemplateModel($email_template, $user->language);
                $name = $user->name;
                $email = $user->email;
                $upgrade_link = url('/upgrade-package?redirect_to_upgrade_package=1');
                $contact_link = url('/contact-us');
                $subject = $email_template['subject'];
                $content = $email_template['content'];

                $search = array("{{name}}", "{{from}}", "{{to}}", "{{upgrade_link}}", "{{contact_link}}", "{{app_name}}");
                $replace = array($name, $subscription->package_title, $package->title, $upgrade_link, $contact_link, settingValue('site_title'));
                $content  = str_replace($search, $replace, $content);

                SendMail::dispatch($email, $subject, $content);
            }
            $user = User::find(Hashids::decode($id)[0]);
            $data['user'] = $user;
        }

        return view('admin.users.packages', $data);
    }

    public function updatePackage(Request $request)
    {
        $user = User::find($request->user_id);
        $name = $user->name;
        $email = $user->email;

        if ($user->package_id == 9) {
            $this->clearPayments($user);
        }

        if ($request->payment_option == 1) {
            $request['lang'] = $user->language;
            PaymentHandler::checkout($request, $request->package_id, $request->type, $request->user_id, 'admin', '', '', 'Admin');
            Session::flash('flash_success', 'Package has been updated successfully.');
        } else {
            $payment_link = url('/packages/payment-checkout?package_id=' . $request->package_id . '&type=' . $request->type . '&repetition=' . $request->repetition);
            $email_template = EmailTemplate::where('type', 'unpaid_package_upgrade_downgrade_by_admin')->first();
            $email_template = transformEmailTemplateModel($email_template, $user->language);
            $subject = $email_template['subject'];
            $content = $email_template['content'];

            $search = array("{{name}}", "{{link}}", "{{app_name}}");
            $replace = array($name, $payment_link, settingValue('site_title'));
            $content  = str_replace($search, $replace, $content);

            sendMail::dispatch($email, $subject, $content);
            Session::flash('flash_success', 'Package change request has been initiated successfully.');
            $user->update([
                'package_updated_by_admin'  => 0,
                'unpaid_package_email_by_admin' => 1,
                'expired_package_disclaimer' => 0
            ]);
        }

        return redirect()->back();
    }

    private function clearPayments($user)
    {
        if ($user->subscription) {
            $used_emails = $user->subscription->emails_paying_for;
            $used_sms = $user->subscription->sms_paying_for;
            $used_contacts = $user->subscription->contacts_paying_for;
            $emails_to_pay_for = $user->subscription->emails_to_pay;
            $sms_to_pay_for = $user->subscription->sms_to_pay;
            // $contacts_to_pay_for = $user->subscription->contacts_to_pay;
            // $total_price = $emails_to_pay_for + $sms_to_pay_for + $contacts_to_pay_for;
            $total_price = $emails_to_pay_for + $sms_to_pay_for;
            $package_title = $user->package->title;
            $vat_country_code = 'def';
            $vat_percentage = settingValue('vat');
            $voucher = '';

            if (!empty($user->country_id) && $user->country->apply_default_vat == 0 && $user->country->status == 1) {
                $vat_percentage =  $user->country->vat;
                $vat_country_code = $user->country->code;
            }
            $total_amount_charged = $total_price + ($total_price * ($vat_percentage / 100));

            if ($total_amount_charged > 0) {
                // create payload
                // ==============
                $data = [];
                $description = $user->package->title . ' Package Payment';
                $data['items'] = [
                    [
                        'name'  => $user->package->title . ' Package',
                        'desc'  => $description,
                        'price' => $total_price,
                        'qty'   => 1,
                    ],
                ];
                $data['title'] = 'Pay as you go Payment';
                $data['subscription_desc'] = $data['invoice_description'] = $description;
                $data['total'] = $total_price + $vat_percentage;
                // created payload
                // ==============
                $paymentGatewaySettings = PaymentGatewaySetting::first();
                if ($paymentGatewaySettings->mollie_mode == 'sandbox') {
                    $mollie_api_key = $paymentGatewaySettings->mollie_sandbox_api_key;
                } else if ($paymentGatewaySettings->mollie_mode == 'live') {
                    $mollie_api_key = $paymentGatewaySettings->mollie_live_api_key;
                }

                $mollie = new \Mollie\Api\MollieApiClient();
                $mollie->setApiKey($mollie_api_key);
                $customer = null;
                $customerExist = false;

                // /* Check Customer Existance */
                if (!empty($user->mollie_customer_id)) {
                    try {
                        $customer = $mollie->customers->get($user->mollie_customer_id);
                        $customerExist = true;
                    } catch (\Mollie\Api\Exceptions\ApiException $e) {
                        $customerExist = false;
                    }
                }

                if (!$customerExist) {
                    // /** Create a new customer */
                    $customer = $mollie->customers->create([
                        'name'  => $user->name,
                        'email' => $user->email,
                    ]);
                }

                $payment = PayAsYouGoPayments::create([
                    'user_id'                   =>  $user->id,
                    'package_subscription_id'   =>  $user->subscription->id,
                    'item'                      =>  $package_title,
                    'payment_method'            =>  'mollie',
                    'amount'                    =>  $total_price,
                    'vat_percentage'            =>  $vat_percentage,
                    'vat_amount'                =>  $total_price * ($vat_percentage / 100),
                    'vat_country_code'          =>  strtolower($vat_country_code),
                    'voucher'                   =>  $voucher,
                    'discount_percentage'       =>  0,
                    'discount_amount'           =>  0,
                    'total_amount_charged'      =>  $total_amount_charged,
                    'payload'                   =>  json_encode($data),
                    'payment_mode'              =>  $paymentGatewaySettings->mollie_mode == 'sandbox' ? 2 : 1,
                    'lang'                      =>  $user->language != 'en' ? $user->language : 'en',
                    'charging_for_emails' => $used_emails,
                    'charging_for_sms' => $used_sms,
                    'charging_for_contacts' => $used_contacts,
                    'price_for_emails_charged' => $emails_to_pay_for,
                    'price_for_sms_charged' => $sms_to_pay_for,
                    // 'price_for_contacts_charged' => $contacts_to_pay_for
                    'price_for_contacts_charged' => 0
                ]);

                $total_amount_charged = number_format((float)$total_amount_charged, 2, '.', '');

                // /**Initiate payment*/
                $payRequest = [
                    "amount" => [
                        "currency" =>  strtoupper(Config::get('constants.currency')['code']),
                        "value" => $total_amount_charged // You must send the correct number of decimals, thus we enforce the use of strings
                    ],
                    'customerId'   => $customer->id,
                    'sequenceType' => 'first',
                    "description" => $data['subscription_desc'],
                    "redirectUrl" => url("/packages/mollie-confirmation?order_id=" . Hashids::encode($user->subscription->id)),
                    "webhookUrl"  => url("/api/mollie/pay-as-you-go-callback"),
                    "metadata" => [
                        "order_id" => $user->subscription->id,
                        "language" => 'en'
                    ],
                ];
                $response = $mollie->payments->create($payRequest);
                $redirectUrl = $response->getCheckoutUrl();
                $response = (array)$response;

                $user->update([
                    'mollie_customer_id' => $customer->id,
                ]);

                $payment->update([
                    'link' => $redirectUrl,
                    'data'      =>  json_encode($response),
                    'timestamp' =>  Carbon::now('UTC')->timestamp,
                    'txn_id'    => $response['id'],
                    'status'    => 2
                ]);

                $user->subscription->update([
                    'emails_paying_for' => 0,
                    'emails_to_pay' => 0,
                    'sms_paying_for' => 0,
                    'sms_to_pay' => 0,
                    'contacts_paying_for' => 0,
                    'contacts_to_pay' => 0,
                ]);
            }
        }

        $payments = PayAsYouGoPayments::where('user_id', $user->id)->where('status', '!=', 1)->get();
        // if (count($payments)) {
        foreach ($payments as $payment) {
            $payment->update([
                'status' => 1,
                'discount_percentage' => 100,
                'discount_amount' => $payment->total_amount_charged,
                'total_amount_charged' => 0,
                'payment_method' => 'admin'
            ]);
        }
        // }
    }
}
