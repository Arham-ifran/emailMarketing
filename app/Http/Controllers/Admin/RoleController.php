<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin\Role;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Session;
use Hashids;
use Auth;
use DataTables;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!have_right(1))
            access_denied();

        $data = [];
        if($request->ajax())
        {
            $db_record = Role::orderBy('created_at','DESC');//->get();
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

            $datatable = $datatable->addColumn('sub_admins', function($row)
            {   
                return '<a title="Sub Admins" href="'.url("admin/admins?role_id=".Hashids::encode($row->id)).'"><span class="badge badge-light">'.count($row->subAdmins).'</span></a>';
            });

            $datatable = $datatable->addColumn('action', function($row)
            {
                $actions = '<span class="actions">';

                if(have_right(3) && $row->id != 1 && auth()->user()->role_id != $row->id)
                {
                    $actions .= '&nbsp;<a class="btn btn-primary" href="'.url("admin/roles/" . Hashids::encode($row->id).'/edit').'" title="Edit"><i class="fa fa-pencil-square-o"></i></a>';
                }

                if(have_right(4) && $row->id != 1 && auth()->user()->role_id != $row->id)
                {
                    $actions .= '&nbsp;<form id="delete_'.Hashids::encode($row->id).'" method="POST" action="'.url("admin/roles/" . Hashids::encode($row->id)).'" accept-charset="UTF-8" style="display:inline">';
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

            $datatable = $datatable->rawColumns(['status','action','sub_admins']);
            $datatable = $datatable->make(true);
            return $datatable;
        }
        return view('admin.roles.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!have_right(2))
            access_denied();

        $data['role'] = new Role();
        $data['action'] = "Add";
        return view('admin.roles.form')->with($data);
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
        $input['right_ids'] = $request->has('right_ids') ? implode(",",$request->right_ids) : NULL;

        if($input['action'] == 'Add')
        {
            $validator = Validator::make($request->all(), [
                'name' => ['required','string','max:100',Rule::unique('roles')]
            ]);

            $model = new Role();         
            $flash_message = 'Role has been created successfully.';
        }
        else
        {
            $validator = Validator::make($request->all(), [
                'name' => ['required','string','max:100',Rule::unique('roles')->ignore($input['id'])]
            ]);

            $model = Role::findOrFail($input['id']);
            $flash_message = 'Role has been updated successfully.';
        }

        if ($validator->fails())
        {
            Session::flash('flash_danger', $validator->messages());
            return redirect()->back()->withInput();
        }

        $model->fill($input);
        $model->save();

        if(auth()->user()->role_id == $model->id && $model->status == 0)
        {
            Auth::guard('admin')->logout();
            return redirect()->route('admin.auth.login')->withErrors(['error' => 'Your role has been disabled.']);
        }

        $request->session()->flash('flash_success', $flash_message);
        return redirect('admin/roles');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(!have_right(3))
        access_denied();

        $id = Hashids::decode($id)[0];
        
        if($id == 1)
            access_denied();

        $data['action'] = "Edit";
        $data['role'] = Role::findOrFail($id);
        return view('admin.roles.form')->with($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(!have_right(4))
        access_denied();

        $id = Hashids::decode($id)[0];

        if($id == 1)
            access_denied();

        $role = Role::find($id);
        if(count($role->subAdmins) > 0)
        {
            Session::flash('flash_danger', "You cannot delete this role because admin user(s) have already registered with this role.");
            return redirect('admin/roles');
        }

        Role::destroy($id);
        Session::flash('flash_success', 'Role has been deleted successfully.');
        return redirect('admin/roles');
    }
}