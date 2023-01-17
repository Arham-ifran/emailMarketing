<?php

namespace App\Http\Controllers;

// use App\CustomClasses\BounceHandler;

use App\Jobs\BounceJob;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Mail;
use Illuminate\Support\Facades\Crypt;

class MailController extends Controller
{
    private $folder_array = [];

    public function subscribeContact(Request $request)
    {
        if ($request->code) {
            $id = Crypt::decryptString($request->code);
            return redirect('/subscribe-contact/' . \Hashids::encode($id));
        }
        return redirect('/');
    }
    public function unsubscribeContact(Request $request)
    {
        if ($request->code) {
            $id = Crypt::decryptString($request->code);
            return redirect('/unsubscribe-contact/' . \Hashids::encode($id));
        }
        return redirect('/');
    }

    public function startBounceJob()
    {
        $today = Carbon::parse(now());
        $totalDuration = $today->addMinutes(1);
        BounceJob::dispatch()->delay($totalDuration);
        return 0;
    }
}
