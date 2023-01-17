<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin\Package;
use App\Models\Admin\PackageSubscription;
use App\Models\Admin\PackageSetting;
use App\Models\Admin\PackageFeature;
use App\Models\Admin\PackageLinkFeature;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Session;
use Hashids;
use Auth;
use Storage;
use DataTables;
use Carbon\Carbon;

class PackageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!have_right(15))
            access_denied();

        $data = [];

        if ($request->ajax()) {
            $db_record = Package::where('deleted_at', NULL)->get();
            $datatable = Datatables::of($db_record);
            $datatable = $datatable->addIndexColumn();

            $datatable = $datatable->addColumn('type', function ($row) {
                $type = '';

                if ($row->id == 1)
                    $type = '<span class="label label-primary">Trial</span>';
                else if ($row->id == 2)
                    $type = '<span class="label label-primary">Free</span>';
                else {
                    $type = '<span class="label label-success">Paid</span>';
                }

                return $type;
            });

            $datatable = $datatable->editColumn('monthly_price', function ($row) {
                return '<sup>' . config('constants.currency')['symbol'] . '</sup>' . $row->monthly_price;
            });

            $datatable = $datatable->editColumn('yearly_price', function ($row) {
                return '<sup>' . config('constants.currency')['symbol'] . '</sup>' . $row->yearly_price;
            });

            $datatable = $datatable->addColumn('total_users', function ($row) {
                return '<a title="Users" href="' . url("admin/users?package_id=" . Hashids::encode($row->id)) . '"><span class="badge badge-light">' . $row->totalUsers() . '</span></a>';
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

                if (have_right(19)) {
                    $actions .= '&nbsp;<button type="button" onclick=openClonePopup("'. url("admin/packages/clone/" . Hashids::encode($row->id)) .'") class="btn btn-primary" title="Clone"><i class="fa fa-clone"></i></button>';
                }
                if (have_right(20)) {
                    $actions .= '&nbsp;<a class="btn btn-primary" title="Subscriptions" href="' . url("admin/packages/subscriptions/" . Hashids::encode($row->id)) . '"><i class="fa fa-tasks"></i></a>';
                }
                if (have_right(17)) {
                    $actions .= '&nbsp;<a class="btn btn-primary" href="' . url("admin/packages/" . Hashids::encode($row->id) . '/edit') . '" title="Edit"><i class="fa fa-pencil-square-o"></i></a>';
                }
                if (have_right(18) && !in_array($row->id, [1, 2, 9])) {
                    $actions .= '&nbsp;<form id="delete_'.Hashids::encode($row->id).'" method="POST" action="' . url("admin/packages/" . Hashids::encode($row->id)) . '" accept-charset="UTF-8" style="display:inline">';
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

            $datatable = $datatable->rawColumns(['type', 'monthly_price', 'yearly_price', 'total_users', 'status', 'action']);
            $datatable = $datatable->make(true);
            return $datatable;
        }

        return view('admin.packages.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!have_right(16))
            access_denied();

        $data['package'] = new Package();
        $data['packageFeatures'] = PackageFeature::where(['status' => 1, 'deleted_at' => NULL])->get();
        $data['action'] = "Add";
        return view('admin.packages.form')->with($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!have_right(17))
            access_denied();

        $id = Hashids::decode($id)[0];
        $data['action'] = "Edit";
        $data['package'] = Package::findOrFail($id);
        $data['packageFeatures'] = PackageFeature::where(['status' => 1, 'deleted_at' => NULL])->get();

        return view('admin.packages.form')->with($data);
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
                'title' => ['required', 'max:100', 'string', Rule::unique('packages')],
                'sub_title' => ['required', 'max:100', 'string'],
                'monthly_price' => ['required', 'numeric', 'max:10000'],
                'yearly_price' => ['required', 'numeric', 'max:10000'],
                'icon' => 'file|max:' . config('constants.file_size')
            ]);

            $model = new Package();
            $flash_message = 'Package has been created successfully.';
        } else {
            $validator = Validator::make($request->all(), [
                'title' => ['required', 'max:100', 'string', Rule::unique('packages')->ignore($input['id'])],
                'sub_title' => ['required', 'max:100', 'string'],
                'monthly_price' => ['required', 'numeric', 'max:10000'],
                'yearly_price' => ['required', 'numeric', 'max:10000'],
                'icon' => 'file|max:' . config('constants.file_size')
            ]);

            $model = Package::findOrFail($input['id']);
            $flash_message = 'Package has been updated successfully.';

            if ($model->status == 1 && $model->totalUsers() > 0 && $input['status'] == 0) {
                Session::flash('flash_danger', "You cannot disable this package because user(s) have already subscribed this package.");
                return redirect()->back()->withInput();
            } else {
                PackageLinkFeature::where('package_id', $model->id)->delete();
            }
        }

        if ($validator->fails()) {
            Session::flash('flash_danger', $validator->messages());
            return redirect()->back()->withInput();
        }

        if (!empty($request->files) && $request->hasFile('icon')) {
            $file = $request->file('icon');

            // *************** //
            // File Validation //
            // *************** //

            if ($file->getClientMimeType() != 'image/svg+xml') {
                Session::flash('flash_danger', 'The icon must be a file of type: image/svg+xml.');
                return redirect()->back();
            }

            // *********** //
            // Upload File //
            // *********** //

            $target_path = 'public/packages';
            $filename = 'icon-' . uniqid() . '.' . $file->getClientOriginalExtension();

            // **************** //
            // Delete Old File
            // **************** //

            if ($input['action'] == 'Edit') {
                $old_file = public_path() . '/storage/packages/' . $model->icon;
                if (file_exists($old_file) && !empty($model->icon)) {
                    Storage::delete($target_path . '/' . $model->icon);
                }
            }

            $path = $file->storeAs($target_path, $filename);
            $input['icon'] = $filename;
        }

        $model->fill($input);
        $model->save();

        if (isset($input['package_features'])) {
            foreach ($input['package_features'] as $key => $value) {
                PackageLinkFeature::create([
                    'package_id' => $model->id,
                    'feature_id' => $value,
                    'count' => $input['package_features_count'][$key]
                ]);
            }
        }

        $request->session()->flash('flash_success', $flash_message);
        return redirect('admin/packages');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!have_right(18))
            access_denied();

        $id = Hashids::decode($id)[0];
        $package = Package::find($id);
        if ($package->totalUsers() > 0) {
            Session::flash('flash_danger', "You cannot delete this package because user(s) have already subscribed this package.");
            return redirect('admin/packages');
        }

        Package::destroy($id);
        Session::flash('flash_success', 'Package has been deleted successfully.');
        return redirect('admin/packages');
    }

    /**
     * Show all users who has used this package.
     *
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function subscriptions(Request $request, $id)
    {
        if (!have_right(20))
            access_denied();

        $data = [];
        $data['id'] = $id;
        if (Hashids::decode($id) && Hashids::decode($id)[0])
            $id = Hashids::decode($id)[0];
        else
            $id = -1;

        $db_record = PackageSubscription::orderBy('created_at', 'DESC');
        if ($request->ajax()) {

            if ($request->has('userid') && $request->userid != "") {
                $userid = Hashids::decode($request->userid);
                if ($userid[0]) {
                    $db_record = $db_record->where('user_id', $userid[0]);
                } else {
                    $db_record = $db_record->where('id', -1);
                }
            } else {
                $db_record = User::select(
                    'users.name',
                    // 'users.login_location',
                    'users.last_active_at',
                    'users.package_subscription_id',
                    'package_subscriptions.*'
                )
                    ->join('package_subscriptions', function ($join) use ($id) {
                        $join->on('package_subscriptions.id', '=', 'users.package_subscription_id')
                            ->where('package_subscriptions.package_id', '=', $id);
                    })
                    ->orderBy('package_subscriptions.created_at', 'DESC');
            }

            $datatable = Datatables::of($db_record);
            $datatable = $datatable->addIndexColumn();
            $datatable = $datatable->addColumn('price', function ($row) {
                return '<sup>' . config('constants.currency')['symbol'] . '</sup>' . $row->price;
            });
            $datatable = $datatable->addColumn('start_date', function ($row) {
                return Carbon::createFromTimeStamp($row->start_date, "UTC")->tz(session('timezone'))->format('d M, Y');
            });
            $datatable = $datatable->addColumn('end_date', function ($row) {
                return (!empty($row->end_date)) ? Carbon::createFromTimeStamp($row->end_date, "UTC")->tz(session('timezone'))->format('d M, Y') : 'Lifetime';
            });
            $datatable = $datatable->editColumn('last_active_at', function ($row) {
                return (!empty($row->last_active_at)) ? Carbon::createFromTimeStamp($row->last_active_at, "UTC")->tz(session('timezone'))->format('d M, Y - h:i A') : 'N/A';
            });
            $datatable = $datatable->editColumn('status', function ($row) {
                $currentTimestamp = Carbon::now('UTC')->timestamp;
                $status = '';

                if (empty($row->end_date) || $row->end_date > $currentTimestamp)
                    $status = '<span class="label label-success">Active</span>';
                else
                    $status = '<span class="label label-warning">Expired</span>';

                return $status;
            });
            if ($request->has('userid') && $request->userid != "") {
                $userid = Hashids::decode($request->userid);
                if ($userid[0]) {
                    $uid = $userid[0];
                    $datatable = $datatable->addColumn('name', function () use ($uid) {
                        $user = User::where('id', $uid)->first();
                        return $user->name;
                    });
                }
            }

            $datatable = $datatable->rawColumns(['price', 'status']);
            $datatable = $datatable->make(true);
            return $datatable;
        }

        return view('admin.packages.subscriptions', $data);
    }

    /**
     * Clone package
     *
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function clone(Request $request, $id)
    {
        if (!have_right(19))
            access_denied();

        $data = [];
        $data['id'] = $id;
        $id = Hashids::decode($id)[0];

        $package = Package::find($id);
        $newPackage = $package->replicate();
        $newPackage->title = $package->title . ' - ' . 'Clone';
        $newPackage->status = 0;

        $filename = uniqid() . $package->icon;
        $old_file = public_path() . '/storage/packages/' . $package->icon;
        if (file_exists($old_file) && !empty($package->icon)) {
            \File::copy(public_path() . '/storage/packages/' . $package->icon, public_path() . '/storage/packages/' . $filename);
            $newPackage->icon = $filename;
        } else {
            $newPackage->icon = Null;
        }

        $newPackage->save();

        foreach ($package->linkedFeatures as $feature) {
            $newFeature = $feature->replicate();
            $newFeature->package_id = $newPackage->id;
            $newFeature->save();
        }

        Session::flash('flash_success', 'Package has been cloned successfully.');
        return redirect('admin/packages');
    }
}
