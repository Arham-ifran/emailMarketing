<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Language;
use Session;
use Hashids;
use Auth;

class TextTranslationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!have_right(100))
            access_denied();

        return view('admin.text-translations.form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $languages = Language::where('status', 1)->get();

        foreach ($languages as $language) {
            $labels_file = public_path() . '/i18n/translations/' . $language->code . '.json';
            $get_labels = file_get_contents($labels_file);
            $label_array = json_decode($get_labels, true);

            // Create Labels by converting text to lower case and replace space with underscore
            $request_label = strtolower($request->text);
            $request_label = preg_replace('/\s+/', '_', $request_label);
            $request_label = str_replace('.', '', $request_label);
            $request_label = str_replace('?', '', $request_label);
            $request_label = str_replace('!', '', $request_label);
            $request_label = str_replace(',', '', $request_label);


            if ($language->code == 'en') {
                $label_array[$request_label] = $request->text;
            } else {
                $label_array[$request_label] = translationByDeepL($request->text, $language->code);
            }

            file_put_contents(public_path() . '/i18n/translations/' . $language->code . '.json', json_encode($label_array, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        }

        $request->session()->flash('flash_success', 'Text Translations has been created successfully.');
        return redirect('admin/text-translations');
    }
}
