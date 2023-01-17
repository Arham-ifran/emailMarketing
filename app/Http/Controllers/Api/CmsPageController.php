<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\CmsPageResource;
use App\Models\Admin\CmsPage;

class CmsPageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function index()
    {
        $cmsPages = CmsPage::where('status', 1)->get();

        return response()->json([
            'data' => $cmsPages,
            'status' => 1,
            'message' => 'CMS Pages Listing'
        ], 200, ['Content-Type' => 'application/json']);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function detail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'slug' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->errors()->all()], 400, ['Content-Type' => 'application/json']);
        }

        // $cmsPage = CmsPage::where(['slug' => $request->input('slug'), 'status' => 1])->first();
        $cmsPage = CmsPage::where(['slug' => $request->input('slug'), 'status' => 1])->get();

        return response([
            'cmsPage' => $cmsPage,
            'cmsPage' => CmsPageResource::collection($cmsPage)[0],
            'message' => 'CMS Page Detail',
            'status'  => 1
        ], 200);
    }
}
