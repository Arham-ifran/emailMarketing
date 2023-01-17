<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\User;
use App\Models\PackageSubscription;
use App\Models\MigrationPayment;
use App\Models\CloudMigrationReport;
use App\Models\MailboxMigrationReport;
use App\Models\CaldavMigrationReport;
use App\Models\EmailTemplate;
use App\Classes\PaymentHandler;
use App\Models\Admin\Payment;
use App\Models\Admin\PaymentGatewaySetting;
use App\Models\PayAsYouGoPayments;
use Illuminate\Support\Facades\Storage;
use DB;
use PDF;
use Config;
use Hashids;

class GeneratePayAsYouGoPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:payments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "An invoice of payment will be generated and sent to the users subscribed to pay as you go package.";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // \Log::info('inside cron');

        $users = User::where('package_id', 9)->get();

        if (!$users->isEmpty()) {

            foreach ($users as $user) {
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
            }
        }
    }
}
