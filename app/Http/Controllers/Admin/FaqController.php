<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin\Faq;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Session;
use Hashids;
use Auth;
use DataTables;

class FaqController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!have_right(29))
            access_denied();

        $data = [];
        if ($request->ajax()) {
            $db_record = Faq::orderBy('created_at', 'DESC'); //->get();
            $datatable = Datatables::of($db_record);
            $datatable = $datatable->addIndexColumn();

            $datatable = $datatable->editColumn('status', function ($row) {
                $status = '<span class="label label-danger">Disable</span>';
                if ($row->status == 1) {
                    $status = '<span class="label label-success">Active</span>';
                }
                return $status;
            });

            $datatable = $datatable->addColumn('action', function ($row) {
                $actions = '<span class="actions">';

                if (have_right(31)) {
                    $actions .= '&nbsp;<a class="btn btn-primary" href="' . url("admin/faqs/" . Hashids::encode($row->id) . '/edit') . '" title="Edit"><i class="fa fa-pencil-square-o"></i></a>';
                }
                if (have_right(32)) {
                    $actions .= '&nbsp;<form id="delete_'.Hashids::encode($row->id).'" method="POST" action="' . url("admin/faqs/" . Hashids::encode($row->id)) . '" accept-charset="UTF-8" style="display:inline">';
                    $actions .= '<input type="hidden" name="_method" value="DELETE">';
                    $actions .= '<input name="_token" type="hidden" value="' . csrf_token() . '">';
                    // $actions .= '<button class="btn btn-danger" onclick="return confirm(\'Are you sure you want to delete this record?\');" title="Delete">';
                    $actions .= '<button class="btn btn-danger" type="button" onclick=openDeletePopup("delete_'.Hashids::encode($row->id).'") title="Delete">';
                    $actions .= '<i class="fa fa-trash"></i>';
                    $actions .= '</button>';
                    $actions .= '</form>';
                }

                $actions .= '</span>';
                return $actions;
            });

            $datatable = $datatable->rawColumns(['status', 'action']);
            $datatable = $datatable->make(true);
            return $datatable;
        }

        return view('admin.faqs.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!have_right(30))
            access_denied();

        $data['faq'] = new Faq();
        $data['action'] = "Add";
        return view('admin.faqs.form')->with($data);
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

        $validator = Validator::make($request->all(), [
            'question' => ['required', 'max:200', 'string'],
            'answer' => ['required', 'max:1000', 'string'],
        ]);

        if ($validator->fails()) {
            Session::flash('flash_danger', $validator->messages());
            return redirect()->back()->withInput();
        }

        if ($input['action'] == 'Add') {
            $model = new Faq();
            $flash_message = 'FAQ has been created successfully.';
        } else {
            $model = Faq::findOrFail($input['id']);
            $flash_message = 'FAQ has been updated successfully.';
        }

        $model->fill($input);
        $model->save();
        $request->session()->flash('flash_success', $flash_message);
        return redirect('admin/faqs');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function edit($id)
    {
        if (!have_right(31))
            access_denied();

        if (!isset(Hashids::decode($id)[0]))
            abort(404);

        $id = Hashids::decode($id)[0];
        $data['action'] = "Edit";
        $data['faq'] = Faq::findOrFail($id);
        return view('admin.faqs.form')->with($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!have_right(32))
            access_denied();

        $id = Hashids::decode($id)[0];
        Faq::destroy($id);
        Session::flash('flash_success', 'FAQ has been deleted successfully.');
        return redirect('admin/faqs');
    }
}
