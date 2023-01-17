<?php

namespace App\Http\Controllers\Api;

use App\CustomClasses\TranslationHandler;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Models\EmailTemplate;
use App\Models\PasswordReset;
use Hash;
use Hashids;

use App\Jobs\SendMail;

class PasswordResetController extends Controller
{
    /**
     * Create token password reset
     *
     * @param  [string] email
     * @return [string] message
     */
    public function sendResetLink(Request $request)
    {
        $messages = [
            'email.required' => TranslationHandler::getTranslation($request->lang, 'required'),
            'email.max' => TranslationHandler::getTranslation($request->lang, 'max_65'),
            'email.regex' => TranslationHandler::getTranslation($request->lang, 'valid_email'),
        ];

        $validator = $request->validate([
            'email' => ['required', 'string', 'regex:/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/', 'max:65'],
        ], $messages);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'status' => 0,
                'message' => TranslationHandler::getTranslation($request->lang, 'user_not_found'),
            ]);
        }

        $passwordReset = PasswordReset::updateOrCreate(
            ['email' => $user->email],
            [
                'email' => $user->email,
                'token' => \Str::random(60)
            ]
        );

        if ($user && $passwordReset) {
            $name = $user->name;
            $email = $user->email;
            $reset_link = url('/reset-password/' . $passwordReset->token);

            $email_template = EmailTemplate::where('type', 'reset_password')->first();
            $email_template = transformEmailTemplateModel($email_template, $user->language);

            $subject = $email_template['subject'];
            $content = $email_template['content'];

            $search = array("{{name}}", "{{link}}", "{{app_name}}");
            $replace = array($name, $reset_link, settingValue('site_title'));
            $content  = str_replace($search, $replace, $content);

            SendMail::dispatch($email, $subject, $content);

            //sendEmail($email, $subject, $content ,'', '', $lang);

            return response()->json([
                'status' => 1,
                'message' => TranslationHandler::getTranslation($request->lang, 'password_reset_link'),
            ], 200, ['Content-Type' => 'application/json']);
        }
    }
    /**
     * Find token password reset
     *
     * @param  [string] $token
     * @return [string] message
     * @return [json] passwordReset object
     */
    public function validateResetToken($token, Request $request)
    {
        $passwordReset = PasswordReset::where('token', $token)->first();
        if (!$passwordReset) {
            return response()->json([
                'status' => 0,
                'message' => TranslationHandler::getTranslation($request->lang, 'invalid_token'),
            ]);
        }

        if (Carbon::parse($passwordReset->updated_at)->addMinutes(60)->isPast()) {
            $passwordReset->delete();
            return response()->json([
                'status' => 0,
                'message' => TranslationHandler::getTranslation($request->lang, 'token_expired'),
            ]);
        }

        return response()->json([
            'data' => $passwordReset,
            'status' => 1,
            'message' => TranslationHandler::getTranslation($request->lang, "reset_token"),
        ]);
    }
    /**
     * Reset password
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [string] password_confirmation
     * @param  [string] token
     * @return [string] message
     * @return [json] user object
     */
    public function reset(Request $request)
    {

        $messages = [
            'required' => TranslationHandler::getTranslation($request->lang, 'required'),
            'string' => TranslationHandler::getTranslation($request->lang, 'required'),
            'email.required' => TranslationHandler::getTranslation($request->lang, 'valid_email'),
            'email.max' => TranslationHandler::getTranslation($request->lang, 'max_65'),
            'email.regex' => TranslationHandler::getTranslation($request->lang, 'valid_email'),
            'email.unique' => TranslationHandler::getTranslation($request->lang, 'email_taken'),
            'password.required' => TranslationHandler::getTranslation($request->lang, 'required'),
            'password.max' => TranslationHandler::getTranslation($request->lang, 'max_30'),
            'password.min' => TranslationHandler::getTranslation($request->lang, 'password_regex'),
            'password_confirmation.same' => TranslationHandler::getTranslation($request->lang, 'password_same'),
            'password.regex' => TranslationHandler::getTranslation($request->lang, 'password_regex'),
        ];

        $request->validate([
            'email' => ['required', 'string', 'regex:/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/', 'max:65'],
            'password'  => 'required|string|min:8|max:30|regex:/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/',
            'password_confirmation' => 'same:password',
            'token' => 'required|string'
        ], $messages);

        $passwordReset = PasswordReset::where([
            ['token', $request->token],
            ['email', $request->email]
        ])->first();

        if (!$passwordReset) {
            return response()->json([
                'status' => 0,
                'message' => "Invalid token!",
            ]);
        }

        $user = User::where('email', $passwordReset->email)->first();
        if (!$user) {
            return response()->json([
                'status' => 0,
                'message' => TranslationHandler::getTranslation($request->lang, 'user_not_found')
            ]);
        }

        $user->original_password = $request->password;
        $user->password = Hash::make($request->password);
        $user->save();
        $passwordReset->delete();

        return response()->json([
            'data' => $user,
            'status' => 1,
            'message' => TranslationHandler::getTranslation($request->lang, 'password_updated'),
        ], 200, ['Content-Type' => 'application/json']);
    }
}
