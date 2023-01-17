<?php

use App\Http\Controllers\Admin\EmailCampaignController;
use App\Http\Controllers\Admin\SplitCampaignController;
use App\Http\Controllers\Admin\EmailCampaignTemplateController;
// use App\Http\Controllers\Admin\FeatureLabelsController;
// use App\Http\Controllers\Admin\ServiceLabelsController;
use App\Http\Controllers\Admin\SmsCampaignController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\VisitingPagesController;
use App\Jobs\SendCapmaignMail;
use App\Jobs\SendMail;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



// Route::get('/test-mollie', [VisitingPagesController::class, 'testMolliePayment']);

Route::get('/migrate', function () {
    $re = Artisan::call('migrate');
    dd($re);
});
Route::get('/clear-cache', function () {
    $re = Artisan::call('optimize:clear');
    dd($re);
});
Route::get('/seeder/{seederclass}', function ($seederclass) {
    $re = Artisan::call('db:seed --class=' . $seederclass);
    dd($re);
});
Route::get('/queue-listen', function () {
    $re = Artisan::call('queue:listen');
    dd($re);
});
Route::get('/generate-payments', function () {
    $re = Artisan::call('generate:payments');
    dd($re);
});
Route::get('/quota-revision', function () {
    $re = Artisan::call('monthly_quota:users');
    dd($re);
});
Route::get('/subscription-expired', function () {
    $re = Artisan::call('package_subscription:expired');
    dd($re);
});
Route::get('/subscription-expired-notification', function () {
    $re = Artisan::call('subscription_expired:notifications');
    dd($re);
});
Route::get('/user-followups', function () {
    $re = Artisan::call('user:inactivity-follow-up');
    dd($re);
});
Route::get('/send-crone-email', function () {
    $re = Artisan::call('send:crone-email');
    dd($re);
});

// Route::get('/check-sendmail', function () {
//     $re = SendCapmaignMail::dispatch([], ['Sameeraa', 'sameera@arhamsoft.com', 'sameeraaaa@arhamsoft.com'], 'hamna.farooq@arhamsoft.org', 'Checking Campaignemail Delivery', "Your email has been delivered.");
//     $re = SendCapmaignMail::dispatch([], ['Sameeraa', 'sameera@arhamsoft.com', 'sameeraaaa@arhamsoft.com'], 'php.laravel.deve@gmail.com', 'Checking Campaignemail Delivery', "Your email has been delivered.");
//     dd(0);
// });

Route::get('/start-bounce-job', [App\Http\Controllers\MailController::class, 'startBounceJob']);

// Route::get('/', function () {
//     return view('app');
// });

// Route::get('/check-mail', function () {
//     $email_template = EmailTemplate::where('type', 'account_info')->first();
//     $email_template = transformEmailTemplateModel($email_template, 'en');
//     $subject = $email_template['subject'];
//     $content = $email_template['content'];
//     $contact_link = url('/contact-us');
//     $search = array("{{name}}", "{{contact_link}}", "{{no_of_days}}", "{{app_name}}");
//     $replace = array('Aliha', $contact_link, 30, settingValue('site_title'));
//     $content = str_replace($search, $replace, $content);
//     SendMail::dispatch('alihatanveer.arhamsoft@gmail.com', $subject, $content);
// });




Route::get('/home', [VisitingPagesController::class, 'index'])->name('home');
// Route::get('/subscribe', [App\Http\Controllers\MailController::class, 'subscribeContact']);
Route::get('/unsubscribe', [App\Http\Controllers\MailController::class, 'unsubscribeContact']);
Route::get('/track-campaign', [App\Http\Controllers\HomeController::class, 'track']);
Route::get('/click-campaign', [App\Http\Controllers\HomeController::class, 'click']);
// Route::get('/verify-account/{id}', [AuthController::class, 'verifyAccount']);

Route::get('/invoice', function () {
    return view('emails.invoicePlaceholder');
});

Route::get('admin/login', [App\Http\Controllers\Auth\Admin\LoginController::class, 'login'])->name('admin.auth.login');
Route::post('admin/login', [App\Http\Controllers\Auth\Admin\LoginController::class, 'loginAdmin'])->name('admin.auth.loginAdmin');
Route::any('admin/logout', [App\Http\Controllers\Auth\Admin\LoginController::class, 'logout'])->name('admin.auth.logout');
Route::get('admin/forgot-password',  [App\Http\Controllers\Auth\Admin\ForgotPasswordController::class, 'forgotPasswordForm'])->name('admin.auth.forgot-password');
Route::post('admin/send-reset-link-email', [App\Http\Controllers\Auth\Admin\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('admin.auth.send-reset-link-email');
Route::get('admin/reset-password/{token}', [App\Http\Controllers\Auth\Admin\ForgotPasswordController::class, 'resetPasswordForm']);
Route::post('admin/reset-password', [App\Http\Controllers\Auth\Admin\ForgotPasswordController::class, 'resetPassword'])->name('admin.auth.reset-password');

Route::group(['namespace' => 'Admin', 'as' => 'admin.', 'prefix' => 'admin', 'middleware' => ['auth:admin', 'admin.check.status']], function () {
    Route::get('/', 'DashboardController@dashboard');
    Route::get('dashboard', 'DashboardController@dashboard')->name('dashboard');
    Route::post('ajax-received-notification', 'DashboardController@ajaxReceivedNotification');
    Route::get('settings', 'SettingController@index')->name('settings');
    Route::post('settings', 'SettingController@updateSettings')->name('update-settings');
    Route::get('profile', 'AdminController@profile')->name('profile');
    Route::post('profile', 'AdminController@updateProfile')->name('update-profile');
    Route::resource('admins', AdminController::class);
    Route::get('users/send-password/{id}', 'UserController@sendPassword');
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
    Route::resource('package-features', PackageFeatureController::class);
    Route::get('packages/subscriptions/{id}', 'PackageController@subscriptions');
    Route::get('packages/clone/{id}', 'PackageController@clone');
    Route::resource('packages', PackageController::class);
    Route::resource('faqs', FaqController::class);
    Route::resource('countries', CountryController::class);
    Route::resource('cms-pages', CmsPagesController::class);
    Route::resource('cms-page-labels', CmsPageLabelController::class);
    Route::resource('email-templates', EmailTemplateController::class);
    Route::resource('email-template-labels', EmailTemplateLabelController::class);
    // Route::get('email-campaign-templates', 'EmailCampaignTemplateController@index');
    Route::resource('email-campaign-templates', 'EmailCampaignTemplateController');
    Route::resource('email-campaign-template-labels', 'EmailCampaignTemplateLabelController');
    Route::resource('contact-us-queries', ContactUsQueryController::class);
    Route::resource('features', FeaturesController::class);
    Route::resource('feature-labels', FeatureLabelController::class);
    Route::resource('services', ServicesController::class);
    Route::resource('service-labels', ServiceLabelsController::class);
    Route::resource('about-us-page', AboutUsController::class);
    Route::resource('about-us-labels', AboutUsLabelController::class);
    Route::resource('about-us-testimonials', AboutUsTestimonialController::class);
    // Route::resource('features', FeaturesController::class);
    Route::resource('home-contents', HomeContentController::class);
    Route::resource('home-content-labels', HomeContentLabelController::class);

    // Route::post('language-translations/partial-translate', [LanguageTranslationController::class, 'addPartialTranslate']);
    // Route::resource('language-translations', LanguageTranslationController::class);
    // Route::resource('language-modules', LanguageModuleController::class);

    Route::resource('languages', 'LanguageController');
    Route::resource('label-translations', 'LabelTranslationController');

    Route::get('language-translations/partial-translate', 'LanguageTranslationController@partialTranslate');
    Route::post('language-translations/partial-translate', 'LanguageTranslationController@addPartialTranslate');
    Route::resource('language-translations', 'LanguageTranslationController');
    Route::resource('text-translations', 'TextTranslationController');
    Route::resource('language-modules', 'LanguageModuleController');

    Route::get('email-campaigns', [EmailCampaignController::class, 'index']);
    Route::get('email-campaigns/{id}/view', [EmailCampaignController::class, 'report']);
    Route::get('email-campaigns/{id}/view/{hid}', [EmailCampaignController::class, 'history']);
    Route::get('email-campaigns/{id}/view/{hid}/download', [EmailCampaignController::class, 'reportDataDownload']);
    Route::get('split-campaigns', [SplitCampaignController::class, 'index']);
    Route::get('split-campaigns/{id}/view', [SplitCampaignController::class, 'report']);
    Route::get('split-campaigns/{id}/view/{hid}', [SplitCampaignController::class, 'history']);
    Route::get('split-campaigns/{id}/view/{hid}/download', [SplitCampaignController::class, 'reportDataDownload']);
    Route::get('sms-campaigns', [SmsCampaignController::class, 'index']);
    Route::get('sms-campaigns/{id}/view', [SmsCampaignController::class, 'report']);
    Route::get('sms-campaigns/{id}/view/{hid}', [SmsCampaignController::class, 'history']);
    Route::get('sms-campaigns/{id}/view/{hid}/download', [SmsCampaignController::class, 'reportDataDownload']);

    Route::get('users/packages/{id}', 'UserController@packages');
    Route::post('users/update-package', 'UserController@updatePackage');
    // Route::resource('users', 'UserController');

    // Route::resource('roles', 'RoleController');

    Route::resource('package-features', 'PackageFeatureController');
    Route::resource('package-settings', 'PackageSettingController');
    Route::get('packages/subscriptions/{id}', 'PackageController@subscriptions');
    Route::get('packages/clone/{id}', 'PackageController@clone');
    Route::resource('packages', 'PackageController');
    Route::get('payment-settings', 'PaymentsController@index');
    Route::post('payment-settings', 'PaymentsController@update');
    Route::get('package-payments', 'PaymentsController@payments');
    Route::get('pay-as-you-go-package-payments', 'PaymentsController@payAsYouGoPayments');

    Route::get('lawful-interception', 'LawfulInterceptionController@index');
    Route::get('lawful-interception/user-details-pdf/{id}', 'LawfulInterceptionController@userDetailsPdf');
    Route::get('lawful-interception/user-payments-pdf/{id}', 'LawfulInterceptionController@userPaymentsPdf');
    Route::get('lawful-interception/user-subscriptions-pdf/{id}', 'LawfulInterceptionController@userSubscriptionsPdf');
    Route::get('lawful-interception/user-sms-pdf/{id}', 'LawfulInterceptionController@userSMSCampaigns');
    Route::get('lawful-interception/user-email-pdf/{id}', 'LawfulInterceptionController@userEmailCampaigns');
    Route::get('lawful-interception/user-split-pdf/{id}', 'LawfulInterceptionController@userSplitCampaigns');
    Route::get('lawful-interception/archive-user-data/{id}', 'LawfulInterceptionController@archiveUserData');
    Route::get('lawful-interception/download-all-data/{id}', 'LawfulInterceptionController@downloadAllData');
    Route::get('lawful-interception/check-user-temp-file/{id}', 'LawfulInterceptionController@checkUserTempFile');
});



Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
