<?php

namespace App\Http\Controllers;

use App\CustomClasses\TranslationHandler;
use App\Http\Resources\AboutUsContentResource;
use App\Http\Resources\FaqResource;
use App\Http\Resources\FeatureResource;
use App\Http\Resources\HomeContentResource;
use App\Http\Resources\ServiceResource;
use App\Http\Resources\TestimonialResource;
use App\Jobs\SendMail;
use App\Models\Admin\AboutUsContent;
use App\Models\Admin\AboutUsTestimonial;
use App\Models\Admin\ContactUsQuery;
use App\Models\Admin\EmailTemplate;
use App\Models\Admin\Faq;
use App\Models\Admin\Feature;
use App\Models\Admin\HomeContent;
use App\Models\Admin\Package;
use App\Models\Admin\PackageSubscription;
use App\Models\Admin\Payment;
use App\Models\Admin\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Hashids;
use App\Models\Admin\PaymentGatewaySetting;
use App\Models\Admin\Timezone;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Hash;

class VisitingPagesController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return redirect('/');
    }

    public function getHomeSections(Request $request)
    {
        // $contents = HomeContent::where('status', 1)->get();
        // return $contents;
        return HomeContentResource::collection(HomeContent::where('status', 1)->orderBy('created_at', 'ASC')->get());
        //     ->additional([
        //         'message' => 'Home Contents Listing',
        //         'status'  => 1
        //     ]);
    }

    public function getHomeServices(Request $request)
    {
        return ServiceResource::collection(Service::where('status', 1)->orderBy('created_at', 'ASC')->get());
        // $services = Service::where('status', 1)->get();
        // return $services;
    }

    public function getHomeFaqs()
    {
        return FaqResource::collection(Faq::where('status', 1)->orderBy('created_at', 'ASC')->get());
        // $faq = Faq::where('status', 1)->get();
        // return $faq;
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

    public function contactUs(Request $request)
    {
        $messages = [
            'name.required' => TranslationHandler::getTranslation($request->lang, 'required'),
            'email.required' => TranslationHandler::getTranslation($request->lang, 'required'),
            'email.max' => TranslationHandler::getTranslation($request->lang, 'max_65'),
            'email.regex' => TranslationHandler::getTranslation($request->lang, 'valid_email'),
            'email.unique' => TranslationHandler::getTranslation($request->lang, 'email_taken'),
            'subject.required' => TranslationHandler::getTranslation($request->lang, 'required'),
            'subject.max' => TranslationHandler::getTranslation($request->lang, 'max_30'),
            'message.required' => TranslationHandler::getTranslation($request->lang, 'required'),
            'message.required' => TranslationHandler::getTranslation($request->lang, 'max_250'),
            'captcha_token.required' => TranslationHandler::getTranslation($request->lang, 'required'),
        ];

        $data = $request->validate([
            'name' => ['required', 'string', 'max:65'],
            'email' => ['required', 'regex:/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/', 'max:65'],
            'subject' => ['required', 'max:30'],
            'message' => ['required', 'max:250'],
            'captcha_token' => 'required'
        ], $messages);

        $createGoogleUrl = 'https://www.google.com/recaptcha/api/siteverify?secret=' . "6LfOUwoeAAAAAI4pQQltHnfDrjesTCXKkTN-V8Zl" . '&response=' . $request->captcha_token;
        $verifyRecaptcha = $this->curlRequest($createGoogleUrl);
        $decodeGoogleResponse = json_decode($verifyRecaptcha, true);
        if ($decodeGoogleResponse['success'] == 1) {

            $contact_us_query = ContactUsQuery::create($data);

            //*****
            //***** Send Email To User
            //*****

            $name = $request->name;
            $email = $request->email;

            $email_template = EmailTemplate::where('type', 'contact_us_inquiry_received')->first();
            $email_template = transformEmailTemplateModel($email_template, $request->lang);

            $subject = $email_template['subject'];
            $content = $email_template['content'];

            $search = array("{{name}}", "{{app_name}}");
            $replace = array($name, settingValue('site_title'));
            $content  = str_replace($search, $replace, $content);

            SendMail::dispatch($email, $subject, $content);

            //*****
            //***** Send Email To Admin
            //*****

            $name = $request->name;
            $email = settingValue('contact_email');

            $email_template = EmailTemplate::where('type', 'contact_us_inquiry_submitted')->first();
            $subject = $email_template->subject;
            $content = $email_template->content;

            $search = array("{{name}}", "{{app_name}}", "{{date}}", "{{fullname}}", "{{email}}", "{{subject}}", "{{message}}");
            $replace = array($name, settingValue('site_title'), $contact_us_query->created_at, $contact_us_query->name, $contact_us_query->email, $contact_us_query->subject, $contact_us_query->message);
            $content  = str_replace($search, $replace, $content);

            SendMail::dispatch($email, $subject, $content);

            return response()->json([
                'status' => 1,
                'message' => 'Your Message is received!',
            ]);
        } else {
            return response()->json(['status' => 0, 'captcha' => TranslationHandler::getTranslation($request->lang, 'unable_to_verify_google')], 200, ['Content-Type' => 'application/json']);
        }
    }

    public function getContactDetails()
    {
        $office_address = settingValue("office_address");
        $contact_number = settingValue("contact_number");
        $contact_email = settingValue("contact_email");
        $url = url("/");

        return [
            'office_address' => $office_address,
            'contact_number' => $contact_number,
            'contact_email' => $contact_email,
            'website' => $url,

        ];
    }

    public function getFeaturesSections(Request $request)
    {
        return FeatureResource::collection(Feature::where('status', 1)->orderBy('created_at', 'ASC')->get());
        // $features = Feature::where('status', 1)->get();
        // return $features;
    }

    public function getAboutUsSections(Request $request)
    {
        return AboutUsContentResource::collection(AboutUsContent::where('status', 1)->orderBy('created_at', 'ASC')->get());
        // $sections = AboutUsContent::where('status', 1)->get();
        // return $sections;
    }

    public function getAboutUsTestimonials()
    {
        return TestimonialResource::collection(AboutUsTestimonial::where('status', 1)->orderBy('created_at', 'ASC')->get());
        // $sections =  AboutUsTestimonial::where('status', 1)->get();
        // return $sections;
    }

    public function getSocials()
    {
        $facebook = settingValue("facebook");
        $twitter = settingValue("twitter");
        $linkedin = settingValue("linkedin");
        $instagram = settingValue("instagram");

        return [
            'facebook' => $facebook,
            'twitter' => $twitter,
            'linkedIn' => $linkedin,
            'instagram' => $instagram,

        ];
    }

    public function testMolliePayment(Request $request)
    {

        $paymentGatewaySettings = PaymentGatewaySetting::first();
        if ($paymentGatewaySettings->mollie_mode == 'sandbox') {
            $mollie_api_key = $paymentGatewaySettings->mollie_sandbox_api_key;
        } else if ($paymentGatewaySettings->mollie_mode == 'live') {
            $mollie_api_key = $paymentGatewaySettings->mollie_live_api_key;
        }

        $mollie = new \Mollie\Api\MollieApiClient();
        $mollie->setApiKey($mollie_api_key);


        $response = $mollie->payments->create([
            "amount" => [
                "currency" =>  'EUR',
                "value" => '12.00' // You must send the correct number of decimals, thus we enforce the use of strings
            ],
            'customerId'   => 'cst_KhMzkfFGQe',
            'sequenceType' => 'first',
            "description" => "Test Transaction description",
            "redirectUrl" => url("/packages/mollie-confirmation"),
            "webhookUrl"  => url("/api/mollie/pay-as-you-go-callback"),
            "metadata" => [
                "order_id" => "313132132",
                // "language" => session('lang')
                "language" => 'en'
            ],
        ]);

        $redirectUrl = $response->getCheckoutUrl();
        echo  $redirectUrl;

        $response = (array)$response;

        print_r($response);
        exit;
    }

    public function voucherRegister(Request $request)
    {
        $request['lang'] = 'en';
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
            'country_id.required' => TranslationHandler::getTranslation($request->lang, 'required'),
            'timezone.required' => TranslationHandler::getTranslation($request->lang, 'required'),
            'platform.required' => TranslationHandler::getTranslation($request->lang, 'required'),
            'package_duration_in_months.required' => TranslationHandler::getTranslation($request->lang, 'required')
        ];

        $validation_rules = array(
            'name' => 'required|string|max:100',
            'email' => ['required', 'string', 'regex:/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/', 'max:100', 'unique:users'],
            'password' => 'required|string',
            'password_confirmation' => 'required|same:password',
            'package_duration_in_months' => 'nullable|integer|min:0'
        );

        $validator = Validator::make($request->all(), $validation_rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'errors' =>  $validator->messages()->get('*')
            ]);
            exit;
        }

        $timezone_id = '';
        $timezone = Timezone::where('name', $request->timezone)->first();
        if ($timezone) {
            $timezone_id = $timezone->id;
        }

        do {
            $api_token = Str::random(132);
            $secret_key = Str::random(20);
        } while (User::where("api_token", $api_token)->orWhere("secret_key", $secret_key)->first() instanceof User);

        $user = User::create([
            'name' => $request->input('name'),
            'username' => $request->input('email'),
            'email' => $request->input('email'),
            'timezone' => $timezone_id != '' ? $timezone_id : 181,
            'country_id' => $request->has('country_id') ? $request->country_id : 81,
            'password' => Hash::make($request->input('password')),
            'original_password' => $request->input('password'),
            'status' => 1, //active
            'secret_key' => $secret_key,
            'api_token' => $api_token,
            'platform' => $request->input('platform') ? $request->input('platform') : 1
        ]);

        if ($request->has('subscribeToMinPricePlan') && $request->subscribeToMinPricePlan == 1) {
            $package = Package::find(3); // Lite Package
            $months = 1;
            if ($request->package_duration_in_months) {
                if ($request->package_duration_in_months <= 12)
                    $months = $request->package_duration_in_months;
                else
                    $months = 12;
            }
            $end_date = Carbon::now('UTC')->addMonths($months)->timestamp;
            $on_trial = 0;
            $type = 1;

            $packageLinkedFeatures = $package->linkedFeatures->pluck('count', 'feature_id')->toArray();

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
                'is_active'     =>  1
            ]);

            $user->update([
                'package_id' => $package->id,
                'package_subscription_id' => $packageSubscription->id,
                'on_trial' => $on_trial,
                'package_recurring_flag' => 0
            ]);

            $data['subscription_desc'] = $package->title . ' Package (Monthly Subscription)';

            $payment = Payment::create([
                'user_id'                   =>  $user->id,
                'subscription_id'           =>  $packageSubscription->id,
                'item'                      =>  $package->title,
                'payment_method'            =>  'mollie',
                'amount'                    =>  0,
                'vat_percentage'            =>  0,
                'vat_amount'                =>  0,
                'voucher'                   =>  $request->voucher,
                'reseller'                  =>  $request->reseller,
                'discount_percentage'       =>  0,
                'discount_amount'           =>  0,
                'total_amount'              =>  0,
                'payload'                   =>  json_encode($data),
                'lang'                      =>  !empty(session('lang')) && session('lang') != 'en' ? session('lang') : 'en',
                'timestamp'                 =>  Carbon::now('UTC')->timestamp,
            ]);

            $user->update([
                'voucher'                   => '',
                'payment_method'            => 'mollie',
                'payment_id'                => $payment->id,
                'status'                    => 1,
            ]);
        }

        return response()->json([
            'data' => $user->makeHidden(['subscription', 'original_password', 'updated_at', 'created_at']),
            'status' => 1,
            'message' => TranslationHandler::getTranslation($request->lang, 'account_created')
        ]);
    }
}
