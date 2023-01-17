<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin\EmailTemplate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Session;
use Hashids;
use Auth;

class EmailTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!have_right(86))
            access_denied();

        $data['email_templates'] = EmailTemplate::orderBy('created_at', 'ASC')->get();
        return view('admin.email-templates.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //access_denied();
        if (!have_right(87))
            access_denied();

        $data['email_template'] = new EmailTemplate();
        $data['action'] = "Add";
        return view('admin.email-templates.form')->with($data);
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
        $input['status'] = 1;

        if ($input['action'] == 'Add') {
            $validator = Validator::make($request->all(), [
                'subject' => ['required', 'string', 'max:250'],
                'type' => ['required', 'string', 'max:250', Rule::unique('email_templates')],
                'content' => ['required', 'string'],
            ]);

            $model = new EmailTemplate();
            $flash_message = 'Email Template has been created successfully.';
        } else {
            $validator = Validator::make($request->all(), [
                'subject' => ['required', 'string', 'max:250'],
                'type' => ['max:250', Rule::unique('email_templates')->ignore($input['id'])],
                'content' => ['required', 'string'],
            ]);

            $model = EmailTemplate::findOrFail($input['id']);
            $flash_message = 'Email Template has been updated successfully.';
        }

        if ($validator->fails()) {
            Session::flash('flash_danger', $validator->messages());
            return redirect()->back()->withInput();
        }

        $model->fill($input);
        $model->save();
        $request->session()->flash('flash_success', $flash_message);
        return redirect('admin/email-templates');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!have_right(88))
            access_denied();

        $id = Hashids::decode($id)[0];
        $data['action'] = "Edit";
        $data['email_template'] = EmailTemplate::findOrFail($id);
        return view('admin.email-templates.form')->with($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!have_right(89))
            access_denied();

        $id = Hashids::decode($id)[0];
        EmailTemplate::destroy($id);
        Session::flash('flash_success', 'Email Template has been deleted successfully.');
        return redirect('admin/email-templates');
    }
}
