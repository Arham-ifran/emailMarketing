<?php

namespace App\CustomClasses;

use App\Jobs\SendMail;
use App\Models\Admin\Package;
use App\Models\Admin\PackageSubscription;
use App\Models\Admin\Payment;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Card;
use App\Models\Admin\PackageFeature;
use App\Models\Admin\EmailTemplate;
use App\Models\EmailSetting;
use App\Models\Admin\PaymentGatewaySetting;
use Config;
use PDF;
use Hashids;
use Mail;

class PaymentHandler
{
    public static function createDataPayload($package, $type, $repetition)
    {
        $data = [];

        $subscription_type = ($type == 1) ? 'Monthly' : 'Yearly';
        $description = $package->title . ' Package (' . $subscription_type . ' Subscription)';
        $price = ($type == 1) ? $package->monthly_price * $repetition : $package->yearly_price * $repetition;

        $data['items'] = [
            [
                'name'  => $package->title . ' Package',
                'desc'  => $description,
                'price' => $price,
                'qty'   => 1,
            ],
        ];

        $data['title'] = 'Package Subscription';
        $data['subscription_desc'] = $data['invoice_description'] = $description;

        $total = 0;
        foreach ($data['items'] as $item) {
            $total += $item['price'] * $item['qty'];
        }

        $data['total'] = $total;

        return $data;
    }

    public static function checkout($request, $package_id, $type, $user_id, $payment_method, $return_url, $cancel_url, $paid_by = '')
    {
        $user = User::find($user_id);
        $current_subscription = $user->subscription;
        $package = Package::find($package_id);

        $repetition = $request->has('repetition') ? $request->repetition : 1;
        $payment_option = $request->has('payment_option') ? $request->payment_option : 2;

        // ****************************************** //
        //    Send Email Of Package Updated By Admin  //
        // ****************************************** //

        $lang = !empty(session('lang')) && session('lang') != 'en' ? session('lang') : 'en';
        // $lang_file = public_path('i18n/translations/' . $lang . '.json');
        // $lang_arr = json_decode(file_get_contents($lang_file), true);
        $lang_arr = $lang;

        $email_template = EmailTemplate::where('type', 'paid_package_upgrade_downgrade_by_admin')->first();
        $email_template = transformEmailTemplateModel($email_template, $user->language);
        $name = $user->name;
        $email = $user->email;
        $previous_type = ($current_subscription->type == 1) ? 'Monthly' : 'Yearly';
        $new_type = ($type == 1) ? 'Monthly' : 'Yearly';
        $subject = $email_template['subject'];
        $content = $email_template['content'];

        $search = array("{{name}}", "{{from}}", "{{previous_type}}", "{{to}}", "{{new_type}}", "{{app_name}}");
        $replace = array($name, $current_subscription->package_title, $previous_type, $package->title, $new_type, settingValue('site_title'));
        $content  = str_replace($search, $replace, $content);

        // ************************** //
        //    Mollie Payment Gateway  //
        // ************************** //

        $paymentGatewaySettings = PaymentGatewaySetting::first();
        if ($paymentGatewaySettings->mollie_mode == 'sandbox') {
            $mollie_api_key = $paymentGatewaySettings->mollie_sandbox_api_key;
        } else if ($paymentGatewaySettings->mollie_mode == 'live') {
            $mollie_api_key = $paymentGatewaySettings->mollie_live_api_key;
        }

        $mollie = new \Mollie\Api\MollieApiClient();
        $mollie->setApiKey($mollie_api_key);

        // ************************** //
        //    Add New Subscription    //
        // ************************** //

        if ($package_id == 8) {
            $end_date = Carbon::now('UTC')->addDays(1)->timestamp;
            $price = $package->monthly_price;
        } else {
            if ($type == 1) {
                $end_date = Carbon::now('UTC')->addMonth($repetition)->timestamp;
                $price = $package->monthly_price * $repetition;
            } else {
                $end_date = Carbon::now('UTC')->addYear($repetition)->timestamp;
                $price = $package->yearly_price * $repetition;
            }
        }

        $packageLinkedFeatures = $package->linkedFeatures->pluck('count', 'feature_id')->toArray();

        $package_description = $package->description;
        $package_title = $package->title;

        // \Log::info('Session Language Checkout', array(
        //     'response' => session('lang')
        // ));

        if (!empty(session('lang')) && session('lang') != 'en') {
            // $package_title = translation($package->id,2,session('lang'),'title',$package->title);
            // $package_description = translation($package->id, 2, session('lang'), 'description', $package->description);
            $package_description = $package->description;
        }

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
            'user_id'       =>  $user_id,
            'package_id'    =>  $package_id,
            'price'         =>  $price,
            'features'      =>  empty($package->linkedFeatures) ? '' : json_encode($packageLinkedFeatures),
            'description'   =>  $package_description,
            'type'          =>  $type,
            'start_date'    =>  Carbon::now('UTC')->timestamp,
            'end_date'      => ($package->id == 2 || $package->id == 9) ? Null : $end_date,
            'repetition'    =>  $repetition,
            'payment_option' =>  $payment_option,
            'is_active'     =>  1,
            'email_limit' => $totalemails,
            'email_used' => 0,
            'sms_limit' => $totalsms,
            'sms_used' => 0,
            'contact_limit' => $total_contacts
        ]);

        if ($package->id == 2 || $package->id == 9) // Free Package
        {
            $packageSubscription->update([
                'payment_option' => 1
            ]);

            $user->update([
                'package_id'               => $package_id,
                'prev_package_subscription_id' => $current_subscription->id,
                'package_subscription_id'  => $packageSubscription->id,
                'package_recurring_flag'   => 0,
                'on_trial'                 => 0,
                'on_hold_package_id'       => NULL,
                'is_expired'               => 0,
                // 'total_allocated_space'    => $packageLinkedFeatures[1],
                // 'remaining_allocated_space' => $packageLinkedFeatures[1] * 1073741824, // Multiply With 1 GB
                // 'max_file_size'            => $packageLinkedFeatures[2],
                'switch_to_paid_package'   => 0,
                'package_updated_by_admin' => ($paid_by == 'Admin') ? 1 : 0,
                'unpaid_package_email_by_admin' => 0,
                'expired_package_disclaimer' => 0,
                'last_quota_revised'       => NULL
            ]);
            $user->update(['api_status' => $api == 2 ? 2 : $user->api_status]);

            if (!empty($user->mollie_customer_id) && !empty($current_subscription->payment)) {
                try {
                    $customer = $mollie->customers->get($user->mollie_customer_id);
                    $response = $customer->cancelSubscription($current_subscription->payment->profile_id);
                } catch (\Mollie\Api\Exceptions\ApiException $e) {
                }
            }

            if ($paid_by == 'Admin') {
                $user->update([
                    'last_quota_revised'   => date("Y-m-d H:i:s")
                ]);
                SendMail::dispatch($email, $subject, $content);
            }
            PaymentHandler::packageSwitchNotification($current_subscription, $lang);

            return response()->json([
                'status' => 1,
                'message' => 'Your package has been updated successfully.',
            ]);
        }

        // ************************** //
        //     Create Data Payload    //
        // ************************** //

        $data = self::createDataPayload($package, $type, $repetition);
        $data['invoice_id'] = uniqid();

        // ************************** //
        // Calculate Vat and Discount //
        // ************************** //

        $amount = $data['total'];
        $vat_percentage = settingValue('vat');
        $vat_country_code = 'de';

        if (!empty($user->country_id) && $user->country->apply_default_vat == 0 && $user->country->status == 1) {
            $vat_percentage =  $user->country->vat;
            $vat_country_code = $user->country->code;
        }

        if ($vat_percentage)
            $vat_amount = ($amount * $vat_percentage) / 100;
        else
            $vat_amount = 0;

        $voucher = '';
        $discount_percentage = $discount_amount = 0;

        if ($request->has('voucher') && !empty($request->voucher)) {
            $voucher = $request->voucher;
            $discount_percentage = $request->discount_percentage;
            $discount_amount = $request->discount_amount;
        }

        $total_amount = $amount + $vat_amount - $discount_amount;
        $total_amount = number_format($total_amount, 2, '.', '');

        // ************************** //
        //        Add New Payment     //
        // ************************** //

        $payment = Payment::create([
            'user_id'                   =>  $user_id,
            'subscription_id'           =>  $packageSubscription->id,
            'item'                      =>  $package_title,
            'payment_method'            =>  $payment_method,
            'amount'                    =>  $amount,
            'vat_percentage'            =>  $vat_percentage,
            'vat_amount'                =>  $vat_amount,
            'vat_country_code'          =>  strtolower($vat_country_code),
            'voucher'                   =>  $voucher,
            'discount_percentage'       =>  $discount_percentage,
            'discount_amount'           =>  $discount_amount,
            'total_amount'              =>  $total_amount,
            'payload'                   =>  json_encode($data),
            'payment_mode'              =>  $paymentGatewaySettings->mollie_mode == 'sandbox' ? 2 : 1,
            'lang'                      =>  !empty(session('lang')) && session('lang') != 'en' ? session('lang') : 'en'
        ]);

        if ($paid_by == 'Admin') {
            $user->update([
                'package_id'                => $packageSubscription->package_id,
                'prev_package_subscription_id' => $current_subscription->id,
                'package_subscription_id'   => $packageSubscription->id,
                'payment_id'                => $payment->id,
                'payment_method'            => 'admin',
                'package_recurring_flag'    => 0,
                'on_trial'                  => 0,
                'on_hold_package_id'        => NULL,
                'is_expired'                => 0,
                // 'total_allocated_space'     => $packageLinkedFeatures[1],
                // 'remaining_allocated_space' => $packageLinkedFeatures[1] * 1073741824, // Multiply With 1 GB
                // 'max_file_size'             => $packageLinkedFeatures[2],
                'switch_to_paid_package'    => 1,
                'package_updated_by_admin'  => 1,
                'unpaid_package_email_by_admin' => 0,
                'expired_package_disclaimer' => 0,
                'last_quota_revised'        => date("Y-m-d H:i:s")
            ]);

            $user->update(['api_status' => $api == 2 ? 2 : $user->api_status]);

            if (!empty($user->mollie_customer_id) && !empty($current_subscription->payment)) {
                try {
                    $customer = $mollie->customers->get($user->mollie_customer_id);
                    $response = $customer->cancelSubscription($current_subscription->payment->profile_id);
                } catch (\Mollie\Api\Exceptions\ApiException $e) {
                }
            }

            $payment->update([
                'timestamp' =>  Carbon::now('UTC')->timestamp,
                'discount_percentage'       =>  100,
                'discount_amount'           =>  $total_amount,
                'total_amount'              =>  0,
                'status'    => 1
            ]);

            // ****************************************************** //
            //   Send Invoice With Email Of Package Updated By Admin  //
            // ****************************************************** //

            $invoive_data = array();
            $invoive_data = self::generatePaymentInvoice($payment);
            $invoive_data['lang'] = $lang_arr;
            $data['payment'] = $payment;
            $data['lang'] = $lang;

            $invoive_data['global_font_family'] = in_array($lang, ['ja', 'zh']) ? 'chinesefont' : 'Segoe';
            $invoive_data['payment_font_family'] = in_array($payment->lang, ['ja', 'zh']) ? 'chinesefont' : 'Segoe';

            // \Log::info('Invoice data', array(
            //     'data' => $invoive_data
            // ));
            $pdf = PDF::loadView('emails.invoice', $invoive_data);

            // SendMail::dispatch($email, $subject, $content, $pdf, "invoice.pdf", $lang);
            // SendMail::dispatch($email, $subject, $content, $pdf);
            // SendMail::dispatch($email, $subject, $content);
            try {
                Mail::send('emails.template', ['content' => $content], function ($message) use ($email, $subject, $pdf) {
                    $message->to($email)
                        ->subject($subject)
                        ->attachData($pdf->output(), "invoice.pdf");
                    $bcc_emails = settingValue('bcc_emails');
                    if ($bcc_emails) {
                        $bcc_emails = explode(',', $bcc_emails);
                        foreach ($bcc_emails as $bcc_email) {
                            $message->bcc(trim($bcc_email));
                        }
                    }
                });
            } catch (\Exception $e) {
                \Log::info('Send Email Exception', array(
                    'Message' => $e->getMessage()
                ));
            }
            return;
        }

        $payment_success = false;
        $response_array = array();

        // if ($payment_method == Config::get('constants.payment_methods')['PAYPAL']) {
        if ($payment_method == 'paypal') {
            $response_array = self::payByPaypalCreditCard($user, $total_amount);

            if (isset($response_array['ACK']) && strtolower($response_array['ACK']) == 'success') {
                $payment->update([
                    'token'             =>  $response_array['CORRELATIONID'],
                    'txn_id'            =>  $response_array['TRANSACTIONID'],
                    'data'              =>  json_encode($response_array),
                    'timestamp'         =>  Carbon::now('UTC')->timestamp
                ]);

                $payment_success = true;
            }
            // } else if ($payment_method == Config::get('constants.payment_methods')['MOLLIE']) {
        } else if ($payment_method == 'mollie') {
            $customer = null;
            $customerExist = false;

            /*
            *Check Customer Existance
            */

            if (!empty($user->mollie_customer_id)) {
                try {
                    $customer = $mollie->customers->get($user->mollie_customer_id);
                    $customerExist = true;
                } catch (\Mollie\Api\Exceptions\ApiException $e) {
                    $customerExist = false;
                }
            }

            if (!$customerExist) {
                /*
                *Create a new customer
                */

                $customer = $mollie->customers->create([
                    'name'  => $user->name,
                    'email' => $user->email,
                ]);
            }

            /*
            *Initial payment
            */

            $response = $mollie->payments->create([
                "amount" => [
                    "currency" =>  strtoupper(Config::get('constants.currency')['code']),
                    // "currency" =>  'USD',
                    "value" => $total_amount // You must send the correct number of decimals, thus we enforce the use of strings
                ],
                'customerId'   => $customer->id,
                'sequenceType' => 'first',
                "description" => $data['subscription_desc'],
                "redirectUrl" => url("/packages/mollie-confirmation?order_id=" . Hashids::encode($packageSubscription->id)),
                "webhookUrl"  => url("/api/mollie/callback"),
                // "webhookUrl"  => url("/api/rfdf/callback"),
                "metadata" => [
                    "order_id" => $packageSubscription->id,
                    // "language" => session('lang')
                    "language" => 'en'
                ],
            ]);
            $redirectUrl = $response->getCheckoutUrl();
            $response = (array)$response;

            $payment->update([
                'data'      =>  json_encode($response),
                'timestamp' =>  Carbon::now('UTC')->timestamp,
                'txn_id'    => $response['id'],
                'status'    => 2
            ]);

            $user->update([
                'mollie_customer_id' => $customer->id,
            ]);

            return response()->json([
                'redirect_url' => $redirectUrl,
                'status' => 1,
                'message' => 'Mollie Payment Gateway Link'
            ]);
        }

        if ($payment_success) {
            $user->update([
                'package_id'                => $package_id,
                'prev_package_subscription_id' => $current_subscription->id,
                'package_subscription_id'   => $payment->subscription_id,
                'payment_id'                => $payment->id,
                'payment_method'            => $payment_method,
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
                'last_quota_revised'        => $type == 2 ? date("Y-m-d H:i:s") : NULL
            ]);

            $user->update(['api_status' => $api == 2 ? 2 : $user->api_status]);

            self::sendInvoiceEmail($payment);
            return response()->json(['status' => 1, 'message' => 'Your transaction is complete and payment has been successfully processed.']);
        } else {
            self::deleteRecords($payment);
            return response()->json([
                'data' => $response_array,
                'status' => 0,
                'message' => 'Oops something went wrong, selected package is failed to update. Please confirm your credit card details.'
            ]);
        }
    }

    public static function payByPaypalCreditCard($user, $amount)
    {
        $accountSettings = $user->accountSettings;
        $nameArr = explode(' ', $accountSettings->card_holder_name);

        $paymentGatewaySettings = PaymentGatewaySetting::first();
        $paypal_api_secret = $paypal_api_password = $paypal_api_username = $paypal_api_base_url = '';

        if ($paymentGatewaySettings->paypal_mode == 'sandbox') {
            $paypal_api_secret = $paymentGatewaySettings->paypal_sandbox_api_secret;
            $paypal_api_password = $paymentGatewaySettings->paypal_sandbox_api_password;
            $paypal_api_username = $paymentGatewaySettings->paypal_sandbox_api_username;
            $paypal_api_base_url = $paymentGatewaySettings->paypal_sandbox_api_base_url;
        } else if ($paymentGatewaySettings->paypal_mode == 'live') {
            $paypal_api_secret = $paymentGatewaySettings->paypal_live_api_secret;
            $paypal_api_password = $paymentGatewaySettings->paypal_live_api_password;
            $paypal_api_username = $paymentGatewaySettings->paypal_live_api_username;
            $paypal_api_base_url = $paymentGatewaySettings->paypal_live_api_base_url;
        }

        $request_params = array(
            'VERSION' => '56.0',
            'SIGNATURE' => $paypal_api_secret,
            'PWD' => $paypal_api_password,
            'USER' => $paypal_api_username,
            'METHOD' => 'DoDirectPayment',
            'PAYMENTACTION' => 'Sale',
            'IPADDRESS' => $_SERVER['REMOTE_ADDR'],
            'AMT' => $amount,
            'CREDITCARDTYPE' => $accountSettings->card_brand,
            'ACCT' => $accountSettings->card_number,
            'EXPDATE' => $accountSettings->expire_month . $accountSettings->expire_year,
            'CVV2' => $accountSettings->cvc,
            'FIRSTNAME' => $nameArr[0],
            'LASTNAME' => isset($nameArr[1]) ? $nameArr[1] : "",
            'STREET' => '',
            'CITY' => '',
            'ZIP' => '',
            'COUNTRYCODE' => 'DE',
            'CURRENCYCODE' => strtoupper(Config::get('constants.currency')['code'])
        );

        $nvp_string = http_build_query($request_params);
        $api_endpoint = $paypal_api_base_url . '/nvp';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_VERBOSE, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_URL, $api_endpoint);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $nvp_string);

        $result = curl_exec($curl);

        $response_array = array();
        foreach (explode('&', $result) as $chunk) {
            $param = explode("=", $chunk);
            if ($param && isset($param[0]) && isset($param[1])) {
                $response_array[$param[0]] = urldecode($param[1]);
            }
        }

        return $response_array;
    }

    public static function checkoutCancel($payment_id)
    {
        $payment = Payment::find($payment_id);
        self::deleteRecords($payment);
    }

    public static function deleteRecords($payment)
    {
        $subscription = $payment->subscription;
        $subscription->delete();
    }

    public static function generatePaymentInvoice($payment)
    {
        // $subscription = $payment->subscription;
        $subscription = PackageSubscription::where('id', $payment->subscription_id)->first();
        // dd($payment->subscription_id, $subscription);

        // \Log::info('generatePaymentInvoice', array(
        //     'payment' => $payment,
        //     'subscription' => $subscription
        // ));

        $amount = $subscription->price;
        $single_qty_amount = $amount;
        if ($subscription->repetition > 1) {
            $single_qty_amount = $amount / $subscription->repetition;
        }

        $item = array();
        $item['name'] = $payment->item;
        $item['amount'] = $amount;
        $item['single_qty_amount'] = $single_qty_amount;
        $item['type'] = $subscription->type;
        $item['repetition'] = $subscription->repetition;
        $item['start_date'] = $subscription->start_date;
        $item['end_date'] = $subscription->end_date;
        $item['vat_percentage'] = $payment->vat_percentage;
        $item['vat_amount'] = $payment->vat_amount;
        $item['reseller'] = $payment->reseller;
        $item['voucher'] = $payment->voucher;
        $item['discount_percentage'] = $payment->discount_percentage;
        $item['discount_amount'] = $payment->discount_amount;
        $item['total_amount'] = $payment->total_amount;
        $item['payment_method'] = $payment->payment_method;

        $featuresDetail = '';
        $features = json_decode($subscription->features, true);

        foreach ($features as $key => $value) {
            $packageFeature = PackageFeature::find($key);
            $count = $value > 0 ? '(' . $value . ')' : '';
            $featuresDetail .= $packageFeature->name . ' ' . $count . ',';
        }

        //$item['description'] = substr($featuresDetail, 0, strlen($featuresDetail)-2);
        $item['description'] = $subscription->description;

        $data['item'] = $item;
        $data['user'] = $payment->user;
        $data['payment'] = $payment;

        return $data;
    }

    public static function generatePayAsYouGoPaymentInvoice($payment)
    {
        $subscription = PackageSubscription::where('id', $payment->package_subscription_id)->first();

        $item = array();
        $item['name'] = 'Pay As You Go';
        $item['amount'] = $payment->amount;
        // $item['single_qty_amount'] = $single_qty_amount;
        $item['vat_percentage'] = $payment->vat_percentage;
        $item['vat_amount'] = $payment->vat_amount;
        $item['reseller'] = $payment->reseller;
        $item['voucher'] = $payment->voucher;
        $item['discount_percentage'] = $payment->discount_percentage;
        $item['discount_amount'] = $payment->discount_amount;
        $item['total_amount'] = $payment->total_amount_charged;
        $item['payment_method'] = $payment->payment_method;

        $featuresDetail = '';
        $features = json_decode($subscription->features, true);

        foreach ($features as $key => $value) {
            $packageFeature = PackageFeature::find($key);
            $count = $value > 0 ? '(' . $value . ')' : '';
            $featuresDetail .= $packageFeature->name . ' ' . $count . ',';
        }

        $item['description'] = 'total emails sent: ' . $payment->charging_for_emails . "<br>" .
            'total sms sent: ' . $payment->charging_for_sms . "<br>" .
            'total contacts created: ' . $payment->charging_for_contacts . "<br>" .
            'price for emails: ' . $payment->price_for_emails_charged . "<br>" .
            'price for sms: ' . $payment->price_for_sms_charged . "<br>" .
            // 'price for contacts: ' . $payment->price_for_contacts_charged . "<br>" .
            'total: ' . $payment->amount;

        $data['item'] = $item;
        $data['user'] = $payment->user;
        $data['payment'] = $payment;

        return $data;
    }

    // sendPayAsYouGoInvoiceEmail
    public static function sendPayAsYouGoInvoiceEmail($payment)
    {
        $lang = !empty(session('lang')) && session('lang') != 'en' ? session('lang') : 'en';

        $user = $payment->user;
        $name = $user->name;
        $email = $user->email;

        $email_template = EmailTemplate::where('type', 'payment_success')->first();
        $email_template = transformEmailTemplateModel($email_template, $lang);

        $subject = $email_template['subject'];
        $content = $email_template['content'];

        $search = array("{{name}}", "{{app_name}}");
        $replace = array($name, settingValue('site_title'));
        $content  = str_replace($search, $replace, $content);

        $data = array();
        $data = self::generatePayAsYouGoPaymentInvoice($payment);
        // $data['user'] = $user;
        $data['payment'] = $payment;
        $data['lang'] = $lang;
        $data['global_font_family'] = in_array($lang, ['ja', 'zh']) ? 'chinesefont' : 'Segoe';
        $data['payment_font_family'] = in_array($payment->lang, ['ja', 'zh']) ? 'chinesefont' : 'Segoe';

        $pdf = PDF::loadView('emails.pay_as_you_go_invoice', $data);

        try {
            Mail::send('emails.template', ['content' => $content], function ($message) use ($email, $subject, $pdf) {
                $message->to($email)
                    ->subject($subject)
                    ->attachData($pdf->output(), "invoice.pdf");
                $bcc_emails = settingValue('bcc_emails');
                if ($bcc_emails) {
                    $bcc_emails = explode(',', $bcc_emails);
                    foreach ($bcc_emails as $bcc_email) {
                        $message->bcc(trim($bcc_email));
                    }
                }
            });
        } catch (\Exception $e) {
            // \Log::info('Send Email Exception', array(
            //     'Message' => $e->getMessage()
            // ));
        }
    }

    public static function sendInvoiceEmail($payment)
    {
        $lang = !empty(session('lang')) && session('lang') != 'en' ? session('lang') : 'en';
        // $lang_file = public_path('i18n/translations/' . $lang . '.json');
        // $lang_arr = json_decode(file_get_contents($lang_file), true);

        $user = $payment->user;
        $name = $user->name;
        $email = $user->email;


        // \Log::info("sendInvoiceEmail function call user email =" . $email);


        $email_template = EmailTemplate::where('type', 'payment_success')->first();
        $email_template = transformEmailTemplateModel($email_template, $lang);

        $subject = $email_template['subject'];
        $content = $email_template['content'];

        $search = array("{{name}}", "{{app_name}}");
        $replace = array($name, settingValue('site_title'));
        $content  = str_replace($search, $replace, $content);

        $data = array();
        $data = self::generatePaymentInvoice($payment);
        $data['payment'] = $payment;
        $data['lang'] = $lang;
        // $data['lang_arr'] = $lang_arr;
        $data['global_font_family'] = in_array($lang, ['ja', 'zh']) ? 'chinesefont' : 'Segoe';
        $data['payment_font_family'] = in_array($payment->lang, ['ja', 'zh']) ? 'chinesefont' : 'Segoe';

        // \Log::info('Invoice data', array(
        //     'data' => $data
        // ));
        $pdf = PDF::loadView('emails.invoice', $data);

        // SendMail::dispatch($email, $subject, $content, $pdf, "invoice.pdf", $lang);
        // SendMail::dispatch($email, $subject, $content, $pdf);
        // SendMail::dispatch($email, $subject, $content);
        try {
            Mail::send('emails.template', ['content' => $content], function ($message) use ($email, $subject, $pdf) {
                $message->to($email)
                    ->subject($subject)
                    ->attachData($pdf->output(), "invoice.pdf");
                $bcc_emails = settingValue('bcc_emails');
                if ($bcc_emails) {
                    $bcc_emails = explode(',', $bcc_emails);
                    foreach ($bcc_emails as $bcc_email) {
                        $message->bcc(trim($bcc_email));
                    }
                }
            });
        } catch (\Exception $e) {
            // \Log::info('Send Email Exception', array(
            //     'Message' => $e->getMessage()
            // ));
        }
    }

    public static function packageSwitchNotification($payment, $lang)
    {
        $user = $payment->user;
        $name = $user->name;
        $email = $user->email;


        // \Log::info("packageSwitchNotification function call user email = " . $email);


        $previous_subscription = $payment->user->previousSubscription;
        $prev_package = $previous_subscription->package;

        $prev_package_title = $prev_package->title;
        $prev_package_type = empty($previous_subscription->type) ? '' : ($prev_package->id == 9 ? '(' . __('Weekly') . ')' : ($previous_subscription->type == 1 ? '(' . __('Monthly') . ')' : '(' . __('Yearly') . ')'));
        $new_package = $user->package;
        $new_package_name = $new_package->title;
        $new_package_type = empty($user->subscription->type) ? '' : ($new_package->id == 9 ? '(' . __('Weekly') . ')' : ($user->subscription->type == 1 ? '(' . __('Monthly') . ')' : '(' . __('Yearly') . ')'));
        $login_link = url('/signin');

        $email_template = EmailTemplate::where('type', 'package_switch_notification')->first();
        $email_template = transformEmailTemplateModel($email_template, $lang);

        $subject = $email_template['subject'];
        $content = $email_template['content'];

        $search = array("{{name}}", "{{app_name}}", "{{old_package}}", "{{new_package}}", "{{link}}", "{{platform}}");
        $replace = array($name, settingValue('site_title'), $prev_package_title . ' ' . $prev_package_type, $new_package_name . ' ' . $new_package_type, $login_link, settingValue('site_title'));
        $content  = str_replace($search, $replace, $content);

        SendMail::dispatch($email, $subject, $content, '', '', $lang);
    }
}
