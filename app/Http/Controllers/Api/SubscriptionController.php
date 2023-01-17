<?php

namespace App\Http\Controllers\Api;

use App\CustomClasses\PaymentHandler;
use App\CustomClasses\TranslationHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Resources\PayAsYouGoPaymentResource;
use Illuminate\Support\Facades\Validator;
use Srmklive\PayPal\Services\ExpressCheckout;
use App\Models\User;
use App\Models\Admin\PaymentGatewaySetting;
use App\Models\Admin\EmailTemplate;
use App\Http\Resources\PaymentResource;
use App\Jobs\SendMail;
use App\Models\Admin\Package;
use App\Models\Admin\Payment;
use App\Models\Admin\Timezone;
use App\Models\PayAsYouGoPayments;
use Carbon\Carbon;
use PDF;
use Config;
use Hashids;

class SubscriptionController extends Controller
{
    /**
     * Create a new SubscriptionController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['downloadPayAsYouGoInvoice', 'downloadPaymentInvoice', 'molliePayment', 'mollieSubscriptionWebhook', 'molliePayAsYouGoPayment']]);
    }

    /**
     * Paypal Express Checkout
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function paymentCheckout(Request $request)
    {
        $return_url = $cancel_url = '';
        session(['lang' => $request->lang]);

        // \Log::info('Session Language Payment Checkout', array(
        //     'response' => session('lang')
        // ));

        // if($request->payment_method == Config::get('constants.payment_methods')['Paypal'])
        // {
        //     $return_url = '/paypal/success';
        //     $cancel_url = '/paypal/cancel';
        // }

        return PaymentHandler::checkout($request, $request->package_id, $request->type, auth()->user()->id, $request->payment_method, $return_url, $cancel_url);
    }

    /**
     * Mollie payment gateway Verify user payment status after doing payment
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function molliePaymentVerify(Request $request)
    {
        $lang = $request->has('lang') && $request->lang != 'en' ? $request->lang : 'en';
        // $lang_file = public_path('i18n/translations/' . $request->lang . '.json');
        // $lang_arr = json_decode(file_get_contents($lang_file), true);

        $order_id = Hashids::decode($request->order_id)[0];

        $user_package = auth()->user()->subscription->package_id;
        if ($user_package != 9)
            $payment = Payment::where('subscription_id', $order_id)->first();
        else
            $payment = PayAsYouGoPayments::where('package_subscription_id', $order_id)->first();

        if ($payment) {
            $user = $payment->user;

            $message = TranslationHandler::getTranslation($request->lang, 'mollie_verify_order_success');

            if ($user->is_secondary_accounts_created == 1 && !empty($payment->voucher)) {
                $message = $message . 'lite_account_on_other_platforms_check_email';
            }

            return response()->json([
                'status' => $payment->status,
                'message' => $message
            ]);
        } else //No payment exists or payment is cancelled from mollie.
        {
            $message = 'unable_to_update_package';

            return response()->json([
                'status' => 2,
                'message' => $message
            ]);
        }
    }

    /**
     * Mollie payment gateway upadate payment status and user package / Callback
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function molliePayment(Request $request)
    {
        $paymentGatewaySettings = PaymentGatewaySetting::first();
        if ($paymentGatewaySettings->mollie_mode == 'sandbox') {
            $mollie_api_key = $paymentGatewaySettings->mollie_sandbox_api_key;
        } else if ($paymentGatewaySettings->mollie_mode == 'live') {
            $mollie_api_key = $paymentGatewaySettings->mollie_live_api_key;
        }

        $mollie = new \Mollie\Api\MollieApiClient();
        $mollie->setApiKey($mollie_api_key);
        $payment = $mollie->payments->get($request->id);
        $paymentArr = (array) $payment;

        // \Log::info('Mollie payment response', array(
        //     'response' => (array) $payment,
        //     'isPaid' => $payment->isPaid(),
        //     'hasRefunds' => $payment->hasRefunds(),
        //     'hasChargebacks' => $payment->hasChargebacks()
        // ));

        $status = 3;
        session(['lang' => $payment->metadata->language]);
        $lang = $payment->metadata->language;

        $paymentModel = Payment::where(['txn_id' => $request->id])->first();
        $new_subscription = $paymentModel->subscription;

        /*
        * Start call to product immunity's voucher redeem API for getting reseller info
        */
        if (!empty($paymentModel->voucher)) {
            $data = array(
                "voucher" => $paymentModel->voucher,
                "platform" => "EMK",
                "apply_voucher" => 0,
                "lang"  => $lang
            );

            $product_immunity_url = Config::get('constants.product_immunity_url') . "/api/vouchers/redeem?lang=" . $lang;
            $response = checkVoucherValidity($product_immunity_url, $data);

            // Start- Voucher Integration with Odoo

            if (empty($response) || !array_key_exists('status', $response) || !$response['status']) {
                $odoo_timmunity_url = Config::get('constants.odoo_timmunity_url') . "/api/redeem-voucher?lang=" . $lang;
                $response = checkVoucherValidity($odoo_timmunity_url, $data);
            }

            // End- Voucher Integration with Odoo

            if (!empty($response) && array_key_exists('status', $response) && $response['status'] && $response['status'] == 1) {
                $reseller = $response['data']['reseller']['name'] . ' (' . $response['data']['reseller']['email'] . ')';

                $paymentModel->update([
                    'reseller' => $reseller
                ]);
            }
        }
        /*
        * End call to product immunity's voucher redeem API for getting reseller info
        */

        if ($payment->isPaid() && !$payment->hasRefunds() && !$payment->hasChargebacks() && empty($paymentModel->profile_id) && empty($paymentModel->profile_data)) {
            /*
            * The payment is paid and isn't refunded or charged back. At this point you'd  probably want to start the process of delivering the product to the customer.
            */
            $status = 1;

            $package = Package::find($new_subscription->package_id);
            $packageLinkedFeatures = $package->linkedFeatures->pluck('count', 'feature_id')->toArray();

            $user = User::find($new_subscription->user_id);
            $current_subscription = $user->subscription;

            $customer = $mollie->customers->get($user->mollie_customer_id);

            /*
            *Cancel Previous Subscription
            */

            if (!empty($current_subscription->payment) && $user->package_recurring_flag) {
                try {
                    $response = $customer->cancelSubscription($current_subscription->payment->profile_id);

                    // \Log::info('Mollie Cancel Previous Subscription Response Success', array(
                    //     'response' => (array) $response
                    // ));
                } catch (\Mollie\Api\Exceptions\ApiException $e) {
                    // \Log::info('Mollie Cancel Previous Subscription Response Fail', array(
                    //     'response' => $e->getMessage()
                    // ));
                }
                $current_subscription->update(['is_active' => 0]);
            }

            /*
            *Create New Subscription
            */

            $total_amount = $paymentModel->total_amount + $paymentModel->discount_amount;

            if ($new_subscription->package_id == 8) {
                $repetition = $new_subscription->repetition;
                $response = $customer->createSubscription([
                    "amount" => [
                        "currency" => strtoupper(Config::get('constants.currency')['code']),
                        "value" => (string) number_format($total_amount, 2)
                    ],
                    "interval" =>  "1 day",
                    "startDate" => Carbon::now('UTC')->addDays(1)->format('Y-m-d'),
                    "description" => $paymentModel->item . ' ' . uniqid(),
                    "webhookUrl" => url("/api/mollie/subscriptions/webhook"),
                ]);
            } else {
                $interval = "1 month";
                $repetition = $new_subscription->repetition;
                if ($new_subscription->type == 1) {
                    if ($repetition != 1) {
                        $interval = $new_subscription->repetition . " months";
                    }
                } else {
                    $interval = $new_subscription->repetition * 12 . " months";
                }

                $response = $customer->createSubscription([
                    "amount" => [
                        "currency" =>  strtoupper(Config::get('constants.currency')['code']),
                        "value" => (string) number_format($total_amount, 2)
                    ],
                    "interval" => $interval,
                    "startDate" => ($new_subscription->type == 1) ? Carbon::now('UTC')->addMonth($repetition)->format('Y-m-d') : Carbon::now('UTC')->addYear($repetition)->format('Y-m-d'),
                    "description" => $paymentModel->item . ' ' . uniqid(),
                    "webhookUrl" => url("/api/mollie/subscriptions/webhook"),
                ]);
            }

            $response = (array) $response;

            // \Log::info('Mollie Subscription Response', array(
            //     'response' => $response
            // ));

            /*
            ** Update User
            */

            $user->update([
                'package_id'                => $new_subscription->package_id,
                'prev_package_subscription_id' => $current_subscription->id,
                'package_subscription_id'   => $new_subscription->id,
                'payment_id'                => $paymentModel->id,
                'payment_method'            => 'mollie',
                'package_recurring_flag'    => 1,
                'on_trial'                  => 0,
                'on_hold_package_id'        => NULL,
                'is_expired'                => 0,
                // 'total_allocated_space'     => $packageLinkedFeatures[1],
                // 'remaining_allocated_space' => $packageLinkedFeatures[1] * 1073741824, // Multiply With 1 GB
                // 'max_file_size'             => $packageLinkedFeatures[2],
                'switch_to_paid_package'    => 1,
                'package_updated_by_admin'  => 0,
                'unpaid_package_email_by_admin' => 0,
                'expired_package_disclaimer' => 0,
                'last_quota_revised'        => $new_subscription->type == 2 || $repetition != 1  ? date("Y-m-d H:i:s") : NULL
            ]);

            Payment::where('txn_id', $request->id)->update([
                'profile_id' => $response['id'],
                'profile_data' => $response
            ]);

            /*
            ** Start Send call to product immunity to validate voucher
            */

            if (!empty($paymentModel->voucher)) {
                $data = array(
                    "voucher" => $paymentModel->voucher,
                    "platform" => "EMK",
                    "apply_voucher" => 0,
                    "lang"  => $lang
                );

                $product_immunity_url = Config::get('constants.product_immunity_url') . "/api/vouchers/redeem?lang=" . $lang;
                $response = checkVoucherValidity($product_immunity_url, $data);

                // Start- Voucher Integration with Odoo

                if (empty($response) || !array_key_exists('status', $response) || !$response['status']) {
                    $odoo_timmunity_url = Config::get('constants.odoo_timmunity_url') . "/api/redeem-voucher?lang=" . $lang;
                    $response = checkVoucherValidity($odoo_timmunity_url, $data);
                }

                // End- Voucher Integration with Odoo

                if (!empty($response) && array_key_exists('status', $response) && $response['status'] && $response['status'] == 1) {
                    $reseller = $response['data']['reseller']['name'] . ' (' . $response['data']['reseller']['email'] . ')';

                    /*
                    ** Create accounts on secondary projects
                    */

                    $secondary_project_user_ids = [];
                    $secondary_projects = [];

                    if (!empty($response['data']) && array_key_exists('secondary_projects', $response['data']) && !empty($response['data']['secondary_projects'])) {
                        foreach ($response['data']['secondary_projects'] as $key => $value) {
                            if ($value == "NED") {
                                $data = array(
                                    'primaryEmail' => $user->email,
                                    'password' => $user->original_password,
                                    'username' => $user->username,
                                    'firstName' => $user->name,
                                    'lastName' => '',
                                    'subscribeToMinPricePlan' => true,
                                    'voucher' => $paymentModel->voucher,
                                    'reseller' => $reseller,
                                    'package_duration_in_months' => $user->subscription->type == 1 ? 1 : 12
                                );

                                $curl = initCurl(Config::get('constants.ned_link_url') . '/accounts/member/register', $data);

                                $response_ned = curl_exec($curl);
                                curl_close($curl);

                                $response_ned_arr = json_decode($response_ned, true);

                                if (!empty($response_ned_arr) && array_key_exists('status', $response_ned_arr) && $response_ned_arr['status']) {
                                    $secondary_project_user_ids[] = $response_ned_arr['data']['_id'];
                                    $secondary_projects[] = 'NED.link';
                                }

                                // \Log::info('Ned Link Account Sign Up', array(
                                //     'response' => $response_ned
                                // ));
                            } else if ($value == "MOV") {
                                $data = array(
                                    'name' => $user->name,
                                    'email' => $user->email,
                                    'password' => $user->original_password,
                                    'password_confirmation' => $user->original_password,
                                    'timezone' => $user->timezone,
                                    'country_id' => $user->country_id,
                                    'subscribeToMinPricePlan' => true,
                                    'platform' => 14,
                                    'voucher' => $paymentModel->voucher,
                                    'reseller' => $reseller,
                                    'package_duration_in_months' => $user->subscription->type == 1 ? 1 : 12
                                );

                                $curl = initCurl(Config::get('constants.move_immunity_url') . '/api/auth/register', $data);

                                $response = curl_exec($curl);
                                curl_close($curl);

                                $response_arr = json_decode($response, true);

                                if (!empty($response_arr) && array_key_exists('status', $response_arr) && $response_arr['status']) {
                                    $secondary_project_user_ids[] = $response_arr['data']['id'];
                                    $secondary_projects[] = 'Move Immunity';
                                }

                                // \Log::info('Move Immunity Account Sign Up', array(
                                //     'response' => $response
                                // ));
                            } else if ($value == "QRC") {
                                $data = array(
                                    'name' => $user->name,
                                    'email' => $user->email,
                                    'password' => $user->original_password,
                                    'password_confirmation' => $user->original_password,
                                    'timezone' => $user->timezone,
                                    'country_id' => $user->country_id,
                                    'subscribeToMinPricePlan' => true,
                                    'platform' => 14,
                                    'voucher' => $paymentModel->voucher,
                                    'reseller' => $reseller,
                                    'package_duration_in_months' => $user->subscription->type == 1 ? 1 : 12
                                );

                                $curl = initCurl(Config::get('constants.qr_code_url') . '/api/auth/register', $data);

                                $response = curl_exec($curl);
                                curl_close($curl);

                                $response_arr = json_decode($response, true);

                                if (!empty($response_arr) && array_key_exists('status', $response_arr) && $response_arr['status']) {
                                    $secondary_project_user_ids[] = $response_arr['data']['id'];
                                    $secondary_projects[] = 'QR Code';
                                }

                                // \Log::info('QR Code Account Sign Up', array(
                                //     'response' => $response
                                // ));
                            } else if ($value == "AKQ") {
                                $data = array(
                                    'action' => 'signup',
                                    'email_local' => $user->email,
                                    'pass1' => $user->original_password,
                                    'pass2' => $user->original_password,
                                    'timezone' => $user->timezone,
                                    'country_id' => $user->country_id,
                                    'firstname' => $user->name,
                                    'surname' => '',
                                    'subscribeToMinPricePlan' => true,
                                    'voucher' => $paymentModel->voucher,
                                    'reseller' => $reseller,
                                    'package_duration_in_months' => $user->subscription->type == 1 ? 1 : 12
                                );

                                $curl = initCurl(Config::get('constants.aikq_url') . '/api/index.php', $data);

                                $response = curl_exec($curl);
                                curl_close($curl);

                                $response_arr = json_decode($response, true);

                                if (!empty($response_arr) && array_key_exists('status', $response_arr) && $response_arr['status']) {
                                    $secondary_project_user_ids[] = $response_arr['data']['id'];
                                    $secondary_projects[] = 'aikQ';
                                }

                                // \Log::info('aikQ Account Sign Up', array(
                                //     'response' => $response
                                // ));
                            } else if ($value == "INB") {
                                $data = array(
                                    'action' => 'signup',
                                    'email_local' => $user->email,
                                    'pass1' => $user->original_password,
                                    'pass2' => $user->original_password,
                                    'timezone' => $user->timezone,
                                    'country_id' => $user->country_id,
                                    'firstname' => $user->name,
                                    'surname' => '',
                                    'subscribeToMinPricePlan' => true,
                                    'voucher' => $paymentModel->voucher,
                                    'reseller' => $reseller,
                                    'package_duration_in_months' => $user->subscription->type == 1 ? 1 : 12
                                );

                                $curl = initCurl(Config::get('constants.inbox_de_url') . '/api/index.php', $data);

                                $response = curl_exec($curl);
                                curl_close($curl);

                                $response_arr = json_decode($response, true);

                                if (!empty($response_arr) && array_key_exists('status', $response_arr) && $response_arr['status']) {
                                    $secondary_project_user_ids[] = $response_arr['data']['id'];
                                    $secondary_projects[] = 'Inbox';
                                }

                                // \Log::info('Inbox.de Account Sign Up', array(
                                //     'response' => $response
                                // ));
                            } else if ($value == "OVM") {
                                $data = array(
                                    'action' => 'signup',
                                    'email_local' => $user->email,
                                    'pass1' => $user->original_password,
                                    'pass2' => $user->original_password,
                                    'timezone' => $user->timezone,
                                    'country_id' => $user->country_id,
                                    'firstname' => $user->name,
                                    'surname' => '',
                                    'subscribeToMinPricePlan' => true,
                                    'voucher' => $paymentModel->voucher,
                                    'reseller' => $reseller,
                                    'package_duration_in_months' => $user->subscription->type == 1 ? 1 : 12
                                );

                                $curl = initCurl(Config::get('constants.overmail_url') . '/api/index.php', $data);

                                $response = curl_exec($curl);
                                curl_close($curl);

                                $response_arr = json_decode($response, true);

                                if (!empty($response_arr) && array_key_exists('status', $response_arr) && $response_arr['status']) {
                                    $secondary_project_user_ids[] = $response_arr['data']['id'];
                                    $secondary_projects[] = 'Overmail';
                                }

                                // \Log::info('Overmail Account Sign Up', array(
                                //     'response' => $response
                                // ));
                            } else if ($value == "MAI") {
                                $data = array(
                                    'action' => 'signup',
                                    'email_local' => $user->email,
                                    'pass1' => $user->original_password,
                                    'pass2' => $user->original_password,
                                    'timezone' => $user->timezone,
                                    'country_id' => $user->country_id,
                                    'firstname' => $user->name,
                                    'surname' => '',
                                    'subscribeToMinPricePlan' => true,
                                    'voucher' => $paymentModel->voucher,
                                    'reseller' => $reseller,
                                    'package_duration_in_months' => $user->subscription->type == 1 ? 1 : 12
                                );

                                $curl = initCurl(Config::get('constants.maili_de_url') . '/api/index.php', $data);

                                $response = curl_exec($curl);
                                curl_close($curl);

                                $response_arr = json_decode($response, true);

                                if (!empty($response_arr) && array_key_exists('status', $response_arr) && $response_arr['status']) {
                                    $secondary_project_user_ids[] = $response_arr['data']['id'];
                                    $secondary_projects[] = 'Maili';
                                }

                                // \Log::info('Maili.de Account Sign Up', array(
                                //     'response' => $response
                                // ));
                            } else if ($value == "TRF") {
                                $data = array(
                                    'name' => $user->name,
                                    'email' => $user->email,
                                    'password' => $user->original_password,
                                    'password_confirmation' => $user->original_password,
                                    'timezone' => $user->timezone,
                                    'country_id' => $user->country_id,
                                    'subscribeToMinPricePlan' => true,
                                    'platform' => 14,
                                    'voucher' => $request->voucher,
                                    'reseller' => $reseller,
                                    'package_duration_in_months' => $user->subscription->type == 1 ? 1 : 12
                                );

                                $curl = initCurl(Config::get('constants.transfer_immunity_url') . '/api/auth/register', $data);

                                $response = curl_exec($curl);
                                curl_close($curl);

                                $response_arr = json_decode($response, true);

                                if (!empty($response_arr) && array_key_exists('status', $response_arr) && $response_arr['status']) {
                                    $secondary_project_user_ids[] = $response_arr['data']['id'];
                                    $secondary_projects[] = 'Transfer Immunity';
                                }

                                // \Log::info('Transfer Immunity Account Sign Up', array(
                                //     'response' => $response
                                // ));
                            }
                        }
                    }

                    /*
                    ** Start Send call to product immunity to redeem voucher
                    */

                    $data = array(
                        "voucher" => $paymentModel->voucher,
                        "platform" => "EMK",
                        "user_data" => array(
                            'name' => $user->name,
                            'email' => $user->email
                        ),
                        "main_project_user_id" => $user->id,
                        "secondary_project_user_ids" => implode(',', $secondary_project_user_ids),
                        "apply_voucher" => 1,
                        "lang"  => $lang
                    );

                    $product_immunity_url = Config::get('constants.product_immunity_url') . "/api/vouchers/redeem?lang=" . $lang;
                    $responseArr = checkVoucherValidity($product_immunity_url, $data);

                    // Start- Voucher Integration with Odoo

                    if (!array_key_exists('status', $responseArr) || !$responseArr['status']) {
                        $odoo_timmunity_url = Config::get('constants.odoo_timmunity_url') . "/api/redeem-voucher?lang=" . $lang;
                        $responseArr = checkVoucherValidity($odoo_timmunity_url, $data);
                    }

                    // End- Voucher Integration with Odoo

                    if (array_key_exists('status', $responseArr) && $responseArr['status'] && $responseArr['status'] == 1) {
                        $user->update([
                            'voucher' => '',
                            'is_voucher_redeemed' => 1
                        ]);

                        $reseller = $responseArr['data']['reseller']['name'] . ' (' . $responseArr['data']['reseller']['email'] . ')';

                        $paymentModel->update([
                            'reseller' => $reseller
                        ]);
                    }

                    /*
                    ** End Send call to product immunity to redeem voucher
                    */

                    if (!empty($secondary_projects)) {
                        /*
                        ** Start send email to user
                        */

                        $name = $user->name;
                        $email = $user->email;

                        $email_template = EmailTemplate::where('type', 'lite_account_created_on_other_platforms')->first();
                        $email_template = transformEmailTemplateModel($email_template, $lang);

                        $subject = $email_template['subject'];
                        $content = $email_template['content'];

                        $search = array("{{name}}", "{{app_name}}", "{{platforms}}");
                        $replace = array($name, settingValue('site_title'), implode(',', $secondary_projects));
                        $content  = str_replace($search, $replace, $content);

                        SendMail::dispatch($email, $subject, $content, '', '', $lang);

                        /*
                        ** End send email to user
                        */

                        $user->update([
                            'is_secondary_accounts_created' => 1
                        ]);
                    } else {
                        $user->update([
                            'is_secondary_accounts_created' => 0
                        ]);
                    }
                }
            }

            /*
            ** End Send call to product immunity to validate voucher
            */
            // \Log::info("Changes package plus send invoice to user " . $email);

            // PaymentHandler::packageSwitchNotification($paymentModel, $lang);
            // PaymentHandler::sendInvoiceEmail($paymentModel);
        } elseif ($payment->isOpen()) {
            $status = 2;
        } elseif ($payment->isPending()) {
            $status = 3;
        } elseif ($payment->isFailed()) {
            $status = 4;
        } elseif ($payment->isExpired()) {
            $status = 5;
        } elseif ($payment->isCanceled()) {
            $status = 6;
            PaymentHandler::deleteRecords($paymentModel);
        } elseif ($payment->hasRefunds()) {
            /*
             * The payment has been (partially) refunded.
             * The status of the payment is still "paid"
            */
            $status = 7;
        } elseif ($payment->hasChargebacks()) {
            /*
            *The payment has been (partially) charged back. The status of the payment is still paid
            */
            $status = 8;
        }

        Payment::where(['txn_id' => $request->id])->update([
            'status' => $status,
            'data' => (array) $payment
        ]);

        $payy = Payment::where(['txn_id' => $request->id])->first();

        if ($payment->isPaid() && !$payment->hasRefunds() && !$payment->hasChargebacks() && empty($paymentModel->profile_id) && empty($paymentModel->profile_data)) {
            PaymentHandler::packageSwitchNotification($payy, $lang);
            PaymentHandler::sendInvoiceEmail($payy);
        }
    }

    /**
     * Mollie payment gateway upadate payment status and user pay as you go package / Callback
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function molliePayAsYouGoPayment(Request $request)
    {
        $notCleared = PayAsYouGoPayments::where('txn_id', $request->id)->where('payment_method', '!=', 'admin')->first();
        if ($notCleared) {
            $paymentGatewaySettings = PaymentGatewaySetting::first();
            if ($paymentGatewaySettings->mollie_mode == 'sandbox') {
                $mollie_api_key = $paymentGatewaySettings->mollie_sandbox_api_key;
            } else if ($paymentGatewaySettings->mollie_mode == 'live') {
                $mollie_api_key = $paymentGatewaySettings->mollie_live_api_key;
            }

            $mollie = new \Mollie\Api\MollieApiClient();
            $mollie->setApiKey($mollie_api_key);
            $payment = $mollie->payments->get($request->id);
            $paymentArr = (array) $payment;

            $status = 3;
            session(['lang' => $payment->metadata->language]);
            $lang = $payment->metadata->language;

            $paymentModel = PayAsYouGoPayments::where(['txn_id' => $request->id])->first();
            $subscription = $paymentModel->subscription;

            if ($payment->isPaid() && !$payment->hasRefunds() && !$payment->hasChargebacks() && empty($paymentModel->profile_id) && empty($paymentModel->profile_data)) {
                /*
            * The payment is paid and isn't refunded or charged back. At this point you'd  probably want to start the process of delivering the product to the customer.
            */
                $status = 1;

                $package = Package::find($subscription->package_id);
                $user = User::find($subscription->user_id);

                /*
            ** Update User
            */

                $user->update([
                    'payment_id'                => $paymentModel->id,
                    'payment_method'            => 'mollie',
                    'last_quota_revised'        => date("Y-m-d H:i:s")
                ]);

                if ($payment->isPaid()) {
                    $paygpayment = PayAsYouGoPayments::where('txn_id', $request->id)->first();

                    Payment::create([
                        'user_id'                   =>  $user->id,
                        'subscription_id'           =>  $subscription->id,
                        'item'                      =>  $package->title,
                        'payment_method'            =>  'mollie',
                        'amount'                    =>  $paygpayment->amount,
                        'vat_percentage'            =>  $paygpayment->vat_percentage,
                        'vat_amount'                =>  $paygpayment->vat_amount,
                        'voucher'                   =>  '',
                        'reseller'                  =>  NULL,
                        'discount_percentage'       =>  0,
                        'discount_amount'           =>  0,
                        'total_amount'              =>  $paygpayment->total_amount_charged,
                        'payload'                   =>  $paygpayment->payload,
                        'lang'                      =>  !empty(session('lang')) && session('lang') != 'en' ? session('lang') : 'en',
                        'timestamp'                 =>  Carbon::now('UTC')->timestamp,
                        'status'                    =>  1,
                        'data'                      =>  $paygpayment->data,
                    ]);
                }

                // PaymentHandler::sendPayAsYouGoInvoiceEmail($paymentModel);
            } elseif ($payment->isOpen()) {
                $status = 2;
            } elseif ($payment->isPending()) {
                $status = 3;
            } elseif ($payment->isFailed()) {
                $status = 4;
            } elseif ($payment->isExpired()) {
                $status = 5;
            } elseif ($payment->isCanceled()) {
                $status = 6;
                // PaymentHandler::deleteRecords($paymentModel);
            } elseif ($payment->hasRefunds()) {
                /*
             * The payment has been (partially) refunded.
             * The status of the payment is still "paid"
            */
                $status = 7;
            } elseif ($payment->hasChargebacks()) {
                /*
            *The payment has been (partially) charged back. The status of the payment is still paid
            */
                $status = 8;
            }

            PayAsYouGoPayments::where(['txn_id' => $request->id])->update([
                'status' => $status,
                'data' => (array) $payment
            ]);

            $payy = PayAsYouGoPayments::where(['txn_id' => $request->id])->first();

            if ($payment->isPaid() && !$payment->hasRefunds() && !$payment->hasChargebacks() && empty($paymentModel->profile_id) && empty($paymentModel->profile_data)) {
                PaymentHandler::sendPayAsYouGoInvoiceEmail($payy);
            }
        }
    }

    public function mollieSubscriptionWebhook(Request $request)
    {
        // \Log::info('Mollie Subscription Webhook Response', array(
        //     'response' => $request->all()
        // ));

        if (!$request->has('id')) {
            return;
        }

        $paymentGatewaySettings = PaymentGatewaySetting::first();
        if ($paymentGatewaySettings->mollie_mode == 'sandbox') {
            $mollie_api_key = $paymentGatewaySettings->mollie_sandbox_api_key;
        } else if ($paymentGatewaySettings->mollie_mode == 'live') {
            $mollie_api_key = $paymentGatewaySettings->mollie_live_api_key;
        }

        $mollie = new \Mollie\Api\MollieApiClient();
        $mollie->setApiKey($mollie_api_key);
        $payment = $mollie->payments->get($request->id);

        // \Log::info('Mollie Subscription Payment Response', array(
        //     'response' => (array) $payment
        // ));

        if ($payment->isPaid()) {
            $paymentModel = Payment::where(['profile_id' => $payment->subscriptionId])->orderBy('id', 'DESC')->first();
            $subscription = $paymentModel->subscription;
            $user = $paymentModel->user;
            $features = json_decode($subscription->features, true);

            /*
            *Create new subscription
            */

            $newSubscription = $subscription->replicate();
            $newSubscription->start_date = Carbon::now('UTC')->timestamp;
            if ($subscription->package_id == 8) {
                $newSubscription->end_date = Carbon::now('UTC')->addDays(1)->timestamp;
            } else {
                $newSubscription->end_date = ($subscription->type == 1) ? Carbon::now('UTC')->addMonth()->timestamp : Carbon::now('UTC')->addYear()->timestamp;
            }
            $newSubscription->save();

            /*
            *Create new payment
            */

            $newPaymentModel = $paymentModel->replicate();
            $newPaymentModel->subscription_id = $newSubscription->id;
            $newPaymentModel->txn_id = $request->id;
            $newPaymentModel->data = json_encode((array) $payment);
            $newPaymentModel->timestamp = Carbon::now('UTC')->timestamp;
            $newPaymentModel->total_amount = $paymentModel->amount + $paymentModel->vat_amount;
            $newPaymentModel->reseller = null;
            $newPaymentModel->voucher = null;
            $newPaymentModel->discount_percentage = 0;
            $newPaymentModel->discount_amount = 0;
            $newPaymentModel->save();

            /*
            *Update user
            */

            $user->update([
                'prev_package_subscription_id' => $subscription->id,
                'package_subscription_id'   => $newSubscription->id,
                'payment_id'                => $newPaymentModel->id,
                'payment_method'            => 'mollie',
                'package_recurring_flag'    => 1,
                'is_expired'                => 0,
                // 'total_allocated_space'     => $features[1],
                // 'remaining_allocated_space' => $features[1] * 1073741824, // Multiply With 1 GB
                // 'max_file_size'             => $features[2],
                'package_updated_by_admin'  => 0,
                'unpaid_package_email_by_admin' => 0,
                'expired_package_disclaimer' => 0
            ]);

            PaymentHandler::sendInvoiceEmail($newPaymentModel);
        }
    }

    /**
     * Get Current Package Detail
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function getCurrentPackage(Request $request)
    {
        $user = auth()->user();
        // $package = $user->subscription_id->setAppends(['package_image', 'package_title', 'linked_features']);
        // $package = Package::where('id', $user->package_id)->first();

        return response()->json([
            'data' => [
                'package_subscription' => $user->subscription->setAppends(['package_image', 'package_title', 'linked_features']),
                'package_recurring_flag' => $user->package_recurring_flag,
                'package_title' => $user->subscription->package->title
                // 'package_title' => translation($user->subscription->package_id,2,$request->lang,'title',$user->subscription->package->title)
            ],
            'status' => 1,
            'message' => 'User active subscription'
        ]);
    }

    /**
     * Cancel Current Package
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function cancelCurrentPackage(Request $request)
    {
        $lang = $request->has('lang') && $request->lang != 'en' ? $request->lang : 'en';
        // $lang_file = public_path('i18n/translations/' . $request->lang . '.json');
        // $lang_arr = json_decode(file_get_contents($lang_file), true);

        $user = auth()->user();

        $paymentGatewaySettings = PaymentGatewaySetting::first();
        if ($paymentGatewaySettings->mollie_mode == 'sandbox') {
            $mollie_api_key = $paymentGatewaySettings->mollie_sandbox_api_key;
        } else if ($paymentGatewaySettings->mollie_mode == 'live') {
            $mollie_api_key = $paymentGatewaySettings->mollie_live_api_key;
        }

        $mollie = new \Mollie\Api\MollieApiClient();
        $mollie->setApiKey($mollie_api_key);

        $customer = $mollie->customers->get($user->mollie_customer_id);
        $subscription = $customer->cancelSubscription($user->payment->profile_id);

        // \Log::info('Mollie Cancel Subscription Response', array(
        //     'response' => (array) $subscription
        // ));

        $user->update([
            'package_recurring_flag'  => 0
        ]);

        return response()->json([
            'data' => auth()->user()->subscription->setAppends(['package_title', 'linked_features'])->makeHidden(['package']),
            'status' => 1,
            'message' => TranslationHandler::getTranslation($request->lang, 'unsubscribe_package_success')
        ]);
    }

    /**
     * Check subscription status
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function checkStatus(Request $request)
    {
        $user = auth()->user();

        $subscription = $user->subscription;
        $currentTimestamp = Carbon::now('UTC')->timestamp;

        if (!empty($subscription->end_date) && $subscription->end_date < $currentTimestamp) {
            //************************//
            // Subscribe Free Package //
            //************************//

            $user->update([
                'on_hold_package_id' =>  $subscription->package_id,
                'is_expired' => 0,
                'expired_package_disclaimer' => 1,
                'prev_package_subscription_id' => $subscription->id,
                'package_updated_by_admin' => 0,
                'unpaid_package_email_by_admin' => 0
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
                $upgrade_link = url('/packages/upgrade-package?redirect_to_upgrade_package=1');
                $contact_link = url('/contact-us');
                $subject = $email_template['subject'];
                $content = $email_template['content'];

                $search = array("{{name}}", "{{from}}", "{{to}}", "{{upgrade_link}}", "{{contact_link}}", "{{app_name}}");
                $replace = array($name, $subscription->package_title, $package->title, $upgrade_link, $contact_link, settingValue('site_title'));
                $content  = str_replace($search, $replace, $content);

                SendMail::dispatch($email, $subject, $content);
            }
            $user = User::find($user->id);
        }

        $response['is_expired'] = $user->is_expired;
        $response['expired_package_disclaimer'] = $user->expired_package_disclaimer;
        $response['user_status'] = $user->status;
        $response['package_updated_by_admin'] = $user->package_updated_by_admin;
        $response['unpaid_package_email_by_admin'] = $user->unpaid_package_email_by_admin;

        return response()->json([
            'data' => $response,
            'status' => 1,
            'message' => 'User subscription status'
        ]);
    }

    /**
     * remove the expired package disclaimer flag
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function expiredPackageDisclaimerFlag()
    {
        $user = auth()->user();
        $user->update([
            'expired_package_disclaimer' => 0
        ]);

        return response()->json([
            'status' => 1,
            'message' => 'Expired package disclaimer flag is updated.'
        ]);
    }

    /**
     * remove the package updated by admin flag
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function updatePackageByAdminFlag()
    {
        $user = auth()->user();
        $user->update([
            'package_updated_by_admin' => 0
        ]);

        return response()->json([
            'status' => 1,
            'message' => 'Update package by admin flag is updated.'
        ]);
    }

    /**
     * remove the unpaid package email by admin flag
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function unpaidPackageEmailByAdminFlag()
    {
        $user = auth()->user();
        $user->update([
            'unpaid_package_email_by_admin' => 0
        ]);

        return response()->json([
            'status' => 1,
            'message' => 'Unpaid package email by admin flag is updated.'
        ]);
    }


    /**
     * Display a listing of subscriptions history.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function payments(Request $request)
    {
        $payments = Payment::where('user_id', auth()->user()->id)->where('item', '!=', 'Pay as you go')->whereNotNull('timestamp');

        if (!empty($request->item)) {
            $payments = $payments->where('item', 'LIKE', '%' . $request->item . '%');
        }

        if (!empty($request->amount)) {
            $payments = $payments->where(function ($q) use ($request) {
                $q->where('amount', 'LIKE', '%' . $request->amount . '%')
                    ->orWhere('vat_amount', 'LIKE', '%' . $request->amount . '%')
                    ->orWhere('total_amount', 'LIKE', '%' . $request->amount . '%');
            });
        }

        // $start_date = $request->start_date . ' 00:00:00';
        // $end_date = $request->end_date . ' 23:59:59';

        // if ($request->has('start_date') && $request->has('end_date')) {
        //     $payments = $payments->whereBetween('created_at', [$start_date, $end_date]);
        // }

        $payments = $payments->orderBy('created_at', 'DESC')->paginate(10);

        return PaymentResource::collection($payments)
            ->additional([
                'message' => 'Subscriptions history',
                'status' => 1
            ]);
    }

    public function payAsYouGoPayments(Request $request)
    {
        $payments = PayAsYouGoPayments::where('user_id', auth()->user()->id)->whereNotNull('timestamp');

        if (!empty($request->item)) {
            $payments = $payments->where('item', 'LIKE', '%' . $request->item . '%');
        }

        if (!empty($request->amount)) {
            $payments = $payments->where(function ($q) use ($request) {
                $q->where('amount', 'LIKE', '%' . $request->amount . '%')
                    ->orWhere('vat_amount', 'LIKE', '%' . $request->amount . '%')
                    ->orWhere('total_amount', 'LIKE', '%' . $request->amount . '%');
            });
        }

        // $start_date = $request->start_date . ' 00:00:00';
        // $end_date = $request->end_date . ' 23:59:59';

        // if ($request->has('start_date') && $request->has('end_date')) {
        //     $payments = $payments->whereBetween('created_at', [$start_date, $end_date]);
        // }

        $payments = $payments->orderBy('created_at', 'DESC')->paginate(10);

        return PayAsYouGoPaymentResource::collection($payments)
            ->additional([
                'message' => 'Payments history',
                'status' => 1
            ]);
    }

    /**
     * Download payment invoice.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function downloadPaymentInvoice($id, Request $request)
    {
        $lang = $request->has('lang') && $request->lang != 'en' ? $request->lang : 'en';
        // $lang_file = public_path('i18n/translations/' . $lang . '.json');
        // $lang_arr = json_decode(file_get_contents($lang_file), true);

        // $lang = $target_lang && $target_lang != 'en' ? $target_lang : 'en';

        // $lang_file = public_path('i18n/translations/' . $lang . '.json');
        // $lang_arr = json_decode(file_get_contents($lang_file), true);
        // dd($lang_arr);


        $payment = Payment::find(\Hashids::decode($id)[0]);
        $data = array();
        $data = PaymentHandler::generatePaymentInvoice($payment);
        $data['lang_arr'] = $payment;
        $data['lang'] = $lang;
        $data['global_font_family'] = in_array($lang, ['ja', 'zh']) ? 'chinesefont' : 'Segoe';
        $data['payment_font_family'] = in_array($payment->lang, ['ja', 'zh']) ? 'chinesefont' : 'Segoe';

        // \Log::info('Invoice data', array(
        //     'data' => $data
        // ));
        $pdf = PDF::loadView('emails.invoice', $data);
        //return $pdf->stream();
        return $pdf->download('invoice ' . Carbon::now('UTC')->tz(Timezone::where('name', $payment->user->timezone)->first()->utc_offset)->format('Y-m-d H.i.s') . '.pdf');
    }

    /**
     * Download payment invoice.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function downloadPayAsYouGoInvoice($id, Request $request)
    {
        $lang = $request->has('lang') && $request->lang != 'en' ? $request->lang : 'en';

        $payment = PayAsYouGoPayments::find(\Hashids::decode($id)[0]);

        $data = array();
        $data = PaymentHandler::generatePayAsYouGoPaymentInvoice($payment);
        // $data['user'] = $user;
        $data['payment'] = $payment;
        $data['lang'] = $lang;
        $data['global_font_family'] = in_array($lang, ['ja', 'zh']) ? 'chinesefont' : 'Segoe';
        $data['payment_font_family'] = in_array($payment->lang, ['ja', 'zh']) ? 'chinesefont' : 'Segoe';

        $pdf = PDF::loadView('emails.pay_as_you_go_invoice', $data);
        // $pdf = PDF::loadView('emails.invoice', $data);
        //return $pdf->stream();
        return $pdf->download('invoice ' . Carbon::now('UTC')->tz(Timezone::where('name', $payment->user->timezone)->first()->utc_offset)->format('Y-m-d H.i.s') . '.pdf');
    }

    public function updatetransmissionFeatures(Request $request)
    {
        $lang = $request->has('lang') && $request->lang != 'en' ? $request->lang : 'en';
        // $lang_file = public_path('i18n/translations/' . $lang . '.json');
        // $lang_arr = json_decode(file_get_contents($lang_file), true);

        $user = auth()->user();
        $packageFeatures = json_decode($user->subscription->features, true);
        $packageFeatures[4] = $request->transfer_expire_days;
        $packageFeatures[5] = $request->file_delete_days;

        $user->subscription->update([
            'features' => json_encode($packageFeatures)
        ]);

        return response()->json([
            'status' => 1,
            'message' => 'subscription_update_transmission_features_success'
        ]);
    }

    public function pendingPayments()
    {
        // check and create pending pay as you go payments before package switching
        $payment = NULL;
        $user = auth()->user();

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

                // $payment_success = false;
                // $response_array = array();
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

                //     $all_upaid = PayAsYouGoPayments::where('user_id', $user->id)->where('status', '!=', 1)->get();
                // foreach ($all_upaid as $unpaid) {
                //     $unpaid->update(['status' => 5]);
                // }

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
                    // "webhookUrl"  => url("/api/rfdf/callback"),
                    "metadata" => [
                        "order_id" => $user->subscription->id,
                        // "language" => session('lang')
                        "language" => 'en'
                    ],
                ];

                // file_put_contents('mollie-request.txt', json_encode($payRequest));
                $response = $mollie->payments->create($payRequest);
                // file_put_contents('mollie-response.txt', json_encode($response));


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

        $payment = PayAsYouGoPayments::where('user_id', auth()->user()->id)->where('status', '!=', 1)->count();
        if ($payment) {
            return response()->json([
                'status' => 1,
                'message' => 'has pending payments'
            ]);
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'has no pending payments'
            ]);
        }
    }

    public function regeneratePayment($id)
    {
        $payment = PayAsYouGoPayments::find(\Hashids::decode($id)[0]);
        $user = auth()->user();
        if ($payment) {

            // start
            $total_amount_charged = $payment->total_amount_charged;

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

            $total_amount_charged = number_format((float)$total_amount_charged, 2, '.', '');

            // /**Initiate payment*/

            $payRequest = [
                "amount" => [
                    "currency" =>  strtoupper(Config::get('constants.currency')['code']),
                    "value" => $total_amount_charged // You must send the correct number of decimals, thus we enforce the use of strings
                ],
                'customerId'   => $customer->id,
                'sequenceType' => 'first',
                "description" => $payment->payload,
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
            // end

            return response()->json([
                'status' => 1,
                'message' => 'payment regenerated'
            ]);
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'payment not regenerated'
            ]);
        }
    }
}
