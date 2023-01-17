<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\LanguageModule;
use Session;
use Hashids;
use Auth;
use DataTables;

class LanguageModuleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!have_right(100))
            access_denied();

        $data = [];
        if($request->ajax())
        {
            $db_record = LanguageModule::where('status',1);

            $datatable = Datatables::of($db_record);
            $datatable = $datatable->addIndexColumn();
            $datatable = $datatable->make(true);
            return $datatable;
        }

        return view('admin.language-modules.index',$data);
    }
}
