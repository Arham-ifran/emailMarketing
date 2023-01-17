<?php

namespace App\Http\Controllers\Api;

use Abraham\TwitterOAuth\TwitterOAuth;
use App\CustomClasses\TranslationHandler;
use App\Http\Controllers\Controller;
use App\Jobs\SendMail;
use App\Models\Admin\Country;
use App\Models\Admin\Package;
use App\Models\Admin\PackageSubscription;
use App\Models\Admin\Timezone;
use App\Models\Contact;
use App\Models\EmailTemplate;
use App\Models\Group;
use App\Models\User;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Hash;
use Hashids;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
//use Socialite;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Laravel\Socialite\Facades\Socialite;
use Storage;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => [
            'register', 'login', 'verifyAccount', 'resendVerificationEmail', 'checkUser', 'googleLoginAuthUrl', 'googleLoginCallback', 'facebookLoginAuthUrl', 'facebookLoginCallback', 'twitterLoginAuthUrl', 'twitterLoginCallback',
        ]]);
    }

    function curlRequest($url)
    {
        $ch = curl_init();
        $getUrl = $url;
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_URL, $getUrl);
        curl_setopt($ch, CURLOPT_TIMEOUT, 80);

        $response = curl_exec($ch);
        return $response;

        curl_close($ch);
    }

    public function register(Request $request)
    {

        $messages = [
            'name.required' => TranslationHandler::getTranslation($request->lang, 'required'),
            'email.required' => TranslationHandler::getTranslation($request->lang, 'required'),
            'email.max' => TranslationHandler::getTranslation($request->lang, 'max_65'),
            'email.regex' => TranslationHandler::getTranslation($request->lang, 'valid_email'),
            'email.unique' => TranslationHandler::getTranslation($request->lang, 'email_taken'),
            'password.required' => TranslationHandler::getTranslation($request->lang, 'required'),
            'password.min' => TranslationHandler::getTranslation($request->lang, 'password_regex'),
            'password.max' => TranslationHandler::getTranslation($request->lang, 'max_30'),
            'password_confirmation.same' => TranslationHandler::getTranslation($request->lang, 'password_same'),
            'password.regex' => TranslationHandler::getTranslation($request->lang, 'password_regex'),
            'agreed.accepted' =>  TranslationHandler::getTranslation($request->lang, 'agree_terms'),
            'country_id.required' => TranslationHandler::getTranslation($request->lang, 'required'),
            'timezone.required' => TranslationHandler::getTranslation($request->lang, 'required'),
            'captcha_token.required' => TranslationHandler::getTranslation($request->lang, 'required'),
        ];

        $validation_rules = array(
            'name' => 'required|string|max:100',
            'email' => ['required', 'string', 'regex:/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/', 'max:100', 'unique:users'],
            'password' => 'required|string',
            'password_confirmation' => 'same:password',
            'agreed' => 'accepted',
            'country_id' => 'required',
            'timezone' => 'required',
            'captcha_token' => 'required',
        );

        $createGoogleUrl = 'https://www.google.com/recaptcha/api/siteverify?secret=' . "6LfOUwoeAAAAAI4pQQltHnfDrjesTCXKkTN-V8Zl" . '&response=' . $request->captcha_token;
        $verifyRecaptcha = $this->curlRequest($createGoogleUrl);
        $decodeGoogleResponse = json_decode($verifyRecaptcha, true);
        if ($decodeGoogleResponse['success'] == 1) {

            $validation_rules['password'] = 'required|string|min:8|max:30|regex:/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/';
            $validatedData = $request->validate($validation_rules, $messages);

            do {
                $api_token = Str::random(132);
                $secret_key = Str::random(20);
            } while (User::where("api_token", $api_token)->orWhere("secret_key", $secret_key)->first() instanceof User);

            $user = User::create([
                'name' => $request->input('name'),
                'username' => $request->input('email'),
                'email' => $request->input('email'),
                'timezone' => $request->timezone != '' ? $request->timezone : 'UTC',
                'country_id' => $request->has('country_id') ? $request->country_id : 81,
                'password' => Hash::make($request->input('password')),
                'original_password' => $request->input('password'),
                // 'status' => 1, //2,
                'status' => 2, //2,
                'secret_key' => $secret_key,
                'api_token' => $api_token,
                'language' => $request->lang ? $request->lang : 'en'
            ]);

            if ($user) {
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
                $api = 2;
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

                    $index = array_search(3, $features); //api
                    if ($index >= 0)
                        $api = 1;
                    else
                        $api = 2;
                }

                $packageSubscription = PackageSubscription::create([
                    'user_id'       =>  $user->id,
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

                $user->update([
                    'package_id' => $package->id,
                    'package_subscription_id' => $packageSubscription->id,
                    'on_trial' => $on_trial,
                    'package_recurring_flag' => 0
                ]);

                $user->update(['api_status' => $api == 2 ? 2 : $user->api_status]);
                // =======================
                // Personal Package (FREE)  DONE
                // ========================

                // ************************* //
                // Make Default Mailing List for User
                // ************************* //

                // $group = Group::create([
                //     'user_id' => $user->id,
                //     'name' => 'default',
                //     'description' => 'This is your default group of contacts',
                //     'for_sms' => 1,
                //     'for_email' => 1,
                // ]);

                // ************************* //
                // Send Verify Link To User
                // ************************* //

                $name = $user->name;
                $email = $user->email;
                $link = url('/verify-account/' . Hashids::encode($user->id));

                $email_template = EmailTemplate::where('type', 'sign_up_confirmation')->first();
                $email_template = transformEmailTemplateModel($email_template, $user->language);

                $subject = $email_template['subject'];
                $content = $email_template['content'];

                $search = array("{{name}}", "{{link}}", "{{app_name}}");
                $replace = array($name, $link, settingValue('site_title'));
                $content = str_replace($search, $replace, $content);

                SendMail::dispatch($email, $subject, $content);
                //sendEmail($email, $subject, $content, '', '', $lang);

                return response()->json([
                    'data' => $user->makeHidden(['original_password']),
                    'status' => 1,
                    'message' => TranslationHandler::getTranslation($request->lang, 'verification_link_sent'),
                ], 200, ['Content-Type' => 'application/json']);
            } else {
                return response()->json(['status' => 0, 'message' => TranslationHandler::getTranslation($request->lang, 'unable_to_verify')], 200, ['Content-Type' => 'application/json']);
            }
        } else {
            return response()->json(['captcha' => 0, 'message' => TranslationHandler::getTranslation($request->lang, 'unable_to_verify_google')], 200, ['Content-Type' => 'application/json']);
        }
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {

        $messages = [
            'email.required' => TranslationHandler::getTranslation($request->lang, 'required'),
            'email.regex' => TranslationHandler::getTranslation($request->lang, 'valid_email'),
            'password.required' => TranslationHandler::getTranslation($request->lang, 'required'),
            'captcha_token.required' => TranslationHandler::getTranslation($request->lang, 'required'),
        ];

        $validator = $request->validate([
            'email' => ['required', 'string', 'regex:/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/'],
            'password' => 'required|string',
            'captcha_token' => 'required'
        ], $messages);

        $createGoogleUrl = 'https://www.google.com/recaptcha/api/siteverify?secret=' . "6LfOUwoeAAAAAI4pQQltHnfDrjesTCXKkTN-V8Zl" . '&response=' . $request->captcha_token;
        $verifyRecaptcha = $this->curlRequest($createGoogleUrl);
        $decodeGoogleResponse = json_decode($verifyRecaptcha, true);
        if ($decodeGoogleResponse['success'] == 1) {

            $credentials = ['email' => trim($request->email), 'password' => trim($request->password)];

            if (!$token = auth()->attempt($credentials)) {
                return response()->json(['status' => 0, 'message' => TranslationHandler::getTranslation($request->lang, 'invalid_credentials')], 200, ['Content-Type' => 'application/json']);
            }


            $user = User::where(['email' => $request->input('email')])->first();

            $is_user_active = true;
            $resend_email_flag = false;
            $message = '';

            switch ($user->status) {
                case 0:
                    $message = TranslationHandler::getTranslation($request->lang, 'account_disabled');
                    $is_user_active = false;
                    break;
                case 2:
                    $message = TranslationHandler::getTranslation($request->lang, 'verify_email');
                    $is_user_active = false;
                    $resend_email_flag = true;
                    break;
                case 3:
                    $message = TranslationHandler::getTranslation($request->lang, 'account_deleted');
                    $is_user_active = false;
                    break;
            }

            if ($is_user_active == false) {
                auth()->logout();
                return response()->json(['resend_email_flag' => $resend_email_flag, 'email' => $request->email, 'status' => 0, 'user_status' => $user->status, 'message' => $message], 200, ['Content-Type' => 'application/json']);
            }

            $user->update([
                'last_active_at' => now(),
                'language' => $request->lang ? $request->lang : $user->language
                //'ip_address' => $_SERVER['REMOTE_ADDR']
            ]);

            return response()->json([
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL(),
                'data' => $user->makeHidden(['original_password']),
                'status' => 1,
                'message' => "Login successfully!",
            ], 200, ['Content-Type' => 'application/json']);
        } else {
            return response()->json(['status' => 0, 'message' => TranslationHandler::getTranslation($request->lang, 'unable_to_verify_google')], 200, ['Content-Type' => 'application/json']);
        }
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile(Request $request)
    {
        $country_vat = Country::where('id', auth()->user()->country_id)->first()->vat;
        $contacts = Contact::where('user_id', auth()->user()->id)->count();
        return response()->json([
            'data' => auth()->user()->makeHidden(['subscription', 'original_password']),
            'vat_rate' => $country_vat,
            'contacts' => $contacts,
            'status' => 1,
            'message' => 'Your Profile',
        ], 200, ['Content-Type' => 'application/json']);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        auth()->logout();

        return response()->json(['status' => 1, 'message' => TranslationHandler::getTranslation($request->lang, 'logged_out')], 200, ['Content-Type' => 'application/json']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL(),
            'status' => 1,
            'message' => 'User Token',
        ]);
    }

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:8|same:confirm_password',
            'confirm_password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->errors()->all()], 400, ['Content-Type' => 'application/json']);
        }

        $user = auth()->user();

        if (Hash::check($request->input('old_password'), $user->password)) {
            $user->update([
                'password' => Hash::make($request->input('new_password')),
                'original_password' => $request->input('new_password'),
            ]);

            return response()->json(['status' => 1, 'message' => TranslationHandler::getTranslation($request->lang, 'password_updated')], 200, ['Content-Type' => 'application/json']);
        } else {
            return response(['status' => 0, 'message' => TranslationHandler::getTranslation($request->lang, 'incorrect_password'), 'errors' => ['old_password_incorrect' => [TranslationHandler::getTranslation($request->lang, 'incorrect_password')]]], 400);
            // return response()->json(['status' => 0, 'message' => 'Old password is not correct.'], 400, ['Content-Type' => 'application/json']);
        }
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $messages = [
            'name.required' => TranslationHandler::getTranslation($request->lang, 'required'),
            'name.max' => TranslationHandler::getTranslation($request->lang, 'max_35'),
            'email.required' => TranslationHandler::getTranslation($request->lang, 'required'),
            'email.max' => TranslationHandler::getTranslation($request->lang, 'max_65'),
            'email.regex' => TranslationHandler::getTranslation($request->lang, 'valid_email'),
            'email.unique' => TranslationHandler::getTranslation($request->lang, 'email_taken'),
            'country_id.required' => TranslationHandler::getTranslation($request->lang, 'required'),
            'timezone.required' => TranslationHandler::getTranslation($request->lang, 'required'),
            'street.max' => TranslationHandler::getTranslation($request->lang, 'max_250'),
            'city.max' => TranslationHandler::getTranslation($request->lang, 'max_50'),
        ];

        $request->validate([
            'name' => 'required|string|max:100',
            'email' => ['required', 'regex:/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/', 'string', 'max:100', Rule::unique('users')->ignore($user->id)],
            'country_id' => 'required',
            'timezone' => 'required',
            'street' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
        ], $messages);

        if ($request->zip_code) {
            $dig  = mb_strlen((string) $request->zip_code);
            if (!(is_numeric($request->zip_code) && ($dig == 4  || $dig == 5)))
                return response(['message' => TranslationHandler::getTranslation($request->lang, 'incorrect_zipcode'), 'errors' => ['zip_code' => [TranslationHandler::getTranslation($request->lang, 'zipcode_regex')]]], 422);
        }

        $user->update([
            'name' => $request->name,
            'city' => $request->city,
            'country_id' => $request->country_id,
            'timezone' => $request->timezone != '' ? $request->timezone : 'UTC',
            'street' => $request->street,
            'name' => $request->name,
            'zip_code' => $request->zip_code,
            'language' => $request->lang ? $request->lang : $user->language
        ]);

        if (!empty($request->password) || !empty($request->old_password)) {
            // ******************* //
            // Password Validation //
            // ******************* //

            // $messages = [
            //     'old_password.required' => $lang['error_message']['required'],
            //     'password.required' => $lang['error_message']['required'],
            //     'password.max' => $lang['error_message']['max_30'],
            //     'password.min' => $lang['error_message']['min_8'],
            //     'password_confirmation.same' => $lang['error_message']['password_confirmation_same'],
            //     'password.regex' => $lang['error_message']['password_field_title'],
            // ];

            $messages = [
                'old_password.required' => TranslationHandler::getTranslation($request->lang, 'required'),
                'password.required' => TranslationHandler::getTranslation($request->lang, 'required'),
                'password.max' => TranslationHandler::getTranslation($request->lang, 'max_30'),
                'password.min' => TranslationHandler::getTranslation($request->lang, 'password_regex'),
                'password_confirmation.same' => TranslationHandler::getTranslation($request->lang, 'password_same'),
                'password.regex' => TranslationHandler::getTranslation($request->lang, 'password_regex'),
            ];

            $request->validate([
                'old_password' => 'required|string',
                'password' => 'required|string|min:8|max:30|regex:/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/',
                'password_confirmation' => 'same:password',
            ], $messages);

            if (Hash::check($request->input('old_password'), $user->password)) {
                $user->update([
                    'password' => Hash::make($request->input('password')),
                    'original_password' => $request->input('password'),
                ]);
            } else {
                return response(['status' => 0, 'message' => TranslationHandler::getTranslation($request->lang, 'incorrect_password'), 'errors' => ['old_password_incorrect' => [TranslationHandler::getTranslation($request->lang, 'incorrect_password')]]], 400);
                // return response()->json(['status' => 0, 'message' => ['old_password' => ['old_password_incorrect']]], 200, ['Content-Type' => 'application/json']);
            }
        }

        if (!empty($request->files) && $request->hasFile('profile_image')) {
            // *************** //
            // File Validation //
            // *************** //
            // $file_messages = [
            //     'profile_image.required' => $lang['error_message']['required'],
            //     'profile_image.image' => $lang['error_message']['image_type'],
            // ];
            $file_messages = [
                'profile_image.required' => TranslationHandler::getTranslation($request->lang, 'min_8'),
                'profile_image.image' => TranslationHandler::getTranslation($request->lang, 'image_only'),
            ];

            $request->validate([
                'profile_image' => 'required|file|image|mimes:jpg,jpeg,png,svg',
            ], $file_messages);

            // *********** //
            // Upload File //
            // *********** //

            $target_path = 'public/users/profile-images';
            $file = $request->file('profile_image');
            $profile_image = 'profile_image-' . uniqid() . '.' . $file->getClientOriginalExtension();

            $old_file = public_path() . '/storage/users/profile-images/' . $user->profile_image;
            if (file_exists($old_file) && !empty($user->profile_image)) {
                Storage::delete($target_path . '/' . $user->profile_image);
            }

            $path = $file->storeAs($target_path, $profile_image);

            // dd('/storage/users/profile-images/' . $profile_image);
            $user->update([
                'profile_image_path' => '/storage/users/profile-images/' . $profile_image,
            ]);
        }

        return response()->json([
            'data' => auth()->user()->makeHidden(['subscription', 'original_password']),
            'status' => 1,
            'message' => TranslationHandler::getTranslation($request->lang, 'profile_updated'),
        ], 200, ['Content-Type' => 'application/json']);
    }

    public function verifyAccount(Request $request)
    {
        $request->validate([
            'id' => 'required'
        ]);

        $id = $request->id;

        if (!isset(Hashids::decode($id)[0])) {
            // return redirect('/verified-error');
            return response()->json(['status' => 0, 'message' => TranslationHandler::getTranslation($request->lang, 'unable_to_verify')]);
        }

        $user = User::find(Hashids::decode($id)[0]);
        if ($user) {

            $user->update([
                'status' => 1,
            ]);

            $credentials = ['email' => $user->email, 'password' => $user->original_password];
            $token = auth()->attempt($credentials);

            $user->update([
                'last_active_at' => now(),
                'ip_address' => $_SERVER['REMOTE_ADDR'],
            ]);

            return response()->json([
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL(),
                'data' => $user->makeHidden(['subscription', 'original_password']),
                'status' => 1,
                'message' => "Login successfully!",
            ]);

            return response()->json(['status' => 1, 'message' => 'Your account has been verified successfully.']);
            // return redirect('/verified');
        } else {
            // return redirect('/verified-error');
            return response()->json(['status' => 0, 'message' => 'unable_verify_account']);
        }
    }

    public function updateExtraInformation(Request $request)
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'vat_number' => 'required|string|max:50',
            'company_industry' => 'required|string|max:250',
            'street' => 'nullable|string|max:250',
            'city' => 'nullable|string|max:100|max:100',
            'country_id' => 'required',
        ]);
        if ($request->zip_code) {
            $dig  = mb_strlen((string) $request->zip_code);
            if (!(is_numeric($request->zip_code) && ($dig == 4  || $dig == 5)))
                return response(['message' => TranslationHandler::getTranslation($request->lang, 'incorrect_zipcode'), 'errors' => ['zip_code' => [TranslationHandler::getTranslation($request->lang, 'zipcode_regex')]]], 422);
        }

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->errors()->all()], 400, ['Content-Type' => 'application/json']);
        }

        $user->update([
            'name' => $request->name,
            'city' => $request->city,
            'country_id' => $request->country_id,
            'street' => $request->street,
            'name' => $request->name,
            'zip_code' => $request->zip_code
        ]);

        if (!empty($user->freshbook_client_id)) {
            $freshbook = Freshbook::integration();

            if (!empty($freshbook) && $freshbook->status) {
                \Log::info("Update freshbook client details request");
                Freshbook::updateClient($user);
            }
        }

        return response()->json(['status' => 1, 'message' => TranslationHandler::getTranslation($request->lang, 'profile_updated')], 200, ['Content-Type' => 'application/json']);
    }

    public function accountSettings(Request $request)
    {
        return response()->json([
            'data' => auth()->user()->accountSettings,
            'status' => 1,
            'message' => 'Your Account Settings',
        ]);
    }

    public function updateAccountSettings(Request $request)
    {
        $lang = isset($request->query()['lang']) ? $request->query()['lang'] : 'en';
        // $lang_file = public_path('i18n/translations/' . $lang . '.json');
        // $lang = json_decode(file_get_contents($lang_file), true);
        // $request->validate([
        //     'address'               => 'required',
        //     'card_holder_name'      => 'required|max:250',
        //     'card_brand'            => 'required|max:100',
        //     'card_number'           => 'required|max:16',
        //     'expire_month'          => 'required',
        //     'expire_year'           => 'required',
        //     'cvc'                   => 'required|max:4',
        // ]);

        AccountSetting::updateOrCreate(
            ['user_id' => auth()->user()->id],
            [
                'user_id' => auth()->user()->id,
                // 'address'               => $request->address,
                // 'card_holder_name'      => $request->card_holder_name,
                // 'card_brand'            => $request->card_brand,
                // 'card_number'           => encrypt($request->card_number),
                // 'card_last_four_digits' => substr($request->card_number, -4),
                // 'expire_month'          => $request->expire_month,
                // 'expire_year'           => $request->expire_year,
                // 'cvc'                   => encrypt($request->cvc),
                'migration_init_notification' => $request->migration_init_notification,
                'migration_complete_notification' => $request->migration_complete_notification,
                'blacklist_email' => $request->blacklist_email,
            ]
        );

        return response()->json(['status' => 1, 'message' => TranslationHandler::getTranslation($request->lang, 'profile_updated')]);
    }

    public function resendVerificationEmail(Request $request)
    {
        $lang = (isset($request->query()['lang']) && $request->query()['lang'] != 'en') ? $request->query()['lang'] : 'en';
        // $lang_file = public_path('i18n/translations/' . $lang . '.json');
        // $lang_arr = json_decode(file_get_contents($lang_file), true);

        $user = User::where('email', $request->email)->first();

        if ($user) {
            // ************************* //
            // Send Verify Link To User
            // ************************* //

            $name = $user->name;
            $email = $user->email;
            $link = url('/verify-account/' . Hashids::encode($user->id));

            $email_template = EmailTemplate::where('type', 'sign_up_confirmation')->first();
            $email_template = transformEmailTemplateModel($email_template, $lang);

            $subject = $email_template['subject'];
            $content = $email_template['content'];

            $search = array("{{name}}", "{{link}}", "{{app_name}}");
            $replace = array($name, $link, settingValue('site_title'));
            $content = str_replace($search, $replace, $content);

            SendMail::dispatch($email, $subject, $content, '', '', $lang);

            return response()->json([
                'status' => 1,
                'message' => TranslationHandler::getTranslation($request->lang, 'resend_verification_message'),
            ]);
        } else {
            return response()->json([
                'status' => 0,
                'message' => TranslationHandler::getTranslation($request->lang, 'user_not_found'),
            ]);
        }
    }

    public function checkUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'regex:/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => $validator->errors()->all(),
            ]);
        }

        $user = User::where(['email' => $request->email])->first();

        if (!$user) {
            return response()->json([
                'status' => 0,
                'message' => TranslationHandler::getTranslation($request->lang, 'user_not_found'),
            ]);
        } else {
            return response()->json([
                'data' => array(
                    'user' => $user,
                ),
                'status' => 1,
                'message' => TranslationHandler::getTranslation($request->lang, 'required'),
            ]);
        }
    }

    /**
     * Google login Auth Url
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function googleLoginAuthUrl(Request $request)
    {

        $socialite = Socialite::driver('google')->stateless()->redirect();

        return response()->json([
            'url' => $socialite->getTargetUrl(),
            'status' => 1,
            'message' => 'Google Login Auth URL',
        ]);
    }

    /**
     * Google Login Callback
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function googleLoginCallback(Request $request)
    {
        $googleUser = Socialite::driver('google')->stateless()->user();
        $user = User::where('email', $googleUser->email)->first();
        if ($user) {

            if ($user->status == 2) {
                return response()->json([
                    'status' => 0,
                    'message' => TranslationHandler::getTranslation($request->lang, 'account_unverified'),
                ]);
            } elseif ($user->status == 0) {
                $user->update(['status' => 1]);
            }

            $credentials = ['email' => $googleUser->email, 'password' => $user->original_password];
            if (!$token = auth()->attempt($credentials)) {
                return response()->json(['status' => 0, 'message' => TranslationHandler::getTranslation($request->lang, 'invalid_credentials')], 200, ['Content-Type' => 'application/json']);
            }

            $user->update([
                'last_active_at' => now(),
                //'ip_address' => $_SERVER['REMOTE_ADDR']
            ]);

            return response()->json([
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL(),
                'data' => $user->makeHidden(['original_password']),
                'status' => 1,
                'message' => TranslationHandler::getTranslation($request->lang, 'login_success'),
            ], 200, ['Content-Type' => 'application/json']);
        } else {

            do {
                $api_token = Str::random(132);
                $secret_key = Str::random(20);
            } while (User::where("api_token", $api_token)->orWhere("secret_key", $secret_key)->first() instanceof User);

            $random_password = Str::random(10);

            $user = User::create([
                'name' => $googleUser->name,
                'username' => $googleUser->email,
                'email' => $googleUser->email,
                'google_id' => $googleUser->id,
                'password' => Hash::make($random_password),
                'original_password' => $random_password,
                'status' => 1, //2,
                'secret_key' => $secret_key,
                'api_token' => $api_token,
                'language' => 'en',
                'timezone' => 'UTC',
                'country_id' => 81,

            ]);

            if ($user) {

                // ****************************************************//
                // Send Email About User Creation  //
                // *************************************************** //

                $email_template = EmailTemplate::where('type', 'user_created_by_social_login')->first();
                $email_template = transformEmailTemplateModel($email_template, $user->language);
                $name = $user->name;
                $email = $user->email;
                $password = $user->original_password;
                $link = url('/verify-account/' . Hashids::encode($user->id));
                $subject = $email_template['subject'];
                $content = $email_template['content'];

                $search = array("{{name}}", "{{password}}", "{{link}}", "{{app_name}}");
                $replace = array($name, $password, $link, settingValue('site_title'));
                $content = str_replace($search, $replace, $content);

                SendMail::dispatch($email, $subject, $content);

                // ************************* //
                // Make Default Mailing List for User
                // ************************* //

                // $group = Group::create([
                //     'user_id' => $user->id,
                //     'name' => 'default',
                //     'description' => 'This is your default group of contacts',
                //     'for_sms' => 1,
                //     'for_email' => 1,
                // ]);
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
                $api = 2;
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


                    $index = array_search(3, $features); //api
                    if ($index >= 0)
                        $api = 1;
                    else
                        $api = 2;
                }

                $packageSubscription = PackageSubscription::create([
                    'user_id'       =>  $user->id,
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

                $user->update([
                    'package_id' => $package->id,
                    'package_subscription_id' => $packageSubscription->id,
                    'on_trial' => $on_trial,
                    'package_recurring_flag' => 0
                ]);
                $user->update(['api_status' => $api == 2 ? 2 : $user->api_status]);
                // =======================
                // Personal Package (FREE)  DONE
                // ========================
            }

            $credentials = ['email' => $googleUser->email, 'password' => $user->original_password];
            if (!$token = auth()->attempt($credentials)) {
                return response()->json(['status' => 0, 'message' => TranslationHandler::getTranslation($request->lang, 'invalid_credentials')], 200, ['Content-Type' => 'application/json']);
            }

            // $user = User::where(['email' => $request->input('email')])->first();

            // $is_user_active = true;
            // $resend_email_flag = false;
            // $message = '';

            // switch ($user->status) {
            //     case 0:
            //         $message = $lang['alert_messages']['account_disabled_contact_admin'];
            //         $is_user_active = false;
            //         break;
            //     case 2:
            //         $message = $lang['error_message']['verify_account'];
            //         $is_user_active = false;
            //         $resend_email_flag = true;
            //         break;
            //     case 3:
            //         $message = $lang['alert_messages']['account_deleted_contact_admin'];
            //         $is_user_active = false;
            //         break;
            // }

            // if ($is_user_active == false) {
            //     auth()->logout();
            //     return response()->json(['resend_email_flag' => $resend_email_flag, 'email' => $request->email, 'status' => 0, 'user_status' => $user->status, 'message' => $message], 200, ['Content-Type' => 'application/json']);
            // }

            $user->update([
                'last_active_at' => now(),
                //'ip_address' => $_SERVER['REMOTE_ADDR']
            ]);

            return response()->json([
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL(),
                'data' => $user->makeHidden(['original_password']),
                'status' => 1,
                'message' => TranslationHandler::getTranslation($request->lang, 'account_created'),
            ], 200, ['Content-Type' => 'application/json']);

            // return response()->json([
            //     'status' => 1,
            //     'message' => "Account is created successfully!",
            // ]);
        }
    }

    /**
     * Facebook login Auth Url
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function facebookLoginAuthUrl(Request $request)
    {
        $socialite = Socialite::driver('facebook')->stateless()->redirect();
        // print_r($socialite);
        // exit;
        return response()->json([
            'url' => $socialite->getTargetUrl(),
            'status' => 1,
            'message' => 'Google Login Auth URL',
        ]);
    }

    /**
     * Facebook Login Callback
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function facebookLoginCallback(Request $request)
    {
        $facebookUser = Socialite::driver('facebook')->stateless()->user();
        $user = User::where('email', $facebookUser->email)->first();
        if ($user) {

            if ($user->status == 2) {
                return response()->json([
                    'status' => 0,
                    'message' => TranslationHandler::getTranslation($request->lang, 'account_unverified'),
                ]);
            } elseif ($user->status == 0) {
                $user->update(['status' => 1]);
            }

            $credentials = ['email' => trim($facebookUser->email), 'password' => trim($user->original_password)];
            if (!$token = auth()->attempt($credentials)) {
                return response()->json(['status' => 0, 'message' => TranslationHandler::getTranslation($request->lang, 'invalid_credentials')], 200, ['Content-Type' => 'application/json']);
            }

            $user->update([
                'last_active_at' => now(),
                //'ip_address' => $_SERVER['REMOTE_ADDR']
            ]);

            return response()->json([
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL(),
                'data' => $user->makeHidden(['original_password']),
                'status' => 1,
                'message' => "Logged in successfully!",
            ], 200, ['Content-Type' => 'application/json']);
        } else {

            do {
                $api_token = Str::random(132);
                $secret_key = Str::random(20);
            } while (User::where("api_token", $api_token)->orWhere("secret_key", $secret_key)->first() instanceof User);

            $random_password = Str::random(10);

            $user = User::create([
                'name' => $facebookUser->name,
                'username' => $facebookUser->email,
                'email' => $facebookUser->email,
                'google_id' => $facebookUser->id,
                'password' => Hash::make($random_password),
                'original_password' => $random_password,
                'status' => 1, //2,
                'secret_key' => $secret_key,
                'api_token' => $api_token,
                'language' => 'en',
                'timezone' => 'UTC',
                'country_id' => 81,

            ]);

            if ($user) {

                // ****************************************************//
                // Send Email About User Creation  //
                // *************************************************** //

                $email_template = EmailTemplate::where('type', 'user_created_by_social_login')->first();
                $email_template = transformEmailTemplateModel($email_template, $user->language);
                $name = $user->name;
                $email = $user->email;
                $password = $user->original_password;
                $link = url('/verify-account/' . Hashids::encode($user->id));
                $subject = $email_template['subject'];
                $content = $email_template['content'];

                $search = array("{{name}}", "{{password}}", "{{link}}", "{{app_name}}");
                $replace = array($name, $password, $link, settingValue('site_title'));
                $content = str_replace($search, $replace, $content);

                SendMail::dispatch($email, $subject, $content);

                // ************************* //
                // Make Default Mailing List for User
                // ************************* //

                // $group = Group::create([
                //     'user_id' => $user->id,
                //     'name' => 'default',
                //     'description' => 'This is your default group of contacts',
                //     'for_sms' => 1,
                //     'for_email' => 1,
                // ]);

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
                $api = 2;
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

                    $index = array_search(3, $features); //api
                    if ($index >= 0)
                        $api = 1;
                    else
                        $api = 2;
                }

                $packageSubscription = PackageSubscription::create([
                    'user_id'       =>  $user->id,
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

                $user->update([
                    'package_id' => $package->id,
                    'package_subscription_id' => $packageSubscription->id,
                    'on_trial' => $on_trial,
                    'package_recurring_flag' => 0
                ]);
                $user->update(['api_status' => $api == 2 ? 2 : $user->api_status]);
                // =======================
                // Personal Package (FREE)  DONE
                // ========================
            }

            $credentials = ['email' => trim($facebookUser->email), 'password' => trim($user->original_password)];

            if (!$token = auth()->attempt($credentials)) {
                return response()->json(['status' => 0, 'message' => TranslationHandler::getTranslation($request->lang, 'invalid_credentials')], 200, ['Content-Type' => 'application/json']);
            }

            // $user = User::where(['email' => $request->input('email')])->first();

            // $is_user_active = true;
            // $resend_email_flag = false;
            // $message = '';

            // switch ($user->status) {
            //     case 0:
            //         $message = $lang['alert_messages']['account_disabled_contact_admin'];
            //         $is_user_active = false;
            //         break;
            //     case 2:
            //         $message = $lang['error_message']['verify_account'];
            //         $is_user_active = false;
            //         $resend_email_flag = true;
            //         break;
            //     case 3:
            //         $message = $lang['alert_messages']['account_deleted_contact_admin'];
            //         $is_user_active = false;
            //         break;
            // }

            // if ($is_user_active == false) {
            //     auth()->logout();
            //     return response()->json(['resend_email_flag' => $resend_email_flag, 'email' => $request->email, 'status' => 0, 'user_status' => $user->status, 'message' => $message], 200, ['Content-Type' => 'application/json']);
            // }

            $user->update([
                'last_active_at' => now(),
                //'ip_address' => $_SERVER['REMOTE_ADDR']
            ]);

            return response()->json([
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL(),
                'data' => $user->makeHidden(['original_password']),
                'status' => 1,
                'message' => TranslationHandler::getTranslation($request->lang, 'account_created'),
            ], 200, ['Content-Type' => 'application/json']);
        }
    }

    /**
     * Twitter login Auth Url
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function twitterLoginAuthUrl(Request $request)
    {
        // $token = '1307954169131794432-PuuOigiv4E3zAwh1EcLqdtNM761RLR';
        // $secret = 'xaza7TV8mg2uvsentAy2aWb2M4kflthbwmULCPaWnaJp4UG7U8';
        // $user = Socialite::driver('twitter')->userFromTokenAndSecret($token, $secret);
        // print_r()
        $socialite = Socialite::driver('twitter')->redirect();

        return response()->json([
            'url' => $socialite->getTargetUrl(),
            'status' => 1,
            'message' => 'Twitter Login Auth URL',
        ]);
    }

    /**
     * Twitter Login Callback
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function twitterLoginCallback(Request $request)
    {
        //$input = $request->all();
        $tokens = $this->access_token($request->oauth_token, $request->oauth_verifier);
        $twitterUser = Socialite::driver('twitter')->userFromTokenAndSecret($tokens->oauth_token, $tokens->oauth_token_secret);
        $user = User::where('email', $twitterUser->email)->first();

        if ($user) {

            if ($user->status == 2) {
                return response()->json([
                    'status' => 0,
                    'message' => TranslationHandler::getTranslation($request->lang, 'account_unverified'),
                ]);
            } elseif ($user->status == 0) {
                $user->update(['status' => 1]);
            }

            $credentials = ['email' => $twitterUser->email, 'password' => $user->original_password];
            if (!$token = auth()->attempt($credentials)) {
                return response()->json(['status' => 0, 'message' => TranslationHandler::getTranslation($request->lang, 'invalid_credentials')], 200, ['Content-Type' => 'application/json']);
            }

            $user->update([
                'last_active_at' => now(),
                //'ip_address' => $_SERVER['REMOTE_ADDR']
            ]);

            return response()->json([
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL(),
                'data' => $user->makeHidden(['original_password']),
                'status' => 1,
                'message' => TranslationHandler::getTranslation($request->lang, 'login_success'),
            ], 200, ['Content-Type' => 'application/json']);
        } else {

            do {
                $api_token = Str::random(132);
                $secret_key = Str::random(20);
            } while (User::where("api_token", $api_token)->orWhere("secret_key", $secret_key)->first() instanceof User);

            $random_password = Str::random(10);

            $user = User::create([
                'name' => $twitterUser->name,
                'username' => $twitterUser->email,
                'email' => $twitterUser->email,
                'twitter_id' => $twitterUser->id,
                'password' => Hash::make($random_password),
                'original_password' => $random_password,
                'status' => 1,
                'secret_key' => $secret_key,
                'api_token' => $api_token,
                'language' => 'en',
                'timezone' => 'UTC',
                'country_id' => 81,

            ]);

            if ($user) {

                // ****************************************************//
                // Send Email About User Creation  //
                // *************************************************** //

                $email_template = EmailTemplate::where('type', 'user_created_by_social_login')->first();
                $email_template = transformEmailTemplateModel($email_template, $user->language);
                $name = $user->name;
                $email = $user->email;
                $password = $user->original_password;
                $link = url('/verify-account/' . Hashids::encode($user->id));
                $subject = $email_template['subject'];
                $content = $email_template['content'];

                $search = array("{{name}}", "{{password}}", "{{link}}", "{{app_name}}");
                $replace = array($name, $password, $link, settingValue('site_title'));
                $content = str_replace($search, $replace, $content);

                SendMail::dispatch($email, $subject, $content);

                // ************************* //
                // Make Default Mailing List for User
                // ************************* //
                // Group::create([
                //     'user_id' => $user->id,
                //     'name' => 'default',
                //     'description' => 'This is your default group of contacts',
                //     'for_sms' => 1,
                //     'for_email' => 1,
                // ]);
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
                $api = 2;
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

                    $index = array_search(3, $features); //api
                    if ($index >= 0)
                        $api = 1;
                    else
                        $api = 2;
                }

                $packageSubscription = PackageSubscription::create([
                    'user_id'       =>  $user->id,
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

                $user->update([
                    'package_id' => $package->id,
                    'package_subscription_id' => $packageSubscription->id,
                    'on_trial' => $on_trial,
                    'package_recurring_flag' => 0
                ]);
                $user->update(['api_status' => $api == 2 ? 2 : $user->api_status]);
                // =======================
                // Personal Package (FREE)  DONE
                // ========================
            }

            $credentials = ['email' => $twitterUser->email, 'password' => $user->original_password];
            if (!$token = auth()->attempt($credentials)) {
                return response()->json(['status' => 0, 'message' => TranslationHandler::getTranslation($request->lang, 'invalid_credentials')], 200, ['Content-Type' => 'application/json']);
            }

            // $user = User::where(['email' => $request->input('email')])->first();
            // $is_user_active = true;
            // $resend_email_flag = false;
            // $message = '';

            // switch ($user->status) {
            //     case 0:
            //         $message = $lang['alert_messages']['account_disabled_contact_admin'];
            //         $is_user_active = false;
            //         break;
            //     case 2:
            //         $message = $lang['error_message']['verify_account'];
            //         $is_user_active = false;
            //         $resend_email_flag = true;
            //         break;
            //     case 3:
            //         $message = $lang['alert_messages']['account_deleted_contact_admin'];
            //         $is_user_active = false;
            //         break;
            // }

            // if ($is_user_active == false) {
            //     auth()->logout();
            //     return response()->json(['resend_email_flag' => $resend_email_flag, 'email' => $request->email, 'status' => 0, 'user_status' => $user->status, 'message' => $message], 200, ['Content-Type' => 'application/json']);
            // }

            $user->update([
                'last_active_at' => now(),
                //'ip_address' => $_SERVER['REMOTE_ADDR']
            ]);

            return response()->json([
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL(),
                'data' => $user->makeHidden(['original_password']),
                'status' => 1,
                'message' => TranslationHandler::getTranslation($request->lang, 'account_created'),
            ], 200, ['Content-Type' => 'application/json']);
        }
    }

    /**
     * Send request to get the token and secret
     * @return object
     */
    private function access_token($oauth_token, $oauth_verifier)
    {

        $config = config('services')['twitter'];
        $connection = new TwitterOAuth($config['client_id'], $config['client_secret']);

        $tokens = $connection->oauth("oauth/access_token", ["oauth_verifier" => $oauth_verifier, "oauth_token" => $oauth_token]);

        return (object)$tokens;
    }

    public function updateLanguage(Request $request)
    {
        $user = auth()->user();
        $user->update(['language' => $request->lang]);
    }
}
