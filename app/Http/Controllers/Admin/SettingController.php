<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Session;
use DB;
use File;
use Storage;

class SettingController extends Controller
{
    public function index()
    {
        if (!have_right(10))
            access_denied();
        $result = DB::table('settings')->get()->toArray();
        $row = [];
        foreach ($result as $value) {
            $row[$value->option_name] = $value->option_value;
        }
        $data['settings'] = $row;
        return view('admin.settings')->with($data);
    }

    public function updateSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'site_title' => 'required|string|max:200',
            'office_address' => 'required|string|max:1000',
            'contact_number' => 'required|string|max:50',
            'contact_email' => 'required|string|max:200',
            'operating_hours' => 'max:1000',
            'instagram' => 'max:200',
            'facebook' => 'max:200',
            'twitter' => 'max:200',
            'dribbble' => 'max:200',
            'behance' => 'max:200',
            'linkedin' => 'max:200',
            'number_of_days' => 'numeric|min:0|max:100',
            'vat' => 'numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            Session::flash('flash_danger', $validator->messages());
            return redirect()->back()->withInput();
        }

        $input = $request->all();
        unset($input['_token']);

        foreach ($input as $key => $value) {
            $result = DB::table('settings')->where('option_name', $key)->get();

            if ($result->isEmpty()) {
                DB::table('settings')->insert(['option_name' => $key, 'option_value' => $value]);
            } else {
                DB::table('settings')->where('option_name', $key)->update(['option_value' => $value]);
            }
        }
        Session::flash('flash_success', 'Site Settings has been updated successfully.');
        return redirect()->back();
    }
}
