<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\LanguageTranslation;
use App\Models\LanguageModule;
use App\Models\Language;
use Session;
use Hashids;
use Auth;
use DataTables;

class LanguageTranslationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!have_right(103))
            access_denied();

        $data = [];
        $data['language_modules'] = LanguageModule::where('status',1)->get();
        $data['languages'] = Language::where('status',1)->get();

        if($request->ajax())
        {
            $db_record = LanguageTranslation::where('status',1);

            if($request->has('language_module_id') && !empty($request->language_module_id))
            {
                $db_record = $db_record->where('language_module_id',$request->language_module_id);
            }

            if($request->has('language_id') && !empty($request->language_id))
            {
                $db_record = $db_record->where('language_id',$request->language_id);
            }

            $datatable = Datatables::of($db_record);
            $datatable = $datatable->addIndexColumn();

            $datatable = $datatable->addColumn('language_module', function($row)
            {
                return $row->languageModule->name;
            });

            $datatable = $datatable->addColumn('language_name', function($row)
            {
                return $row->language->name;
            });

            $datatable = $datatable->editColumn('custom', function($row)
            {
                $custom = '<span class="label label-danger">No</span>';
                if ($row->custom == 1)
                {
                    $custom = '<span class="label label-success">Yes</span>';
                }
                return $custom;
            });

            $datatable = $datatable->editColumn('status', function($row)
            {
                $status = '<span class="label label-danger">Disable</span>';
                if ($row->status == 1)
                {
                    $status = '<span class="label label-success">Active</span>';
                }
                return $status;
            });

            $datatable = $datatable->addColumn('action', function($row)
            {
                $actions = '<span class="actions">';
                
                if(have_right(105))
                {
                    $actions .= '&nbsp;<a class="btn btn-primary" href="'.url("admin/language-translations/" . Hashids::encode($row->id).'/edit').'" title="Edit"><i class="fa fa-pencil-square-o"></i></a>';
                }
                // if(have_right(40))
                // {
                //     $actions .= '&nbsp;<form method="POST" action="'.url("admin/language-translations/" . Hashids::encode($row->id)).'" accept-charset="UTF-8" style="display:inline">';
                //     $actions .= '<input type="hidden" name="_method" value="DELETE">';
                //     $actions .= '<input name="_token" type="hidden" value="'.csrf_token().'">';
                //     $actions .= '<button class="btn btn-danger" onclick="return confirm(\'Are you sure you want to delete this record?\');" title="Delete">';
                //     $actions .= '<i class="fa fa-trash"></i>';
                //     $actions .= '</button>';
                //     $actions .= '</form>';
                // }

                $actions .= '</span>';
                return $actions;
            });

            $datatable = $datatable->rawColumns(['custom','status','action']);
            $datatable = $datatable->make(true);
            return $datatable;
        }

        return view('admin.language-translations.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!have_right(104))
            access_denied();

        $data['model'] = new LanguageTranslation();
        $data['language_modules'] = LanguageModule::where('status',1)->get();
        $data['languages'] = Language::where('status',1)->whereNotIn('code', ['en'])->get();
        $data['action'] = "Add";
        return view('admin.language-translations.form')->with($data);
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

        if($input['action'] == 'Add')
        {
            $language_module = LanguageModule::find($request->language_module_id);
            $table = $language_module->table;
            $columns = explode(',', $language_module->columns);
            $records = \DB::table($table)->get();
            if($input['translate_language'] && $input['translate_language'] <> '')
            {
                $languages = Language::where('id',$input['translate_language'])->get();
            }
            else
            {
                $languages = Language::where('status',1)->whereNotIn('code', ['en'])->get();
            }

            foreach ($languages as $language) 
            {
                foreach ($records as $record)
                {
                    foreach ($columns as $column)
                    {   
                        $language_translation = LanguageTranslation::where(
                            [
                                'language_module_id' => $language_module->id,
                                'language_id'        => $language->id,
                                'item_id'            => $record->id,
                                'column_name'        => $column
                            ])
                            ->first();

                        if($request->translation_flag == 1 || empty($language_translation) || $language_translation->custom == 0)
                        {
                            $item_value = translationByDeepL($record->$column,$language->code);

                            LanguageTranslation::updateOrCreate(
                                [
                                    'language_module_id' => $language_module->id,
                                    'language_id'        => $language->id,
                                    'item_id'            => $record->id,
                                    'column_name'        => $column,
                                ],
                                [
                                    'language_module_id' => $language_module->id,
                                    'language_id'        => $language->id,
                                    'language_code'      => $language->code,
                                    'item_id'            => $record->id,
                                    'column_name'        => $column,
                                    'item_value'         => $item_value, 
                                    'custom'             => 0,
                                    'editor'             => 0,
                                    'status'             => 1
                                ]
                            );
                        }
                    }
                }
            }

            $flash_message = 'Language Translation has been created successfully.';
        }
        else
        {
            $model = LanguageTranslation::findOrFail($input['id']);
            $model->item_value = $input['item_value'];
            $model->custom = $input['custom'];
            $model->editor = $input['editor'];
            $model->save();
            $flash_message = 'Language Translation has been updated successfully.';
        }

        $request->session()->flash('flash_success', $flash_message);
        return redirect('admin/language-translations');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    
    public function edit($id)
    {
        if(!have_right(105))
            access_denied();

        if(!isset(Hashids::decode($id)[0]))
            abort(404);

        $id = Hashids::decode($id)[0];
        $data['action'] = "Edit";
        $data['model'] = LanguageTranslation::findOrFail($id);
        return view('admin.language-translations.form')->with($data); 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(!have_right(106))
            access_denied();

        $id = Hashids::decode($id)[0];
        LanguageTranslation::destroy($id);
        Session::flash('flash_success', 'Language Translation has been deleted successfully.');
        return redirect('admin/language-translations');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function partialTranslate()
    {
        if(!have_right(107))
            access_denied();

        $data['language_modules'] = LanguageModule::where('status',1)->get();
        $data['languages'] = Language::where('status',1)->whereNotIn('code', ['en'])->get();
        return view('admin.language-translations.partial_translate')->with($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addPartialTranslate(Request $request)
    {
        $input = $request->all();

        if($input['translate_language'] == 'all')
        {
            $languages = Language::where('status',1)->whereNotIn('code', ['en'])->get();
        }
        else
        {
            $languages = Language::where('id',$input['translate_language'])->get();
        }

        foreach ($languages as $language) 
        {
            LanguageTranslation::updateOrCreate(
                [
                    'language_module_id' => $request->language_module_id,
                    'language_id'        => $language->id,
                    'item_id'            => $request->item_id,
                    'column_name'        => $request->column_name,
                ],
                [
                    'language_module_id' => $request->language_module_id,
                    'language_id'        => $language->id,
                    'language_code'      => $language->code,
                    'item_id'            => $request->item_id,
                    'column_name'        => $request->column_name,
                    'item_value'         => translationByDeepL($request->text,$language->code),
                    'custom'             => 0,
                    'editor'             => 0,
                    'status'             => 1
                ]
            );
        }

        $request->session()->flash('flash_success', 'Language Translation has been created successfully.');
        return redirect('admin/language-translations');
    }
}
