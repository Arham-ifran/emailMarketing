<?php

namespace App\Http\Controllers\Auth\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Admin\Admin;
use App\Models\EmailTemplate;
use Session;
use Hash;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    public function forgotPasswordForm()
    {
        return view('admin.auth.passwords.email');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $admin = Admin::where('email', $request->email)->first();
        if (!$admin) {
            Session::flash('flash_danger', "We can't find a user with that e-mail address.");
            return redirect()->back();
            // return redirect()->back()->withInput()->withErrors(['error' => "We can't find a user with that e-mail address."]);
        }

        $admin->update([
            'password_reset_token' => \Str::random(60),
            'password_reset_token_date' => date('Y-m-d H:i:s')
        ]);

        $name = $admin->name;
        $email = $admin->email;
        $reset_link = url('/admin/reset-password/' . $admin->password_reset_token);

        $email_template = EmailTemplate::where('type', 'reset_password')->first();
        $subject = $email_template->subject;
        $content = $email_template->content;

        $search = array("{{name}}", "{{link}}", "{{app_name}}");
        $replace = array($name, $reset_link, settingValue('site_title'));
        $content  = str_replace($search, $replace, $content);

        SendMail::dispatch($email, $subject, $content);

        Session::flash('flash_success', 'We have e-mailed you reset password link! Please check your inbox or spam folder.');
        return redirect()->back();
    }

    public function resetPasswordForm($token)
    {
        $admin = Admin::where('password_reset_token', $token)->first();
        if (!$admin) {
            Session::flash('flash_danger', 'The password reset token is invalid.');
            return redirect('admin/forgot-password');
        }

        if (\Carbon\Carbon::parse($admin->password_reset_token_date)->addMinutes(60)->isPast()) {
            $admin->update([
                'password_reset_token' => null,
                'password_reset_token_date' => null
            ]);

            Session::flash('flash_danger', 'This password reset token is expired.');
            return redirect('admin/forgot-password');
        }

        return view('admin.auth.passwords.reset');
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'regex:/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/', 'max:191'],
            'password' => 'required|string|min:8|max:30|confirmed',
        ]);

        if ($validator->fails()) {
            Session::flash('flash_danger', $validator->messages());
            return redirect()->back()->withInput();
        }

        $admin = Admin::where('email', $request->email)->first();
        if (!$admin) {
            Session::flash('flash_danger', "We can't find a user with that e-mail address.");
            return redirect()->back()->withInput();
        }

        $admin->original_password = $request->password;
        $admin->password = Hash::make($request->password);
        $admin->password_reset_token = null;
        $admin->password_reset_token_date = null;
        $admin->save();

        Session::flash('flash_success', "Your password has been updated successfully.");
        return redirect('admin/login');
    }
}
