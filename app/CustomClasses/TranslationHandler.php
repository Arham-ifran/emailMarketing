<?php

namespace App\CustomClasses;

class TranslationHandler
{
    /**** INSTANTIATION *******************************************************/
    // public function __construct($lang, $message)
    // {

    // }

    /**** METHODS *************************************************************/
    // 
    public static function getTranslation($lang, $message)
    {
        $lang = $lang && $lang != 'en' ? $lang : 'en';

        $lang_file = public_path('i18n/translations/' . $lang . '.json');
        $lang_arr = json_decode(file_get_contents($lang_file), true);
        return $lang_arr[$message];
    }
}
