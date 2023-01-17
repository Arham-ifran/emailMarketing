<?php

use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\GroupController;
use App\Http\Controllers\Api\Contact_GroupController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\SmsCampaignController;
use App\Http\Controllers\Api\CampaignContactController;
use App\Http\Controllers\Api\CampaignExcludeController;
use App\Http\Controllers\Api\CmsPageController;
use App\Http\Controllers\Api\SmsCampaignGroupController;
use App\Http\Controllers\RestApi\ApiController;
use App\Http\Controllers\Api\SmsTemplatesController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\VisitingPagesController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
// 	return $request->user();
// });

//Visiting pages Routes
Route::get('cms-pages', [CmsPageController::class, 'index']);
Route::get('cms-pages/detail', [CmsPageController::class, 'detail']);
Route::get('/get-home-sections', [VisitingPagesController::class, 'getHomeSections']);
Route::get('/get-home-services', [VisitingPagesController::class, 'getHomeServices']);
Route::get('/get-home-faqs', [VisitingPagesController::class, 'getHomeFaqs']);
Route::post('/contact-us', [VisitingPagesController::class, 'contactUs']);
Route::get('/get-contact-us-details', [VisitingPagesController::class, 'getContactDetails']);
Route::get('/get-features-sections', [VisitingPagesController::class, 'getFeaturesSections']);
Route::get('/get-about-us-sections', [VisitingPagesController::class, 'getAboutUsSections']);
Route::get('/get-about-us-testimonials', [VisitingPagesController::class, 'getAboutUsTestimonials']);
Route::get('/get-socials', [VisitingPagesController::class, 'getSocials']);
Route::any('/logout-admin', [App\Http\Controllers\HomeController::class, 'logout']);

Route::group(['middleware' => ['web']], function () {
	Route::get('auth/google-auth-url', [AuthController::class, 'googleLoginAuthUrl']);
	Route::get('auth/google-callback', [AuthController::class, 'googleLoginCallback']);

	Route::get('auth/facebook-auth-url', [AuthController::class, 'facebookLoginAuthUrl']);
	Route::get('auth/facebook-callback', [AuthController::class, 'facebookLoginCallback']);

	Route::get('auth/twitter-auth-url', [AuthController::class, 'twitterLoginAuthUrl']);
	Route::get('auth/twitter-callback', [AuthController::class, 'twitterLoginCallback']);
});

Route::group(['namespace' => 'Api'], function () {
	Route::post('auth/voucher-register', [VisitingPagesController::class, 'voucherRegister']);
	Route::post('auth/register', 'AuthController@register');
	Route::post('auth/login', 'AuthController@login');
	Route::post('auth/logout', 'AuthController@logout');

	Route::get('auth/profile', 'AuthController@profile');
	Route::post('auth/update-profile', 'AuthController@updateProfile');
	Route::get('auth/account-settings', 'AuthController@accountSettings');
	// Route::post('auth/update-account-settings', 'AuthController@updateAccountSettings');
	Route::post('auth/verify-account', 'AuthController@verifyAccount');
	Route::post('auth/resend-verification-email', 'AuthController@resendVerificationEmail');
	// Route::post('check-user', 'AuthController@checkUser');
	Route::get('auth/update-user-lang', 'AuthController@updateLanguage');

	Route::post('auth/password/send-reset-link', 'PasswordResetController@sendResetLink');
	Route::get('auth/password/validate-reset-token/{token}', 'PasswordResetController@validateResetToken');
	Route::post('auth/password/reset', 'PasswordResetController@reset');


	//email campaign
	Route::post('/campaign/create-update', 'EmailCampaignController@store');
	Route::get('/campaign/report', 'EmailCampaignController@campaignReportListing');
	Route::get('/campaign/{campaign_id?}/edit', 'EmailCampaignController@edit');
	Route::get('/contact/group', 'EmailCampaignController@getUserGroups');
	Route::post('/campaign/create-update', 'EmailCampaignController@store');
	Route::delete('/campaign/{campaign_id}', 'EmailCampaignController@destroy');
	Route::post('/campaign/stop/{id}', 'EmailCampaignController@stop');
	Route::get('/get-email-campaign-report/{id}/{history}', 'EmailCampaignController@report');
	Route::get('/get-email-campaign-report-histories/{id}', 'EmailCampaignController@reportHistories');
	Route::get('/campaign-contacts-report/{id}', 'EmailCampaignController@reportCSV');

	Route::post('/campaign/stats-show', 'EmailCampaignController@campaignCounter');

	Route::post('/campaign-template/create-update', 'EmailCampaignTemplateController@saveEmailTemplate');
	Route::get('/get-campaign-template/{id?}', 'EmailCampaignTemplateController@getTemplate');
	Route::get('/campaign-template/{id?}/edit', 'EmailCampaignTemplateController@edit');

	Route::get('/campaign-template/index', 'EmailCampaignTemplateController@index');
	Route::delete('/campaign-template/{template_id}', 'EmailCampaignTemplateController@destroy');
	Route::post('/upload-template-image', 'EmailCampaignTemplateController@addImage');
	Route::get('/public-campaign-template/index', 'PublicEmailCampaignTemplateController@index');
	Route::get('/public-campaign-template/import/{id}', 'PublicEmailCampaignTemplateController@import');

	//Route::post('/split-testing/create-update', 'SplitTestingController@store');
	//Route::get('/split-testing/list', 'SplitTestingController@campaignReportListing');
	//Route::get('/split-testing/{campaign_id?}/edit', 'SplitTestingController@edit');
	Route::resource('/split-testing', 'SplitTestingController');
	Route::post('/split-testing/stats-show', 'SplitTestingController@campaignCounter');

	Route::get('/get-split-campaign-report/{id}', 'SplitTestingController@report');
	Route::get('/get-split-subject/{id}', 'SplitTestingController@getSubject');
	Route::post('/split-campaign/stop/{id}', 'SplitTestingController@stop');

	// Contacts Routes
	Route::get('/get-contacts', [ContactController::class, 'index']);
	Route::get('/get-contact/{id}', [ContactController::class, 'show']);
	// Route::post('/add-contact', [ContactController::class, 'store']); //not used
	Route::post('/add-contacts', [ContactController::class, 'storeMany']);
	// Route::post('/edit-contact/{id}', [ContactController::class, 'update']); //not used
	Route::post('/edit-contacts', [ContactController::class, 'updateMany']);
	// Route::post('/delete-contact/{id}', [ContactController::class, 'destroy']); //not used
	Route::post('/delete-contacts', [ContactController::class, 'destroyMany']);
	Route::post('/subscribe-contact/{id}', [ContactController::class, 'subscribe']);
	Route::post('/unsubscribe-contact/{id}', [ContactController::class, 'unsubscribe']);
	// Route::post('/activate-contact/{id}', [ContactController::class, 'activate']); //not used
	// Route::post('/deactivate-contact/{id}', [ContactController::class, 'deactivate']); //not used
	Route::post('/get-contacts-info', [ContactController::class, 'contactsInfo']);
	// contacts bulk import export Routes
	Route::post('/check-file-import', [ContactController::class, 'checkFileImport']);
	Route::post('/file-import', [ContactController::class, 'fileImport']);
	Route::get('/file-export', [ContactController::class, 'fileExport']);
	Route::get('/can-add-contacts', [ContactController::class, 'canAddContacts']);

	// Group (List) Routes
	Route::get('/get-all-groups', [GroupController::class, 'getAll']);
	Route::get('/get-groups', [GroupController::class, 'index']);
	Route::get('/get-group/{id}', [GroupController::class, 'show']);
	Route::post('/add-group', [GroupController::class, 'store']);
	Route::post('/edit-group/{id}', [GroupController::class, 'update']);
	Route::post('/delete-group/{id}', [GroupController::class, 'destroy']);
	// Route::post('/activate-group/{id}', [GroupController::class, 'activate']); //not used
	// Route::post('/deactivate-group/{id}', [GroupController::class, 'deactivate']); //not used
	Route::post('/get-groups-info', [GroupController::class, 'groupsInfo']);
	// Add/Remove Contacts from Groups 
	Route::post('/add-to-group', [Contact_GroupController::class, 'addToGroup']);
	Route::post('/remove-from-group', [Contact_GroupController::class, 'removeContactFromGroup']);

	// SmsCampaign Routes
	Route::get('/get-sms-campaigns', [SmsCampaignController::class, 'index']);
	Route::get('/get-sms-campaign/{id}', [SmsCampaignController::class, 'show']);
	Route::get('/get-sms-campaign-report/{id}/{history}', [SmsCampaignController::class, 'report']);
	Route::get('/get-sms-campaign-report-histories/{id}', [SmsCampaignController::class, 'reportHistories']);
	Route::post('/add-sms-campaign', [SmsCampaignController::class, 'store']);
	// Route::post('/draft-sms-campaign', [SmsCampaignController::class, 'draft']); //not used
	Route::post('/edit-sms-campaign', [SmsCampaignController::class, 'store']);
	Route::post('/delete-sms-campaign/{id}', [SmsCampaignController::class, 'destroy']);
	Route::post('/get-sms-campaigns-info', [SmsCampaignController::class, 'info']);
	Route::post('/stop-sms-campaign/{id}', [SmsCampaignController::class, 'stop']);
	// Add/Remove Contacts from Campaign 
	// Route::post('/send-smscampaign', [SmsCampaignController::class, 'sendCampaign']); 
	Route::post('/remove-contact-from-smscampaign', [CampaignContactController::class, 'removeFromCampaign']); //not used
	// Route::post('/add-group-to-smscampaign', [SmsCampaignGroupController::class, 'addToCampaign']); //not used
	Route::post('/remove-group-from-smscampaign', [SmsCampaignGroupController::class, 'removeFromCampaign']); //not used

	Route::post('/add-contact-to-campaignincludes', [CampaignContactController::class, 'addToCampaign']);
	Route::post('/add-contact-to-campaignexcludes', [CampaignExcludeController::class, 'addToCampaign']);
	Route::post('/remove-contact-from-campaignincludes', [CampaignContactController::class, 'removeFromCampaign']);
	Route::post('/remove-contact-from-campaignexcludes', [CampaignExcludeController::class, 'removeFromCampaign']);
	Route::post('/clear-campaignexcludes', [CampaignExcludeController::class, 'clearCampaign']);

	// SMS Template routes
	Route::get('/get-sms-templates', [SmsTemplatesController::class, 'index']);
	Route::get('/get-sms-template/{id}', [SmsTemplatesController::class, 'show']);
	Route::post('/add-sms-templates', [SmsTemplatesController::class, 'store']);
	Route::post('/delete-sms-template/{id}', [SmsTemplatesController::class, 'destroy']);

	// Notification routes
	Route::get('/get-notifications', [NotificationController::class, 'index']);
	Route::get('/read-notifications', [NotificationController::class, 'readAll']);
	Route::get('/read-notification/{id}', [NotificationController::class, 'readOne']);
	Route::get('/delete-notification/{id}', [NotificationController::class, 'delete']);
	Route::get('/read-payment-notification', [NotificationController::class, 'readPaymentNoti']);
	Route::get('/read-paid-notification', [NotificationController::class, 'readPaidNoti']);
	// dashboare routes
	Route::get('/get-dashboard-data', [HomeController::class, 'dashboard']);
	// analytics routes
	Route::get('/get-analytics', [HomeController::class, 'analytics']);
	// api routes.
	Route::get('/get-apidata', [HomeController::class, 'apiData']);
	Route::post('/update-apidata', [HomeController::class, 'updateApiData']);
	Route::get('/refresh-api-token', [HomeController::class, 'refreshApiToken']);
	Route::get('/refresh-api-key', [HomeController::class, 'refreshApiKey']);
	Route::get('/download-api-doc', [HomeController::class, 'downloadApiDocument']);

	// other routes
	Route::get('/get-countries', [CountryController::class, 'getCountries']);
	Route::get('/get-country/{id}', [CountryController::class, 'getCountry']);

	// packages routes
	Route::post('subscription/payment-checkout', [SubscriptionController::class, 'paymentCheckout']);
	Route::post('subscription/paypal-checkout-success', [SubscriptionController::class, 'paypalCheckoutSuccess']);
	Route::post('subscription/payment-checkout-cancel', [SubscriptionController::class, 'paymentCheckoutCancel']);
	Route::post('subscription/update-transmission-features', [SubscriptionController::class, 'updatetransmissionFeatures']);

	Route::get('subscription/get-current-package', [SubscriptionController::class, 'getCurrentPackage']);
	Route::get('subscription/cancel-current-package', [SubscriptionController::class, 'cancelCurrentPackage']);
	Route::get('subscription/check-status', [SubscriptionController::class, 'checkStatus']);
	Route::get('subscription/expired-package-disclaimer-flag', [SubscriptionController::class, 'expiredPackageDisclaimerFlag']);
	Route::get('subscription/update-package-by-admin-flag', [SubscriptionController::class, 'updatePackageByAdminFlag']);
	Route::get('subscription/unpaid-package-email-by-admin-flag', [SubscriptionController::class, 'unpaidPackageEmailByAdminFlag']);
	Route::get('subscription/payments', [SubscriptionController::class, 'payments']);
	Route::get('subscription/pay-as-you-go-payments', [SubscriptionController::class, 'payAsYouGoPayments']);
	Route::get('subscription/download-payment-invoice/{id}', [SubscriptionController::class, 'downloadPaymentInvoice']);
	Route::get('subscription/download-pay-as-you-go-invoice/{id}', [SubscriptionController::class, 'downloadPayAsYouGoInvoice']);

	Route::post('mollie/callback', [SubscriptionController::class, 'molliePayment']);
	Route::post('mollie/pay-as-you-go-callback', [SubscriptionController::class, 'molliePayAsYouGoPayment']);
	Route::post('mollie/verify-order', [SubscriptionController::class, 'molliePaymentVerify']);
	Route::post('mollie/subscriptions/webhook', [SubscriptionController::class, 'mollieSubscriptionWebhook']);

	Route::get('faqs', 'ListingController@faqs');
	Route::get('languages', 'ListingController@languages');
	Route::get('timezones', 'ListingController@timezones');
	Route::get('settings', 'ListingController@settings');
	Route::get('countries', 'ListingController@countries');
	Route::get('get-country-vat', 'ListingController@getCountryVat');
	Route::get('packages', 'ListingController@packages');
	Route::get('package-detail', 'ListingController@packageDetail');
	Route::get('payment-gateway-settings', 'ListingController@paymentGatewaySetting');
	Route::get('features', 'ListingController@features');
	Route::get('services', 'ListingController@services');
	Route::get('home-contents', 'ListingController@homeContents');
	Route::get('get-geo-location', 'ListingController@getGeoLocation');

	Route::get('get-user-package', [HomeController::class, 'getUserPackage']);
	Route::get('can-switch', 'ListingController@canSwitch');
	Route::get('pending-payments', [SubscriptionController::class, 'pendingPayments']);
	Route::get('regenerate-payment/{id}', [SubscriptionController::class, 'regeneratePayment']);
	Route::get('pay-as-you-go-pricing', 'ListingController@payAsYouGoPricing');
	Route::post('vouchers/redeem', 'VoucherController@redeem');
});

// Route::post('check', [HomeController::class, 'check']);

// =============================== //
// ======== API V1 ROUTES ======== //
// =============================== //
Route::prefix('v1')->group(function () {
	// ==========
	// api routes
	// ==========

	// contacts
	Route::get('/get-contacts', [ApiController::class, 'contactIndex']);
	Route::get('/get-contact/{id}', [ApiController::class, 'contactShow']);
	Route::post('/add-contact', [ApiController::class, 'contactStore']);
	Route::patch('/edit-contact', [ApiController::class, 'contactUpdate']);
	Route::delete('/delete-contact', [ApiController::class, 'contactDestroy']);
	Route::patch('/subscribe-contact/{id}', [ApiController::class, 'subscribe']);
	Route::patch('/unsubscribe-contact/{id}', [ApiController::class, 'unsubscribe']);
	// Route::patch('/get-contacts-info', [ApiController::class, 'contact_contacts_info']);

	// groups
	Route::get('/get-groups', [ApiController::class, 'groupIndex']);
	Route::get('/get-group/{id}', [ApiController::class, 'groupShow']);
	Route::post('/add-group', [ApiController::class, 'groupStore']);
	Route::patch('/edit-group/{id}', [ApiController::class, 'groupUpdate']);
	Route::delete('/delete-group/{id}', [ApiController::class, 'groupDestroy']);

	// sms campaign
	Route::get('/get-sms-campaigns', [ApiController::class, 'smsIndex']);
	Route::get('/get-sms-campaign/{id}', [ApiController::class, 'smsShow']);
	Route::post('/add-sms-campaign', [ApiController::class, 'smsStore']);
	Route::patch('/edit-sms-campaign', [ApiController::class, 'smsStore']);
	Route::delete('/delete-sms-campaign/{id}', [ApiController::class, 'smsDestroy']);
	Route::get('/get-sms-campaign-report/{id}', [ApiController::class, 'smsReport']);
	Route::patch('/stop-sms-campaign/{id}', [ApiController::class, 'smsStop']);
	// Route::post('/draft-sms-campaign', [ApiController::class, 'sms_draft']); //cancelled

	// email campaign
	Route::get('/get-email-campaigns', [ApiController::class, 'emailIndex']);
	Route::get('/get-email-campaign/{id}', [ApiController::class, 'emailShow']);
	Route::post('/add-email-campaign', [ApiController::class, 'emailStore']);
	Route::patch('/edit-email-campaign', [ApiController::class, 'emailStore']);
	Route::delete('/delete-email-campaign/{id}', [ApiController::class, 'emailDestroy']);
	Route::get('/get-email-campaign-report/{id}', [ApiController::class, 'emailReport']);
	Route::patch('/stop-email-campaign/{id}', [ApiController::class, 'emailStop']);

	// split campaign
	Route::get('/get-split-campaigns', [ApiController::class, 'splitIndex']);
	Route::get('/get-split-campaign/{id}', [ApiController::class, 'splitShow']);
	Route::post('/add-split-campaign', [ApiController::class, 'splitStore']);
	Route::patch('/edit-split-campaign', [ApiController::class, 'splitStore']);
	Route::delete('/delete-split-campaign/{id}', [ApiController::class, 'splitDestroy']);
	Route::get('/get-split-campaign-report/{id}', [ApiController::class, 'splitReport']);
	Route::patch('/stop-split-campaign/{id}', [ApiController::class, 'emailStop']);

	// templates
	Route::get('/get-templates', [ApiController::class, 'templateIndex']);
});
