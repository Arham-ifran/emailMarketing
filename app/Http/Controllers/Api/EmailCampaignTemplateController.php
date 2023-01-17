<?php

namespace App\Http\Controllers\Api;

use App\CustomClasses\TranslationHandler;
use App\Http\Controllers\Controller;
use App\Http\Resources\EmailCampaignTemplateResource;
use App\Models\EmailCampaign;
use App\Models\EmailCampaignTemplate;
use Carbon\Carbon;
use Hashids;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

//use Spatie\Browsershot\Browsershot;

class EmailCampaignTemplateController extends Controller
{
    /**
     * Create a new EmailCampaignTemplateController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['addImage']]);
    }

    /**
     * Save email campaign template
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveEmailTemplate(Request $request)
    {
        $input = $request->all();
        $messages = [
            'name.required' => TranslationHandler::getTranslation($request->lang, 'required'),
        ];

        $validation_rules = [
            'name' => 'required|string|max:250',
            'image' => 'required',
        ];
        $request->validate($validation_rules, $messages);

        $base64_image = $request->input('image'); // your base64 encoded     
        @list($type, $file_data) = explode(';', $base64_image);
        @list(, $file_data) = explode(',', $file_data);
        $imageName = 'template-' . Carbon::now()->timestamp . rand(111111111, 999999999) . '.' . 'png';
        $target_path = 'public';
        Storage::disk($target_path)->put($imageName, base64_decode($file_data));

        $input['image'] = '/storage/' . $imageName;

        $id = "";
        if (isset($input['id']) && $input['id'] != "")
            $id = Hashids::decode($input['id'])[0];
        $input['content'] = json_encode($input['content']);
        $emailCampaignTemplate = EmailCampaignTemplate::updateOrCreate(
            [
                'user_id' => auth()->user()->id,
                'id' => $id,
            ],
            $input
        );

        return response()->json([
            'data' => $emailCampaignTemplate->makeHidden(['id', 'content']),
            'status' => 1,
            'message' => TranslationHandler::getTranslation($request->lang, 'template_created'),
        ], 200, ['Content-Type' => 'application/json']);
    }

    /**
     * Edit email campaign template.
     * @var id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Request $request)
    {

        $result = EmailCampaignTemplate::where('user_id', auth()->user()->id)->find(Hashids::decode($request->_id)[0]);
        return response()->json([
            'data' => $result->makeHidden(['id']),
            'status' => 1,
            'message' => "Edit Campaign!",
        ], 200, ['Content-Type' => 'application/json']);
    }

    /**
     * Edit email campaign template.
     * @var id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTemplate($id)
    {
        $result = EmailCampaignTemplate::find($id);
        return $result;
    }

    /**
     * Display a listing of the campaign reports.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    //public function campaignReportListing(Request $request)
    public function index(Request $request)
    {
        $reports = EmailCampaignTemplate::where('user_id', auth()->user()->id);
        $reports = $reports->orderBy('created_at', 'DESC')->paginate(10);
        return EmailCampaignTemplateResource::collection($reports)
            ->additional([
                'message' => 'Email template listing!',
                'status' => 1,
            ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        $id = Hashids::decode($id);
        if (isset($id[0])) {
            $EmailCampaignTemplate = EmailCampaignTemplate::where(['id' => $id[0], 'user_id' => auth()->user()->id])->first();
            $camp = EmailCampaign::where('template_id', $id[0])->where('status', '!=', 2)->first();
            if ($EmailCampaignTemplate && !($camp))
                $EmailCampaignTemplate->delete();
            else
                return response()->json([
                    'status' => 0,
                    'message' => "cannot delete, template in use",
                ]);
        }

        return response()->json([
            'status' => 1,
            'message' => TranslationHandler::getTranslation($request->lang, 'template_deleted'),
        ]);
    }

    public function addImage(Request $request)
    {
        $input = $request->all();

        $rules = [
            'file' => 'file|mimes:jpeg,jpg,png|max:' . config('constants.file_size'),
        ];
        $validator = Validator::make($request->all(), $rules);

        $img_link = "";
        $img = "";
        if (!empty($request->files) && $request->hasFile('file')) {
            $file = $request->file('file');
            // Upload File //
            $target_path = 'public/about-us-page';
            $filename = 'feature-' . $file->getClientOriginalName();
            $path = $file->storeAs($target_path, $filename);
            $img_link = url('/') . '/storage/about-us-page/' . $filename;
            $img = '/storage/about-us-page/' . $filename;
        }

        return response([
            'image_url' => $img_link,
            'success' => true
        ], 200);
    }
}
