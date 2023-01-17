<?php

namespace App\Exports;

use App\CustomClasses\TranslationHandler;
use App\Models\Contact;
use App\Models\EmailCampaignClick;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReportClicksExport implements FromCollection, WithHeadings
{
    protected $report, $clicks, $request;

    /**
     * Create a new instance.
     *
     * @return void
     */
    // public function __construct($id = NULL)
    public function __construct($clicks = [] , $request)
    {
        // $this->report = $id;
        $this->clicks = $clicks;
        $this->request = $request;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        if ($this->clicks != NULL) {
            foreach ($this->clicks as $clickData) {
                $clickData->first_name = $clickData->contact->first_name;
                $clickData->last_name = $clickData->contact->last_name;
                $clickData->email = $clickData->contact->email;
            }
            return $this->clicks->makeHidden(['id', 'campaign_id', 'history_id', 'contact_id', 'updated_at']);
        } else {
            return EmailCampaignClick::all()->take(0);
        }
    }

    public function headings(): array
    {
        return [TranslationHandler::getTranslation($this->request->lang,"Clicked Link"), TranslationHandler::getTranslation($this->request->lang,"Clicked At"), TranslationHandler::getTranslation($this->request->lang,'First Name'), TranslationHandler::getTranslation($this->request->lang,"Last Name"), TranslationHandler::getTranslation($this->request->lang,"Email")];
    }
}
