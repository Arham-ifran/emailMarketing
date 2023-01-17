<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Admin\Admin;
use App\Models\Admin\Role;
use Auth;
use Hashids;
use File;
use Storage;
use Session;
use Hash;
use DB;
use DataTables;
use App\Jobs\SendMail;
use App\Models\Admin\EmailTemplate;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        if (!have_right(5))
            access_denied();

        $data = [];
        $role_id = '';

        if ($request->has('role_id') && !empty($request->role_id)) {
            $role_id = $request->role_id;
        }

        $data['role_id'] = $role_id;

        if ($request->ajax()) {
            $db_record = Admin::whereNotIn('id', [1, auth()->user()->id]);

            if ($request->has('role_id') && !empty($request->role_id)) {
                $role_id = Hashids::decode($request->role_id)[0];
                $db_record = $db_record->where('role_id', $role_id);
            }

            $db_record = $db_record->orderBy('created_at', 'DESC');

            $datatable = Datatables::of($db_record);
            $datatable = $datatable->addIndexColumn();
            $datatable = $datatable->addColumn('role', function ($row) {
                return $row->role->name;
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

                if (have_right(7)) {
                    $actions .= '&nbsp;<a class="btn btn-primary" href="' . url("admin/admins/" . Hashids::encode($row->id) . '/edit') . '" title="Edit"><i class="fa fa-pencil-square-o"></i></a>';
                }

                if (have_right(8)) {
                    $actions .= '&nbsp;<form id="delete_' . Hashids::encode($row->id) . '" method="POST" action="' . url("admin/admins/" . Hashids::encode($row->id)) . '" accept-charset="UTF-8" style="display:inline">';
                    $actions .= '<input type="hidden" name="_method" value="DELETE">';
                    $actions .= '<input name="_token" type="hidden" value="' . csrf_token() . '">';
                    $actions .= '<button class="btn btn-danger" type="button" onclick=openDeletePopup("delete_' . Hashids::encode($row->id) . '") title="Delete">';
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

        return view('admin.admins.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!have_right(6))
            access_denied();
        $data['admin'] = new Admin();
        $data['roles'] = Role::where('status', 1)->whereNull('deleted_at')->get();
        $data['action'] = "Add";
        return view('admin.admins.form')->with($data);
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
            if (!have_right(6))
                access_denied();

            $validator = Validator::make($request->all(), [
                'email' => ['required', 'max:100', 'string', Rule::unique('admins')],
                'name' => ['required', 'max:100', 'string'],
                'password' => 'required|string|min:8|max:30',
            ]);

            if ($validator->fails()) {
                Session::flash('flash_danger', $validator->messages());
                return redirect()->back()->withInput();
            }

            $input['original_password'] = $input['password'];
            $input['password'] = Hash::make($input['password']);

            $model = new Admin();
            $model->fill($input);

            // ****************************************************//
            // Send Email About User Creation  //
            // *************************************************** //

            $email_template = EmailTemplate::where('type', 'admin_created_by_admin')->first();
            $email_template = transformEmailTemplateModel($email_template, 'en');
            $name = $model->name;
            $email = $model->email;
            $password = $model->original_password;
            $link = url('/admin/login');
            $subject = $email_template['subject'];
            $content = $email_template['content'];

            $search = array("{{name}}", "{{password}}", "{{link}}", "{{app_name}}");
            $replace = array($name, $password, $link, settingValue('site_title'));
            $content = str_replace($search, $replace, $content);

            SendMail::dispatch($email, $subject, $content);

            $flash_message = 'Admin user has been created successfully.';
        } else {
            if (!have_right(7))
                access_denied();

            $validator = Validator::make($request->all(), [
                'email' => ['required', 'string', Rule::unique('admins')->ignore($input['id'])],
                'name' => ['required', 'max:100', 'string'],
                'password' => 'required|string|min:8|max:30',
            ]);

            if ($validator->fails()) {
                Session::flash('flash_danger', $validator->messages());
                return redirect()->back()->withInput();
            }

            if (!empty($input['password'])) {
                $input['original_password'] = $input['password'];
                $input['password'] = Hash::make($input['password']);
            } else {
                unset($input['password']);
            }

            $model = Admin::findOrFail($input['id']);
            $model->fill($input);
            $flash_message = 'Admin user has been updated successfully.';
        }

        // $model->fill($input);
        $model->save();
        $request->session()->flash('flash_success', $flash_message);
        return redirect('admin/admins');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!have_right(7))
            access_denied();

        $id = Hashids::decode($id)[0];

        if ($id == 1)
            access_denied();

        $data['action'] = "Edit";
        $data['admin'] = Admin::findOrFail($id);
        $data['roles'] = Role::where('status', 1)->whereNull('deleted_at')->get();
        return view('admin.admins.form')->with($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function destroy($id)
    {
        if (!have_right(8))
            access_denied();

        $id = Hashids::decode($id)[0];
        Admin::destroy($id);
        Session::flash('flash_success', 'Admin user has been deleted successfully.');
        return redirect('admin/admins');
    }


    public function profile()
    {
        if (!have_right(9))
            access_denied();
        return view('admin.admins.profile');
    }

    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'required|string|max:100',
            'password' => 'required|string|min:8|max:30',
            'profile_image' => 'file|max:' . config('constants.file_size'),
        ]);

        if ($validator->fails()) {
            Session::flash('flash_danger', $validator->messages());
            return redirect()->back()->withInput();
        }

        $input = $request->all();
        $user = Auth::user();

        //MAKE DIRECTORY
        $upload_path = 'public/admins/profile-images';
        if (!File::exists(public_path() . '/public/admin-assets/img/profile-images')) {
            Storage::makeDirectory($upload_path);
        }

        if (!empty($request->files) && $request->hasFile('profile_image')) {
            $file = $request->file('profile_image');
            $type = $file->getClientOriginalExtension();
            if ($type == 'jpg' or $type == 'JPG' or $type == 'PNG' or $type == 'png' or $type == 'jpeg' or $type == 'JPEG' or $type == 'svg') {
                $file_temp_name = 'profile-image-' . uniqid() . '.' . $type;

                $old_file = public_path() . '/public/admin-assets/img/profile-images' . $user->profile_image;
                if (file_exists($old_file) && !empty($user->profile_image)) {
                    Storage::delete($upload_path . '/' . $user->profile_image);
                }
                $path = Storage::putFileAs($upload_path, $request->file('profile_image'), $file_temp_name);
                $input['profile_image'] = $file_temp_name;
            } else {
                Session::flash('flash_danger', 'The profile image must be a file of type: image/jpg,JPG,PNG,png,jpeg,JPEG,svg.');
                return redirect()->back()->withInput();
            }
        }

        $password = $request->input('password');

        if (!empty($password)) {
            $validator = Validator::make($request->all(), [
                'password' => 'required|string|min:8|max:30',
            ]);

            if ($validator->fails()) {
                Session::flash('flash_danger', $validator->messages());
                return redirect()->back()->withInput();
            }

            $input['original_password'] = $password;
            $input['password'] = Hash::make($password);
        } else {
            unset($input['password']);
        }

        $user->update($input);
        $request->session()->flash('flash_success', 'Profile has been updated successfully!');
        return redirect('admin/profile');
    }
}
