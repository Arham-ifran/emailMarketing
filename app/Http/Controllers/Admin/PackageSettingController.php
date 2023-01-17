<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin\PackageSetting;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Session;
use Hashids;
use Auth;
use Storage;

class PackageSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!have_right(110))
            access_denied();

        $data['package_settings'] = PackageSetting::where('status', 1)->orderBy('id', 'ASC')->get();
        return view('admin.package-settings.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //access_denied();

        if (!have_right(111))
            access_denied();

        $data['package_setting'] = new PackageSetting();
        $data['action'] = "Add";
        return view('admin.package-settings.form')->with($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!have_right(112))
            access_denied();

        $id = Hashids::decode($id)[0];
        $data['action'] = "Edit";
        $data['package_setting'] = PackageSetting::findOrFail($id);
        // $mailbox_volume = formatBytes($data['package_setting']['mailbox_migration_data_volume']);
        // $cloud_volume = formatBytes($data['package_setting']['cloud_migration_data_volume']);
        // $data['package_setting']['mailbox_migration_data_volume'] = str_replace(' ', '', $mailbox_volume);
        // $data['package_setting']['cloud_migration_data_volume'] = str_replace(' ', '', $cloud_volume);;
        return view('admin.package-settings.form')->with($data);
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
        // $input['mailbox_migration_data_volume'] = convertToByte($request->mailbox_migration_data_volume);
        // $input['cloud_migration_data_volume'] = convertToByte($request->cloud_migration_data_volume);

        if ($input['action'] == 'Add') {
            $messages = [
                // 'mailbox_migration_data_volume.regex' => 'Mailbox migration data volume should be like 1KB, 1MB, 1GB etc',
                // 'cloud_migration_data_volume.regex' => 'Cloud migration data volume should be like 1KB, 1MB, 1GB etc'
            ];

            $validator = Validator::make($request->all(), [
                'name' => ['required', 'max:200', 'string', Rule::unique('package_settings')],
                'info' => ['max:200'],
                // 'mailbox_migration_data_volume' => ['nullable', 'regex:/^(\d+[KB|MB|GB|TB|PB|EB|ZB|YB]{2}$)/i'],
                // 'cloud_migration_data_volume' => ['nullable', 'regex:/^(\d+[KB|MB|GB|TB|PB|EB|ZB|YB]{2}$)/i']
            ], $messages);

            $model = new PackageSetting();
            $input['status'] = 1;
            $flash_message = 'Package setting has been created successfully.';
        } else {
            $messages = [
                // 'mailbox_migration_data_volume.regex' => 'Mailbox migration data volume should be like 1KB, 1MB, 1GB etc',
                // 'cloud_migration_data_volume.regex' => 'Cloud migration data volume should be like 1KB, 1MB, 1GB etc'
            ];

            $validator = Validator::make($request->all(), [
                'name' => ['required', 'max:200', 'string', Rule::unique('package_settings')->ignore($input['id'])],
                'info' => ['max:200'],
                // 'mailbox_migration_data_volume' => ['nullable', 'regex:/^(\d+[KB|MB|GB|TB|PB|EB|ZB|YB]{2}$)/i'],
                // 'cloud_migration_data_volume' => ['nullable', 'regex:/^(\d+[KB|MB|GB|TB|PB|EB|ZB|YB]{2}$)/i']
            ], $messages);

            if ($input['id'] != 3 || $input['id'] != 6) {
                $next_row = PackageSetting::findOrFail($input['id'] + 1);
                $next_row->start_range = $input['end_range'] + 1;
                $next_row->save();
            }

            $model = PackageSetting::findOrFail($input['id']);
            $flash_message = 'Package setting has been updated successfully.';
        }

        if ($validator->fails()) {
            Session::flash('flash_danger', $validator->messages());
            return redirect()->back()->withInput();
        }

        $model->fill($input);
        $model->save();

        $request->session()->flash('flash_success', $flash_message);
        return redirect('admin/package-settings');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!have_right(113))
            access_denied();

        $id = Hashids::decode($id)[0];
        PackageSetting::destroy($id);
        Session::flash('flash_success', 'Package setting has been deleted successfully.');
        return redirect('admin/package-settings');
    }
}
