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
use DataTables;

class LabelTranslationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!have_right(109))
            access_denied();

        $data['languages'] = Language::where('status', 1)->whereNotIn('code', ['en'])->get();
        return view('admin.label-translations.form')->with($data);
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

        $labels_file = public_path('i18n/translations/en.json');
        $get_labels = file_get_contents($labels_file);
        $label_array = json_decode($get_labels, true);
        $translated_labels_array = [];

        if ($input['translate_language'] && $input['translate_language'] <> '') {
            $languages = Language::where('id', $input['translate_language'])->get();
        } else {
            $languages = Language::where('status', 1)->whereNotIn('code', ['en'])->get();
        }

        foreach ($languages as $language) {
            foreach ($label_array as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $innerKey => $innerValue) {
                        $translated_labels_array[$key][$innerKey] = translationByDeepL($innerValue, $language->code);
                    }
                } else {
                    $translated_labels_array[$key] = translationByDeepL($value, $language->code);
                }
            }

            \Storage::disk('i18n_translations')->put($language->code . '.json', json_encode($translated_labels_array, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }

        $request->session()->flash('flash_success', 'Label Translations has been created successfully.');
        return redirect('admin/label-translations');
    }
}
