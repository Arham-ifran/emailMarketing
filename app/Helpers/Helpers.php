<?php

use App\Models\Admin\PackageSubscription;
use App\Models\LanguageTranslation;

function checkImage($path = '', $placeholder = '')
{
    if (empty($placeholder)) {
        $placeholder = 'placeholder.png';
    }

    if (!empty($path)) {
        $url = explode('storage', $path);
        $url = public_path() . '/storage' . $url[1];
        $isFile = explode('.', $url);
        if (file_exists($url) && count($isFile) > 1) {
            return $path;
        } else {
            return asset('images/' . $placeholder);
        }
    } else {
        return asset('images/' . $placeholder);
    }
}

function rights()
{
    $result = DB::table('rights')
        ->select('rights.id', 'rights.name as right_name', 'modules.name as module_name')
        ->join('modules', 'rights.module_id', '=', 'modules.id')
        ->where(['rights.status' => 1])
        ->get()
        ->toArray();

    $array = [];

    for ($i = 0; $i < count($result); $i++) {
        $array[$result[$i]->module_name][] = $result[$i];
    }
    return $array;
}

function have_right($right_id)
{
    $user = \Auth::user();
    if ($user['role_id'] == 1) {
        return true;
    } else {
        $result = \DB::table('roles')
            ->where('id', $user['role_id'])
            ->whereRaw("find_in_set($right_id,right_ids)")
            ->first();

        if (!empty($result)) {
            return true;
        } else {
            return false;
        }
    }
}

function access_denied()
{
    abort(403, 'You have no right to perform this action.');
}

function settingValue($key)
{
    $setting = \DB::table('settings')->where('option_name', $key)->first();
    if ($setting) {
        return $setting->option_value;
    } else {
        return '';
    }
}

function PackageSettingsValue($row, $key)
{
    $setting = \DB::table('package_settings')->where('id', $row)->first();
    if ($setting) {
        return $setting->$key;
    } else {
        return '';
    }
}

function getRecord($tbl, $where)
{
    $record = \DB::table($tbl)->where($where)->first();
    if ($record) {
        return $record;
    } else {
        return "";
    }
}

function getRawRecord($tbl, $where)
{
    //"find_in_set('4',author)"
    $record = \DB::table($tbl)->whereRaw($where)->get();
    return $record;
}

function createNotification($type, $user_id, $message, $link, $fa_class)
{
    $notifications = [
        'type' => $type,
        'user_id' => $user_id,
        'message' => $message,
        'link' => $link,
        'fa_class' => $fa_class,
        'created_at' => date("Y-m-d H:i:s"),
        'updated_at' => date("Y-m-d H:i:s"),
    ];

    $id = DB::table('notifications')->insertGetId($notifications);

    $notification = \App\Models\Admin\Notification::find($id);
    broadcast(new \App\Events\NotificationSent($notification))->toOthers();
    return $notification;
}

function getNotifications($user_id, $is_read, $type)
{
    if ($is_read == -1) // all
    {
        $conditions = ['type' => $type];
    } else {
        $conditions = ['type' => $type, 'is_read' => $is_read];
    }

    if ($type == 2) // user
    {
        $conditions['user_id'] = $user_id;
    }

    return \App\Models\Admin\Notification::select('*')
        ->where($conditions)
        ->orderBy('created_at', 'DESC')
        ->get();
}

function durationConversion($seconds)
{
    $time = gmdate("H:i:s", $seconds);
    $timeArr = explode(':', $time);
    $durationStr = '';

    if ($timeArr[0] != '00') {
        $durationStr .= $timeArr[0] . ' hr ';
    }

    if ($timeArr[1] != '00') {
        $durationStr .= $timeArr[1] . ' min ';
    }

    if ($timeArr[2] != '00') {
        $durationStr .= $timeArr[2] . ' sec';
    }

    return $durationStr;
}

function getValue($tbl, $column, $where)
{
    $record = \DB::table($tbl)->where($where)->first();
    if ($record) {
        return $record->$column;
    } else {
        return "";
    }
}

function activatePackage($user_id, $package)
{
    $packageLinkedFeatures = $package->linkedFeatures->pluck('count', 'feature_id')->toArray();

    $features = $package->linkedFeatures->pluck('feature_id')->toArray();
    $counts = $package->linkedFeatures->pluck('count')->toArray();

    $totalemails = 0;
    $totalsms = 0;
    $total_contacts = 0;
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
        'user_id' => $user_id,
        'package_id' => $package->id,
        'price' => $package->price,
        'features' => empty($package->linkedFeatures) ? '' : json_encode($packageLinkedFeatures),
        'description' =>  $package->description,
        'start_date' => \Carbon\Carbon::now('UTC')->timestamp,
        'end_date' => Null,
        'is_active' => 1,
        'contact_limit' => $total_contacts,
        'email_limit' => $totalemails,
        'email_used' => 0,
        'sms_limit' => $totalsms,
        'sms_used' => 0
    ]);

    $usr = \App\Models\User::where('id', $user_id)->update([
        'package_id' => $package->id,
        'package_subscription_id' => $packageSubscription->id,
        'on_trial' => 0,
        'last_quota_revised'    => NULL
    ]);

    $usr->update(['api_status' => $api == 2 ? 2 : $usr->api_status]);

    if (array_key_exists(1, $packageLinkedFeatures)) {
        \App\Models\User::where('id', $user_id)->update([
            // 'total_allocated_space' => $packageLinkedFeatures[1],
            // 'remaining_allocated_space' => $packageLinkedFeatures[1] * 1073741824, // Multiply With 1 GB
            // 'max_file_size' => $packageLinkedFeatures[2],
            'switch_to_paid_package' => 0
        ]);
    }

    return 1;
}


function transformEmailTemplateModel($model, $lang)
{
    $subject = translation($model->id, 4, $lang, 'subject', $model->subject);
    $subject = $model->subject;
    $content = $model->content;

    $search = [];
    $replace = [];
    $ids = [];
    $labels = $model->emailTemplateLabels;

    foreach ($labels as $object) {
        $search[$object->id] = '{{' . $object->label . '}}';
        $replace[$object->id] = $object->value;
        $ids[] = $object->id;
    }

    if ($lang != 'en') {
        $translations = LanguageTranslation::where(['language_module_id' => 10, 'language_code' => $lang, 'column_name' => 'value'])->whereIn('item_id', $ids)->get();

        foreach ($translations as $translation) {
            $replace[$translation->item_id] = $translation->item_value;
        }
    }

    $content  = str_replace($search, $replace, $content);

    return [
        'id'      => $model->id,
        'subject' => $subject,
        'content' => $content,
        'lang'    => $lang,
        'type'    => $model->type,
        'info'    => $model->info,
        'status'  => $model->status
    ];
}

function translation($item_id, $language_module_id, $lang, $column_name, $org_value)
{
    $record = \App\Models\LanguageTranslation::where(['item_id' => $item_id, 'language_module_id' => $language_module_id, 'language_code' => $lang, 'column_name' => $column_name])->first();

    if (!empty($record))
        return $record->item_value;
    else
        return $org_value;
}

function translationByDeepL($text, $target_lang)
{
    if (empty($text))
        return $text;
    else {
        if ($target_lang == 'br') {
            $target_lang = 'pt-BR';
        }

        $params = array(
            'auth_key' => 'd554170c-80ad-7185-f19c-b776394eb975',
            'text' => $text,
            'target_lang' => $target_lang,
            'source_lang' => 'en'
        );

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.deepl.com/v2/translate?" . http_build_query($params),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);


        $responseArr = json_decode($response, true);

        if (is_array($responseArr) && !empty($responseArr) && array_key_exists('translations', $responseArr)) {
            return $responseArr['translations'][0]['text'];
        } else {
            return $text;
        }
    }
}

function checkVoucherValidity($redeem_url, $data)
{
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $redeem_url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 200,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => http_build_query($data),
        // CURLOPT_HTTPHEADER => array(
        //     'Content-Type: application/json'
        // ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    $response = json_decode($response, true);
    return $response;
}

// function getPlatform($platform_id)
// {
//     // 1- Web, 2- Mobile, 3- Thunderbird, 4- Outlook, 5- Transfer Immunity , 6- Ned Link, 7- aikQ, 8- Inbox, 9- Overmail, 10- Maili, 11- Product Immunity 12- QR Code 13- TIMmunity 14- Move Immunity
// }

function translateText($target_lang, $string)
{
    $lang = $target_lang && $target_lang != 'en' ? $target_lang : 'en';

    $lang_file = public_path('i18n/translations/' . $lang . '.json');
    $lang_arr = json_decode(file_get_contents($lang_file), true);

    $translation = $lang_arr[$string];

    if ($translation)
        return $translation;
    else
        return $string;
}

function initCurl($url, $data)
{
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => http_build_query($data),
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/x-www-form-urlencoded',
        ),
    ));

    return $curl;
}
