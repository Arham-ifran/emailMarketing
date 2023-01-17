<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin\EmailTemplate;
use App\Models\Admin\PublicEmailCampaignTemplate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Session;
use Hashids;
use Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class EmailCampaignTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!have_right(82))
            access_denied();

        $data['email_templates'] = PublicEmailCampaignTemplate::orderBy('created_at', 'ASC')->get();
        return view('admin.email-campaign-templates.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //access_denied();
        if (!have_right(83))
            access_denied();

        $data['email_template'] = new PublicEmailCampaignTemplate();
        $data['action'] = "Add";
        return view('admin.email-campaign-templates.form')->with($data);
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

        // handle image in both cases
        if ($request->input('image')) {
            $base64_image = $request->input('image'); // your base64 encoded     
            @list($type, $file_data) = explode(';', $base64_image);
            @list(, $file_data) = explode(',', $file_data);
            $imageName = 'template-' . Carbon::now()->timestamp . rand(111111111, 999999999) . '.' . 'png';
            $target_path = 'public';
            Storage::disk($target_path)->put($imageName, base64_decode($file_data));
            $input['image'] = '/storage/' . $imageName;
        } else {
            $input['image'] = $input['old_image'];
        }

        if ($input['action'] == 'Add') {
            $validator = Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:250'],
            ]);

            $model = new PublicEmailCampaignTemplate();
            $model->fill($input);
            $flash_message = 'Email Campaign Template has been created successfully.';
        } else {
            $validator = Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:250'],
            ]);

            $model = PublicEmailCampaignTemplate::findOrFail($input['id']);
            $model->update($input);
            $flash_message = 'Email Campaign Template has been updated successfully.';
        }

        if ($validator->fails()) {
            Session::flash('flash_danger', $validator->messages());
            return redirect()->back()->withInput();
        }

        // $model->fill($input);
        $model->save();
        $request->session()->flash('flash_success', $flash_message);
        return redirect('admin/email-campaign-templates');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!have_right(84))
            access_denied();

        $id = Hashids::decode($id)[0];
        $data['action'] = "Edit";
        $data['email_template'] = PublicEmailCampaignTemplate::findOrFail($id);
        return view('admin.email-campaign-templates.form')->with($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!have_right(85))
            access_denied();

        $id = Hashids::decode($id)[0];
        PublicEmailCampaignTemplate::destroy($id);
        Session::flash('flash_success', 'Email Campaign Template has been deleted successfully.');
        return redirect('admin/email-campaign-templates');
    }
}
