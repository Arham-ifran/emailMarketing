<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin\AboutUsContentLabel;
use App\Models\Admin\AboutUsContent;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Session;
use Hashids;
use Auth;
use DataTables;

class AboutUsLabelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!have_right(118))
            access_denied();

        $data = [];
        $data['about_us_contents'] = AboutUsContent::where('status', 1)->get();

        if ($request->ajax()) {
            $db_record = AboutUsContentLabel::where('status', 1);

            if ($request->has('about_us_content_id') && !empty($request->about_us_content_id)) {
                $db_record = $db_record->where('about_us_content_id', $request->about_us_content_id);
            }

            //$db_record = $db_record->get();

            $datatable = Datatables::of($db_record);
            $datatable = $datatable->addIndexColumn();

            $datatable = $datatable->addColumn('about_us_content', function ($row) {
                return $row->AboutUsContent->name;
            });

            $datatable = $datatable->addColumn('action', function ($row) {
                $actions = '<span class="actions">';

                if (have_right(120)) {
                    $actions .= '&nbsp;<a class="btn btn-primary" href="' . url("admin/about-us-labels/" . Hashids::encode($row->id) . '/edit') . '" title="Edit"><i class="fa fa-pencil-square-o"></i></a>';
                }
                if (have_right(121)) {
                    $actions .= '&nbsp;<form id="delete_'.Hashids::encode($row->id).'" method="POST" action="' . url("admin/about-us-labels/" . Hashids::encode($row->id)) . '" accept-charset="UTF-8" style="display:inline">';
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

            $datatable = $datatable->rawColumns(['action']);
            $datatable = $datatable->make(true);
            return $datatable;
        }

        return view('admin.about-us-content-labels.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!have_right(119))
            access_denied();

        $data['model'] = new AboutUsContentLabel();
        $data['action'] = "Add";
        $data['about_us_contents'] = AboutUsContent::where('status', 1)->get();
        return view('admin.about-us-content-labels.form')->with($data);
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

        $validator = Validator::make($request->all(), [
            'about_us_content_id' => ['required'],
            'label' => ['required'],
            'value' => ['required'],
        ]);

        if ($validator->fails()) {
            Session::flash('flash_danger', $validator->messages());
            return redirect()->back()->withInput();
        }

        if ($input['action'] == 'Add') {
            $values = $input['value'];
            foreach ($input['label'] as $key => $label) {
                $value = $values[$key];

                if ($label != NULL && $value != NULL) {

                    $input['label']  = $label;
                    $input['value']  = $value;

                    $model = new AboutUsContentLabel();

                    $model->fill($input);
                    $model->save();
                }
            }

            $flash_message = 'About Us Labels have been created successfully.';
        } else {
            $input['label']  = $input['label'][0];
            $input['value']  = $input['value'][0];

            $model = AboutUsContentLabel::findOrFail($input['id']);
            $model->fill($input);
            $model->save();

            $flash_message = 'About Us Label has been updated successfully.';
        }

        $request->session()->flash('flash_success', $flash_message);
        return redirect('admin/about-us-labels');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function edit($id)
    {
        if (!have_right(120))
            access_denied();

        if (!isset(Hashids::decode($id)[0]))
            abort(404);

        $id = Hashids::decode($id)[0];
        $data['action'] = "Edit";
        $data['about_us_contents'] = AboutUsContent::where('status', 1)->get();
        $data['model'] = AboutUsContentLabel::findOrFail($id);
        return view('admin.about-us-content-labels.form')->with($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!have_right(121))
            access_denied();

        $id = Hashids::decode($id)[0];
        AboutUsContentLabel::destroy($id);
        Session::flash('flash_success', 'About Us Label has been deleted successfully.');
        return redirect('admin/about-us-labels');
    }
}
