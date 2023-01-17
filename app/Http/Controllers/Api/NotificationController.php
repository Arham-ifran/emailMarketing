<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\GroupResource;
use App\Http\Resources\ContactResource;
use Hashids;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $id = auth()->user()->id;
        $notifications = Notification::where('user_id', $id)->orderBy('id', 'DESC')->paginate(5);
        $notseen = Notification::where('is_read', 0)->where('user_id', $id)->count();
        $response = [
            'notifications' => $notifications,
            'notseen' => $notseen,
            'message' => "Notifications fetched"
        ];

        return response($response, 201);
    }

    /**
     * Mark all the resources as read.
     *
     * @return \Illuminate\Http\Response
     */
    public function readAll()
    {
        $id = auth()->user()->id;
        $notseen = Notification::where('is_read', 0)->where('user_id', $id)->orderBy('id', 'DESC')->get();
        foreach ($notseen as $noti) {
            $noti->update(['is_read' => 1]);
        }
    }

    /**
     * Mark one of the resources as read.
     *
     * @return \Illuminate\Http\Response
     */
    public function readOne($id)
    {
        $uid = auth()->user()->id;
        $noti = Notification::where('id', $id)->where('user_id', $uid)->first();
        if ($noti) {
            $noti->update(['is_read' => 1]);
        }
    }

    /**
     * Delete a notification.
     *
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $noti = Notification::where('id', $id)->first();
        if ($noti) {
            $noti->delete();
        }
    }

    public function readPaymentNoti()
    {
        $user = auth()->user();
        $user->update(['unpaid_package_email_by_admin' => 0]);
    }

    public function readPaidNoti()
    {
        $user = auth()->user();
        $user->update(['package_updated_by_admin' => 0]);
    }
}
