<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin\ContactUsQuery;
use Session;
use Hashids;
use Auth;
use DataTables;

class ContactUsQueryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!have_right(94))
            access_denied();

        $data = [];
        if ($request->ajax()) {
            // dd("here000");

            $db_record = ContactUsQuery::orderBy('created_at', 'DESC'); //->get();
            $datatable = Datatables::of($db_record);
            $datatable = $datatable->addIndexColumn();

            $datatable = $datatable->editColumn('status', function ($row) {
                $status = '<span class="label label-danger">Pending</span>';
                if ($row->status == 1) {
                    $status = '<span class="label label-success">Completed</span>';
                }
                return $status;
            });

            $datatable = $datatable->addColumn('action', function ($row) {
                $actions = '<span class="actions">';

                if (have_right(96)) {
                    $actions .= '&nbsp;<a class="btn btn-primary" href="' . url("admin/contact-us-queries/" . Hashids::encode($row->id) . '/edit') . '" title="Edit"><i class="fa fa-pencil-square-o"></i></a>';
                }

                $actions .= '</span>';
                return $actions;
            });

            $datatable = $datatable->rawColumns(['status', 'action']);
            $datatable = $datatable->make(true);
            return $datatable;
        }
        return view('admin.contact-us-queries.index', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $model = ContactUsQuery::findOrFail($request->id);
        $model->fill($request->all());
        $model->save();
        $request->session()->flash('flash_success', 'Query status has been updated successfully.');
        return redirect('admin/contact-us-queries');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!have_right(96))
            access_denied();

        $id = Hashids::decode($id)[0];
        $data['action'] = "Edit";
        $data['query'] = ContactUsQuery::findOrFail($id);
        return view('admin.contact-us-queries.form')->with($data);
    }
}
