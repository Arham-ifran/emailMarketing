<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin\Country;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Session;
use Hashids;
use Auth;
use DataTables;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!have_right(61))
            access_denied();

        $data = [];
        if ($request->ajax()) {
            $db_record = Country::all();

            $datatable = Datatables::of($db_record);
            $datatable = $datatable->addIndexColumn();

            $datatable = $datatable->editColumn('apply_default_vat', function ($row) {
                $apply_default_vat = '<span class="label label-danger">No</span>';
                if ($row->apply_default_vat == 1) {
                    $apply_default_vat = '<span class="label label-success">Yes</span>';
                }
                return $apply_default_vat;
            });

            $datatable = $datatable->editColumn('status', function ($row) {
                $status = '<span class="label label-danger">Disable</span>';
                if ($row->status == 1) {
                    $status = '<span class="label label-success">Active</span>';
                }
                return $status;
            });

            $datatable = $datatable->addColumn('action', function ($row) {
                $actions = '<span class="actions">';

                if (have_right(63)) {
                    $actions .= '&nbsp;<a class="btn btn-primary" href="' . url("admin/countries/" . Hashids::encode($row->id) . '/edit') . '" title="Edit"><i class="fa fa-pencil-square-o"></i></a>';
                }
                if (have_right(64)) {
                    $actions .= '&nbsp;<form id="delete_'.Hashids::encode($row->id).'" method="POST" action="' . url("admin/countries/" . Hashids::encode($row->id)) . '" accept-charset="UTF-8" style="display:inline">';
                    $actions .= '<input type="hidden" name="_method" value="DELETE">';
                    $actions .= '<input name="_token" type="hidden" value="' . csrf_token() . '">';
                    $actions .= '<button class="btn btn-danger" type="button" onclick=openDeletePopup("delete_'.Hashids::encode($row->id).'") title="Delete">';
                    $actions .= '<i class="fa fa-trash"></i>';
                    $actions .= '</button>';
                    $actions .= '</form>';
                }

                $actions .= '</span>';
                return $actions;
            });

            $datatable = $datatable->rawColumns(['apply_default_vat', 'status', 'action']);
            $datatable = $datatable->make(true);
            return $datatable;
        }

        return view('admin.countries.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!have_right(62))
            access_denied();

        $data['country'] = new Country();
        $data['action'] = "Add";
        return view('admin.countries.form')->with($data);
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
                'name' => ['required', 'max:200', 'string', Rule::unique('countries')],
                'code' => ['required', 'max:2', 'string', Rule::unique('countries')],
                'vat' => ['required', 'numeric', 'min:0', 'max:100'],
            ]);

            if ($validator->fails()) {
                Session::flash('flash_danger', $validator->messages());
                return redirect()->back()->withInput();
            }

            $model = new Country();
            $flash_message = 'Country has been created successfully.';
        } else {
            $validator = Validator::make($request->all(), [
                'name' => ['required', 'max:200', 'string', Rule::unique('countries')->ignore($input['id'])],
                'code' => ['required', 'max:2', 'string', Rule::unique('countries')->ignore($input['id'])],
                'vat' => ['required', 'numeric', 'min:0', 'max:100'],
            ]);

            if ($validator->fails()) {
                Session::flash('flash_danger', $validator->messages());
                return redirect()->back()->withInput();
            }

            $model = Country::findOrFail($input['id']);
            $flash_message = 'Country has been updated successfully.';
        }

        $model->fill($input);
        $model->save();
        $request->session()->flash('flash_success', $flash_message);
        return redirect('admin/countries');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function edit($id)
    {
        if (!have_right(63))
            access_denied();

        if (!isset(Hashids::decode($id)[0]))
            abort(404);

        $id = Hashids::decode($id)[0];
        $data['action'] = "Edit";
        $data['country'] = Country::findOrFail($id);
        return view('admin.countries.form')->with($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!have_right(64))
            access_denied();

        $id = Hashids::decode($id)[0];
        Country::destroy($id);
        Session::flash('flash_success', 'Country has been deleted successfully.');
        return redirect('admin/countries');
    }

    public function getCountries()
    {
        $countries = Country::get();
        return response()->json(['countries' => $countries], 200);
    }

    public function getCountry($id)
    {
        $country = Country::where('id', $id)->first();
        return response()->json(['country' => $country], 200);
    }
}
