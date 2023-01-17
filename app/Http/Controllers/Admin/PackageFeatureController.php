<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin\PackageFeature;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Session;
use Hashids;
use Auth;
use Storage;

class PackageFeatureController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!have_right(21))
            access_denied();

        $data['package_features'] = PackageFeature::orderBy('created_at', 'DESC')->get();
        return view('admin.package-features.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //access_denied();

        if (!have_right(21))
            access_denied();

        $data['package_feature'] = new PackageFeature();
        $data['action'] = "Add";
        return view('admin.package-features.form')->with($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!have_right(22))
            access_denied();

        $id = Hashids::decode($id)[0];
        $data['action'] = "Edit";
        $data['package_feature'] = PackageFeature::findOrFail($id);
        return view('admin.package-features.form')->with($data);
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

        if ($input['action'] == 'Add') {
            $validator = Validator::make($request->all(), [
                'name' => ['required', 'max:200', 'string', Rule::unique('package_features')],
                'info' => ['max:200']
            ]);

            $model = new PackageFeature();
            $input['status'] = 1;
            $flash_message = 'Package feature has been created successfully.';
        } else {
            $validator = Validator::make($request->all(), [
                'name' => ['required', 'max:200', 'string', Rule::unique('package_features')->ignore($input['id'])],
                'info' => ['max:200']
            ]);

            $model = PackageFeature::findOrFail($input['id']);
            $flash_message = 'Package feature has been updated successfully.';
        }

        if ($validator->fails()) {
            Session::flash('flash_danger', $validator->messages());
            return redirect()->back()->withInput();
        }

        $model->fill($input);
        $model->save();

        $request->session()->flash('flash_success', $flash_message);
        return redirect('admin/package-features');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!have_right(22))
            access_denied();

        $id = Hashids::decode($id)[0];
        PackageFeature::destroy($id);
        Session::flash('flash_success', 'Package feature has been deleted successfully.');
        return redirect('admin/package-features');
    }
}
