<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin\HomeContent;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Session;
use Hashids;
use Auth;
use Storage;
use DataTables;

class HomeContentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!have_right(70))
            access_denied();

        $data = [];
        if($request->ajax())
        {
            $db_record = HomeContent::orderBy('created_at','DESC');//->get();
            $datatable = Datatables::of($db_record);
            $datatable = $datatable->addIndexColumn();
            $datatable = $datatable->editColumn('status', function($row)
            {
                $status = '<span class="label label-danger">Disable</span>';
                if ($row->status == 1)
                {
                    $status = '<span class="label label-success">Active</span>';
                }
                return $status;
            });

            $datatable = $datatable->addColumn('action', function($row)
            {
                $actions = '<span class="actions">';
                
                if(have_right(72))
                {
                    $actions .= '&nbsp;<a class="btn btn-primary" href="'.url("admin/home-contents/" . Hashids::encode($row->id).'/edit').'" title="Edit"><i class="fa fa-pencil"></i></a>';
                }
                if(have_right(73))
                {
                    $actions .= '&nbsp;<form id="delete_'.Hashids::encode($row->id).'" method="POST" action="'.url("admin/home-contents/" . Hashids::encode($row->id)).'" accept-charset="UTF-8" style="display:inline">';
                    $actions .= '<input type="hidden" name="_method" value="DELETE">';
                    $actions .= '<input name="_token" type="hidden" value="'.csrf_token().'">';
                    $actions .= '<button class="btn btn-danger" type="button" onclick=openDeletePopup("delete_'.Hashids::encode($row->id).'") title="Delete">';
                    $actions .= '<i class="fa fa-trash"></i>';
                    $actions .= '</button>';
                    $actions .= '</form>';
                }

                $actions .= '</span>';
                return $actions;
            });

            $datatable = $datatable->rawColumns(['status','action']);
            $datatable = $datatable->make(true);
            return $datatable;
        }
        return view('admin.home-contents.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!have_right(71))
            access_denied();

        $data['model'] = new HomeContent();
        $data['action'] = "Add";
        return view('admin.home-contents.form')->with($data);
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
            'name' => ['required','string','max:250'],
            'description' => ['required','string'],
            'image' => 'file|mimes:jpeg,jpg,png|max:'.config('constants.file_size')
        ]);

        if ($validator->fails())
        {
            Session::flash('flash_danger', $validator->messages());
            return redirect()->back()->withInput();
        }

        if($input['action'] == 'Add')
        {
            $model = new HomeContent();            
            $flash_message = 'Content has been created successfully.';
        }
        else
        {
            $model = HomeContent::findOrFail($input['id']);
            $flash_message = 'Content has been updated successfully.';
        }

        if (!empty($request->files) && $request->hasFile('image'))
        {
            $file = $request->file('image');

            // *********** //
            // Upload File //
            // *********** //

            $target_path = 'public/home-contents';
            $filename = 'home-content-'.$file->getClientOriginalName();

            // **************** //
            // Delete Old File
            // **************** //

            if($input['action'] == 'Edit')
            {
                $old_file = public_path() . '/storage/home-contents/' . $model->image;
                if (file_exists($old_file) && !empty($model->image)) 
                {
                    Storage::delete($target_path . '/' . $model->image);
                }
            }

            $path = $file->storeAs($target_path, $filename);
            $input['image'] = $filename;
        }

        $model->fill($input);
        $model->save();

        $request->session()->flash('flash_success', $flash_message);
        return redirect('admin/home-contents');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(!have_right(72))
            access_denied();

        $id = Hashids::decode($id)[0];
        $data['action'] = "Edit";
        $data['model'] = HomeContent::findOrFail($id);
        return view('admin.home-contents.form')->with($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(!have_right(73))
            access_denied();

        $id = Hashids::decode($id)[0];
        HomeContent::destroy($id);

        Session::flash('flash_success', 'Content has been deleted successfully.');
        return redirect('admin/home-contents');
    }
}