<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\Language;
use App\Http\Resources\PackageLinkedFeatureResource;
use App\Http\Resources\FaqResource;
use App\Http\Resources\FeatureResource;
use App\Http\Resources\HomeContentResource;
use App\Http\Resources\PackageResource;
use App\Models\Admin\Country;
use App\Models\Admin\Faq;
use App\Models\Admin\Feature;
use App\Models\Admin\HomeContent;
use App\Models\Admin\Package;
use App\Models\Admin\PaymentGatewaySetting;
use App\Models\Admin\Service;
use App\Models\Admin\Timezone;
use App\Models\PayAsYouGoPayments;

class ListingController extends Controller
{
    /**
     * Display a listing of the faqs.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function faqs(Request $request)
    {
        return FaqResource::collection(Faq::where('status', 1)->orderBy('order_by', 'ASC')->get())
            ->additional([
                'message' => 'Faq Listing',
                'status'  => 1
            ]);
    }

    /**
     * Display a listing of the languages.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function languages(Request $request)
    {
        $languages = Language::where('status', 1)->whereNull('deleted_at')->get();

        return response()->json([
            'data' => $languages,
            'status' => 1,
            'message' => 'Languages Listing'
        ], 200, ['Content-Type' => 'application/json']);
    }

    /**
     * Display a listing of the timezones.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function timezones(Request $request)
    {
        $timezones = Timezone::all();

        return response()->json([
            'data' => $timezones,
            'status' => 1,
            'message' => 'Timezones Listing'
        ], 200, ['Content-Type' => 'application/json']);
    }

    /**
     * Display a listing of the settings.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function settings()
    {
        $result = \DB::table('settings')->get()->toArray();
        $settings = [];
        foreach ($result as $value) {
            $settings[$value->option_name] = $value->option_value;
        }

        return response()->json([
            'data' => $settings,
            'status' => 1,
            'message' => 'Settings'
        ], 200, ['Content-Type' => 'application/json']);
    }

    /**
     * Display a listing of the Countries.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function countries(Request $request)
    {
        $countries = Country::where('status', 1)->get();

        return response()->json([
            'data' => $countries,
            'status' => 1,
            'message' => 'Countries Listing'
        ], 200, ['Content-Type' => 'application/json']);
    }

    /**
     * Display detail of the country.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function getCountryVat(Request $request)
    {
        $vat = settingValue('vat');
        $country = Country::where('name', $request->country_name)->orWhere('code', $request->country_code)->first();

        if (!empty($country) && $country->apply_default_vat == 0) {
            $vat = $country->vat;
        }

        return response()->json([
            'data' => array(
                'vat' => $vat
            ),
            'status' => 1,
            'message' => 'Country Vat'
        ]);
    }

    /**
     * Display a listing of the packages.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function packages(Request $request)
    {
        return PackageResource::collection(Package::whereNotIn('id', [1])->where('status', 1)->orderBy('monthly_price')->get())
            ->additional([
                'message' => 'Packages Listing',
                'status'  => 1
            ]);
    }

    /**
     * Display detail of the package.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function packageDetail(Request $request)
    {
        $package = Package::where('id', $request->package_id)->first();
        $user = auth()->user();

        return (new PackageResource($package))
            ->additional([
                'additional' => array(
                    'vat' => !empty($user->country_id) && $user->country->apply_default_vat == 0 && $user->country->status == 1 ? $user->country->vat : settingValue('vat'),
                ),
                'message' => 'Package detail',
                'status'  => 1
            ]);
    }

    /**
     * Display a listing of the payment gateway settings.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function paymentGatewaySetting(Request $request)
    {
        $result = PaymentGatewaySetting::first();

        return response()->json([
            'data' => $result,
            'status' => 1,
            'message' => 'Payment Gateway Settings'
        ]);
    }

    /**
     * Display a listing of the features.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function features(Request $request)
    {
        return FeatureResource::collection(Feature::where('status', 1)->get())
            ->additional([
                'message' => 'Features Listing',
                'status'  => 1
            ]);
    }

    /**
     * Display a listing of the services.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function services(Request $request)
    {
        $services = Service::where('status', 1)->orderBy('created_at', 'DESC')->get();

        return response()->json([
            'data' => $services,
            'status' => 1,
            'message' => 'Services Listing'
        ], 200, ['Content-Type' => 'application/json']);
    }

    /**
     * Display a listing of the home contents.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function homeContents(Request $request)
    {
        // $contents = HomeContent::where('status', 1)->orderBy('created_at', 'DESC')->get();

        // return response()->json([
        //     'data' => $contents,
        //     'status' => 1,
        //     'message' => 'Contents Listing'
        // ], 200, ['Content-Type' => 'application/json']);

        return HomeContentResource::collection(HomeContent::where('status', 1)->orderBy('created_at', 'DESC')->get())
            ->additional([
                'message' => 'Home Contents Listing',
                'status'  => 1
            ]);
    }

    public function canSwitch()
    {
        $payments = PayAsYouGoPayments::where('user_id', auth()->user()->id)->where('status', '!=', 1)->count();
        if ($payments) {
            return response()->json([
                'status' => 0,
                'message' => 'has not paid'
            ]);
        } else {
            return response()->json([
                'status' => 1,
                'message' => 'has paid'
            ]);
        }
    }

    public function payAsYouGoPricing()
    {
        return response()->json([
            'status' => 1,
            'data' => [
                'email_span1_start' => PackageSettingsValue(1, 'start_range'),
                'email_span1_end' => PackageSettingsValue(1, 'end_range'),
                'email_span1_price' => PackageSettingsValue(1, 'price_without_vat'),
                'email_span2_start' => PackageSettingsValue(2, 'start_range'),
                'email_span2_end' => PackageSettingsValue(2, 'end_range'),
                'email_span2_price' => PackageSettingsValue(2, 'price_without_vat'),
                'email_span3_start' => PackageSettingsValue(3, 'start_range'),
                'email_span3_price' => PackageSettingsValue(3, 'price_without_vat'),

                'sms_span1_start' => PackageSettingsValue(4, 'start_range'),
                'sms_span1_end' => PackageSettingsValue(4, 'end_range'),
                'sms_span1_price' => PackageSettingsValue(4, 'price_without_vat'),
                'sms_span2_start' => PackageSettingsValue(5, 'start_range'),
                'sms_span2_end' => PackageSettingsValue(5, 'end_range'),
                'sms_span2_price' => PackageSettingsValue(5, 'price_without_vat'),
                'sms_span3_start' => PackageSettingsValue(6, 'start_range'),
                'sms_span3_price' => PackageSettingsValue(6, 'price_without_vat'),

                // 'contacts_span1_start' => PackageSettingsValue(7, 'start_range'),
                // 'contacts_span1_end' => PackageSettingsValue(7, 'end_range'),
                // 'contacts_span1_price' => PackageSettingsValue(7, 'price_without_vat'),
                // 'contacts_span2_start' => PackageSettingsValue(8, 'start_range'),
                // 'contacts_span2_end' => PackageSettingsValue(8, 'end_range'),
                // 'contacts_span2_price' => PackageSettingsValue(8, 'price_without_vat'),
                // 'contacts_span3_start' => PackageSettingsValue(9, 'start_range'),
                // 'contacts_span3_price' => PackageSettingsValue(9, 'price_without_vat'),
            ]
        ]);
    }

    public function getGeoLocation()
    {
        /*
        * Start call geo location api
        */

        // set IP address and API access key 
        $ip = $_SERVER['REMOTE_ADDR'];
        $access_key = 'KAohk4snSmCrpqn';

        // Initialize CURL:
        $ch = curl_init('https://pro.ip-api.com/json/' . $ip . '?fields=66842623&key=' . $access_key);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Store the data:
        $json = curl_exec($ch);
        curl_close($ch);

        // Decode JSON response:
        $api_result = json_decode($json, true);

        return response()->json([
            'data' => $api_result,
            'status' => 1,
            'message' => 'Geo location'
        ]);

        /*
        * End call geo location api
        */
    }
}
