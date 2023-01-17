<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin\AboutUsTestimonial;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Session;
use Hashids;
use Auth;
use Storage;
use DataTables;

class AboutUsTestimonialController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!have_right(42))
            access_denied();

        $data = [];
        if ($request->ajax()) {
            $db_record = AboutUsTestimonial::orderBy('created_at', 'DESC'); //->get();
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

                if (have_right(44)) {
                    $actions .= '&nbsp;<a class="btn btn-primary" href="' . url("admin/about-us-testimonials/" . Hashids::encode($row->id) . '/edit') . '" title="Edit"><i class="fa fa-pencil"></i></a>';
                }
                if (have_right(45)) {
                    $actions .= '&nbsp;<form id="delete_'.Hashids::encode($row->id).'" method="POST" action="' . url("admin/about-us-testimonials/" . Hashids::encode($row->id)) . '" accept-charset="UTF-8" style="display:inline">';
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

            $datatable = $datatable->rawColumns(['status', 'action']);
            $datatable = $datatable->make(true);
            return $datatable;
        }
        return view('admin.about-us-testimonials.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!have_right(43))
            access_denied();

        $data['model'] = new AboutUsTestimonial();
        $data['action'] = "Add";
        return view('admin.about-us-testimonials.form')->with($data);
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

        $rules = [
            'name' => ['required', 'string', 'max:250'],
            'message' => ['required', 'string', 'max:1000'],
        ];

        if ($input['action'] == 'Add') {
            $validator = Validator::make($request->all(), $rules);

            $model = new AboutUsTestimonial();
            $flash_message = 'Testimonial has been created successfully.';
        } else {
            $validator = Validator::make($request->all(), $rules);

            $model = AboutUsTestimonial::findOrFail($input['id']);
            $flash_message = 'Testimonial has been updated successfully.';
        }

        if ($validator->fails()) {
            Session::flash('flash_danger', $validator->messages());
            return redirect()->back()->withInput();
        }

        $model->fill($input);
        $model->save();

        $request->session()->flash('flash_success', $flash_message);
        return redirect('admin/about-us-testimonials');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!have_right(44))
            access_denied();

        $id = Hashids::decode($id)[0];
        $data['action'] = "Edit";
        $data['model'] = AboutUsTestimonial::findOrFail($id);
        return view('admin.about-us-testimonials.form')->with($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!have_right(45))
            access_denied();

        $id = Hashids::decode($id)[0];
        AboutUsTestimonial::destroy($id);

        Session::flash('flash_success', 'testimonial has been deleted successfully.');
        return redirect('admin/about-us-testimonials');
    }
}
