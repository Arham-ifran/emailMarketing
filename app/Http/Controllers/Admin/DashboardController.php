<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use DB;
use App\Models\Admin\Notification;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        $data['roles'] = DB::table('roles')->count();
        $data['admins'] = DB::table('admins')->where('role_id', '!=', 1)->count();
        $data['users'] = DB::table('users')->count();
        $total_received_payment = DB::table('payments')->where('status', 1)->sum('total_amount') + DB::table('payments')->where('status', 1)->sum('total_amount');
        $data['received_payment'] = $total_received_payment;
        $data['sms_campaigns'] = DB::table('sms_campaigns')->where('deleted_at', NULL)->count();
        $data['email_campaigns'] = DB::table('email_campaigns')->where('is_split_testing', 0)->where('deleted_at', NULL)->count();
        $data['split_campaigns'] = DB::table('email_campaigns')->where('is_split_testing', 1)->where('deleted_at', NULL)->count();
        $data['faqs'] = DB::table('faqs')->count();
        $data['packages'] = DB::table('packages')->where('status', 1)->where('deleted_at', NULL)->count();
        // $data['languages'] = []; //DB::table('languages')->count();
        $data['email_templates'] = DB::table('email_templates')->count();
        $data['cms_pages'] = DB::table('cms_pages')->count();
        $data['deleted_users'] = User::where('status', 3)->orderBy('deleted_at', 'DESC')->get();
        // $data['received_payment'] = 0;

        return view('admin.dashboard')->with($data);
    }

    public function ajaxReceivedNotification(Request $request)
    {
        $notification = Notification::find($request->id);
        if ($notification && $notification->user) {
            $message = str_replace("[name]", $notification->user->name, $notification->message);

            $html = '<li><a href="' . url($notification->link . '?notification_id=' . $notification->id) . '" class="notification-item" style="background:#e4edfc"><i class="fa fa-tags custom-bg-green2"></i><p><span class="text">' . $message . '</span><span class="timestamp">' . Carbon::createFromTimeStamp(strtotime($notification->created_at), "UTC")->diffForHumans() . '</span></p></a></li>';

            return response()->json(['success' => true, 'html' => $html]);
        }
    }
}
