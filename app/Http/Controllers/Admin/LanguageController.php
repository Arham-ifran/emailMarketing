<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Session;
use Hashids;
use Auth;

class LanguageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!have_right(99))
            access_denied();

        $data['languages'] = Language::orderBy('created_at','DESC')->get();
        return view('admin.languages.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!have_right(100))
            access_denied();

        $data['language'] = new Language();
        $data['action'] = "Add";
        return view('admin.languages.form')->with($data);
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
            $validator = Validator::make($request->all(), [
                'name' => ['required','string','max:100',Rule::unique('languages')],
                'code' => ['required','max:10']
            ]);
            
            $model = new Language();            
            $flash_message = 'Language has been created successfully.';
        }
        else
        {
            $validator = Validator::make($request->all(), [
                'name' => ['required','string',Rule::unique('languages')->ignore($input['id'])],
                'code' => ['required','max:10']
            ]);

            $model = Language::findOrFail($input['id']);
            $flash_message = 'Language has been updated successfully.';

            if($model->status == 1 && $model->totalUsers() > 0 && $input['status'] == 0)
            {
                Session::flash('flash_danger', "You cannot disable this language because user(s) have already using this language.");
                return redirect()->back()->withInput();
            }
        }

        if ($validator->fails())
        {
            Session::flash('flash_danger', $validator->messages());
            return redirect()->back()->withInput();
        }

        $model->fill($input);
        $model->save();
        $request->session()->flash('flash_success', $flash_message);
        return redirect('admin/languages');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(!have_right(101))
            access_denied();

        $id = Hashids::decode($id)[0];
        $data['action'] = "Edit";
        $data['language'] = Language::findOrFail($id);
        return view('admin.languages.form')->with($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(!have_right(102))
            access_denied();

        $id = Hashids::decode($id)[0];
        $language = Language::find($id);
        if($language->totalUsers())
        {
            Session::flash('flash_danger', "You cannot delete this language because users(s) of this language have been created.");
            return redirect('admin/languages');
        }

        Language::destroy($id);
        Session::flash('flash_success', 'Language has been deleted successfully.');
        return redirect('admin/languages');
    }
}
