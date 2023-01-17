<?php

namespace App\Http\Controllers\Api;

use App\CustomClasses\TranslationHandler;
use App\Http\Controllers\Controller;
use App\Http\Resources\EmailCampaignTemplateResource;
use App\Http\Resources\PublicEmailCampaignTemplateResource;
use App\Models\Admin\PublicEmailCampaignTemplate;
use App\Models\EmailCampaignTemplate;
use Carbon\Carbon;
use Hashids;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

//use Spatie\Browsershot\Browsershot;

class PublicEmailCampaignTemplateController extends Controller
{
    /**
     * Display a listing of the campaign reports.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $reports = PublicEmailCampaignTemplate::where('status', 1)->paginate(10);
        return PublicEmailCampaignTemplateResource::collection($reports)
            ->additional([
                'message' => 'Email template listing!',
                'status' => 1,
            ]);
    }

    /**
     * Import the specified resource from storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function import(Request $request, $id)
    {
        $id = Hashids::decode($id);
        if (isset($id[0])) {
            $EmailCampaignTemplate = PublicEmailCampaignTemplate::where('id', $id[0])->first()->toArray();
            $EmailCampaignTemplate['user_id'] = auth()->user()->id;
            $EmailCampaignTemplate['type'] = 1;
            EmailCampaignTemplate::create($EmailCampaignTemplate);
        }

        return response()->json([
            'status' => 1,
            'message' => TranslationHandler::getTranslation($request->lang, 'template_imported'),
        ]);
    }
}
