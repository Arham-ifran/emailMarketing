<?php

namespace App\Imports;

use App\CustomClasses\TranslationHandler;
use App\Models\Admin\Package;
use App\Models\Admin\PackageLinkFeature;
use App\Models\Contact;
use App\Models\Group;
use App\Models\Contact_group;
use App\Models\PayAsYouGoPayments;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;


class ContactsImport implements ToModel
{
    private $imported_contacts = [], $data, $ignoreFirstRow = false, $emailLimit = 0, $smsLimit = 0, $applyLimit = false, $contactLimit = 0, $request;

    public function __construct($data, $request)
    {
        $this->data = $data;
        $this->request = $request;

        // checking user package limits
        // if (Auth()->user()->package_id && Auth()->user()->package_id != 9) {
        //     $package = Package::where('id', Auth()->user()->package_id)->where('status', 1)->orderBy('monthly_price')->first();
        //     if ($package) {
        //         // for_email_limit
        //         $foundfeature = PackageLinkFeature::where('package_id', $package->id)->where('feature_id', 3)->first();
        //         if ($foundfeature) {
        //             // $this->emailLimit = $foundfeature->count;
        //             $this->contactLimit = $foundfeature->count;
        //             $this->applyLimit = true;
        //         }
        //     }
        // }
        // checking user package limits end
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

    public function model(array $row)
    {
        if ($this->ignoreFirstRow) {

            $validData = true;
            $cols = [
                'first_name'    => $row[$this->data["first_name"]],
                'last_name'     => $row[$this->data["last_name"]],
                'for_sms'       => $row[$this->data["for_sms"]],
                'for_email'     => $row[$this->data['for_email']],
                'email'         => $row[$this->data["email"]],
                'number'        => '+' . $row[$this->data["number"]],
            ];

            $messages = [
                'required' => TranslationHandler::getTranslation($this->request->lang, 'required'),
                'string' => TranslationHandler::getTranslation($this->request->lang, 'required'),
                'email.string' => TranslationHandler::getTranslation($this->request->lang, 'required'),
                'first_name.*.max' => TranslationHandler::getTranslation($this->request->lang, 'max_35'),
                'last_name.*.max' => TranslationHandler::getTranslation($this->request->lang, 'max_35'),
                'email.max' => TranslationHandler::getTranslation($this->request->lang, 'max_35'),
                'email.*.regex' => TranslationHandler::getTranslation($this->request->lang, 'valid_email'),
                'number.*.required' => TranslationHandler::getTranslation($this->request->lang, 'required'),
                'number.*.regex' => TranslationHandler::getTranslation($this->request->lang, 'number_regex'),
                'number.*.max' => TranslationHandler::getTranslation($this->request->lang, 'number_invalid'),
            ];
            $validator = \Validator::make($cols, [
                'first_name' => ['required', 'string', 'max:35'],
                'last_name' => ['required', 'string', 'max:35'],
                'for_email' => ['required', 'boolean'],
                'for_sms' => ['required', 'boolean'],
            ], $messages);
            if ($validator->fails()) {
                $validData = false;
                // return NULL;
            }
            if ($cols["for_sms"]) {
                // check sms fields
                $validator = \Validator::make($cols, [
                    'number' => ['required', 'regex:/(\+)([1-9]{2})([0-9]{10})/', 'max:13'],
                ], $messages);
                if ($validator->fails()) {
                    $validData = false;
                    // return NULL;
                }
            }
            if ($cols["for_email"]) {
                // check email fields
                $validator = \Validator::make($cols, [
                    'email' => ['required', 'string', 'regex:/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/', 'max:65'],
                ], $messages);
                if ($validator->fails()) {
                    $validData = false;
                    // return NULL;
                }
            }

            if ($validData) {
                $contact = NULL;
                $found_email = Contact::where([
                    'user_id'       => auth()->user()->id,
                    'email'         => $row[$this->data["email"]],
                ])->first();
                $found_num = Contact::where([
                    'user_id'       => auth()->user()->id,
                    'number'         => '+' . $row[$this->data["number"]],
                ])->first();

                $createcont = true;

                if ($found_email) {
                    try {
                        $contact = Contact::updateOrCreate(
                            [
                                'user_id'       => auth()->user()->id,
                                'email'         => $row[$this->data["email"]],
                            ],
                            [
                                'first_name'    => $row[$this->data["first_name"]],
                                'last_name'     => $row[$this->data["last_name"]],
                                'for_sms'       => $row[$this->data["for_sms"]],
                                'for_email'     => $row[$this->data['for_email']],
                                'number'        => '+' . $row[$this->data["number"]],
                            ]
                        );
                    } catch (\Throwable $th) {
                        $createcont = false;
                    }
                } else if ($found_num) {
                    try {
                        $contact = Contact::updateOrCreate(
                            [
                                'user_id'       => auth()->user()->id,
                                'number'         => '+' . $row[$this->data["number"]],
                            ],
                            [
                                'first_name'    => $row[$this->data["first_name"]],
                                'last_name'     => $row[$this->data["last_name"]],
                                'for_sms'       => $row[$this->data["for_sms"]],
                                'for_email'     => $row[$this->data['for_email']],
                                'email'         => $row[$this->data["email"]],
                            ]
                        );
                    } catch (\Throwable $th) {
                        $createcont = false;
                    }
                } else {
                    $hasContacts = Contact::where('user_id', Auth()->user()->id)->count();

                    // if (((!$row[$this->data["for_sms"]] || !$row[$this->data["for_email"]]) && ($row[$this->data["for_sms"]] || $row[$this->data["for_email"]])) || ($row[$this->data["for_sms"]] && $hasContacts < $this->smsLimit) && ($row[$this->data["for_email"]] && $hasContacts < $this->emailLimit)) {
                    // if ($row[$this->data["for_sms"]] && $hasContacts >= $this->smsLimit) {
                    //     $createcont = false;
                    // }
                    // if ($row[$this->data["for_email"]] && $hasContacts >= $this->emailLimit) {
                    //     $createcont = false;
                    // }
                    if ($row[$this->data["for_email"]] && $this->applyLimit && $hasContacts >= $this->contactLimit) {
                        $createcont = false;
                    }
                    if ($createcont) {
                        try {
                            $contact = Contact::create([
                                'user_id'       => auth()->user()->id,
                                'first_name'    => $row[$this->data["first_name"]],
                                'last_name'     => $row[$this->data["last_name"]],
                                'for_sms'       => $row[$this->data["for_sms"]],
                                'for_email'     => $row[$this->data['for_email']],
                                'email'         => $row[$this->data["email"]],
                                'number'        => '+' . $row[$this->data["number"]]
                            ]);
                            if (Auth()->user()->package && Auth()->user()->package_id == 9) {
                                // $contacts_span1_start = PackageSettingsValue(7, 'start_range');
                                // $contacts_span1_end = PackageSettingsValue(7, 'end_range');
                                // $contacts_span1_price = PackageSettingsValue(7, 'price_without_vat');
                                // $contacts_span2_start = PackageSettingsValue(8, 'start_range');
                                // $contacts_span2_end = PackageSettingsValue(8, 'end_range');
                                // $contacts_span2_price = PackageSettingsValue(8, 'price_without_vat');
                                // $contacts_span3_start = PackageSettingsValue(9, 'start_range');
                                // $contacts_span3_price = PackageSettingsValue(9, 'price_without_vat');
                                // Auth()->user()->subscription->contacts_paying_for += 1;
                                // if ($hasContacts >= $contacts_span1_start && $hasContacts <= $contacts_span1_end) {
                                //     Auth()->user()->subscription->contacts_to_pay += $contacts_span1_price;
                                // } else if ($hasContacts >= $contacts_span2_start && $hasContacts <= $contacts_span2_end) {
                                //     Auth()->user()->subscription->contacts_to_pay += $contacts_span2_price;
                                // } else if ($hasContacts >= $contacts_span3_start) {
                                //     Auth()->user()->subscription->contacts_to_pay += $contacts_span3_price;
                                // }
                                // Auth()->user()->subscription->save();
                                // check payment Relief
                                $last_payment = PayAsYouGoPayments::where('package_subscription_id', Auth()->user()->subscription->id)->where('status', '!=', 1)->first();
                                if ($last_payment && isset($last_payment->timestamp)) {
                                    $last_payment = $last_payment->timestamp;
                                    $payment_relief_days = (int)settingValue('payment_relief_days');
                                    $releif_timestamp = Carbon::now('UTC')->subDays($payment_relief_days)->timestamp;
                                    if ($last_payment < $releif_timestamp) {
                                        return response(['message' => TranslationHandler::getTranslation($this->request->lang, 'deactivating_account'), 'code' => 1, 'errors' => ['error_message' => [TranslationHandler::getTranslation($this->request->lang, 'package_unpaid')]],], 422);
                                        Auth()->user()->update([
                                            'status' => 0
                                        ]);
                                    }
                                }
                            }
                        } catch (\Throwable $th) {
                            $createcont = false;
                        }
                    }
                }
                if ($contact && $createcont) {
                    array_push($this->imported_contacts, $contact);
                }
                // $default = Group::where('user_id', auth()->user()->id)->where('name', 'default')->first();
                // $default = Group::where('user_id', auth()->user()->id)->first();
                // if ($default) {
                //     $data = ['contact_id' => $contact->id, 'group_id' => $default->id, 'user_id' => auth()->user()->id];
                //     Contact_group::create($data);
                // }

                return $contact;
            }
        } else {
            $this->ignoreFirstRow = true;
        }
    }

    public function getImported()
    {
        return $this->imported_contacts;
    }
}
