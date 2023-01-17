<?php

namespace App\Http\Controllers\Api;

use App\CustomClasses\TranslationHandler;
use App\Http\Controllers\Controller;
use App\Models\SmsTemplate;
use App\Models\User_log;
use Illuminate\Http\Request;
use Hashids;

class SmsTemplatesController extends Controller
{
    /**
     * Create a new Group instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $id = auth()->user()->id;
        $SmsTemplates = SmsTemplate::where('user_id', $id)->orderBy('created_at', 'DESC')->paginate(10);

        return response([
            'data' => $SmsTemplates,
            'message' => 'Groups',
            'status' => 1,
        ], 200);
    }

    /**
     * Store a created/updated resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $messages = [
            'required' => TranslationHandler::getTranslation($request->lang, 'required'),
            'string' => TranslationHandler::getTranslation($request->lang, 'required'),
            'max' => TranslationHandler::getTranslation($request->lang, 'max_250'),
        ];

        $data = $request->validate([
            'name' => ['required', 'string', 'max:250'],
            'message' => ['required', 'string', 'max:250'],
        ], $messages);

        $data['user_id'] = auth()->user()->id;

        $id = "";
        if (isset($request->id)) {
            $id = Hashids::decode($request->id)[0];
        }
        $SmsTemplate = SmsTemplate::updateOrCreate(
            [
                'id' => $id
            ],
            $data
        );
        User_log::create([
            'user_id' => auth()->user()->id,
            'item_id' => $SmsTemplate->id,
            'log_type' => $id == '' ? 7 : 6,
            'module' => 7,
        ]);

        return response([
            'data' => $SmsTemplate,
            'message' => $id == '' ? TranslationHandler::getTranslation($request->lang, 'template_created') : TranslationHandler::getTranslation($request->lang, 'template_updated'),
            'status' => 1,
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $id = Hashids::decode($id);
        if (isset($id[0])) {
            $SmsTemplate = SmsTemplate::where('id', $id[0])->first();
            if ($SmsTemplate && $SmsTemplate->user_id == auth()->user()->id) {
                return response([
                    'data' => $SmsTemplate,
                    'message' => "Template Fetched Successfully",
                    'status' => 1,
                ], 200);
            }
        }
        return response("Template not found", 401);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $id = Hashids::decode($id);
        if (isset($id[0])) {
            $SmsTemplate = SmsTemplate::where('id', $id[0])->first();
            if ($SmsTemplate && $SmsTemplate->user_id == auth()->user()->id) {
                $SmsTemplate->delete();
                return response([
                    'message' => "Template Deleted Successfully",
                    'status' => 1,
                ], 200);
            }
        }
        return response("Group not found", 401);
    }
}
