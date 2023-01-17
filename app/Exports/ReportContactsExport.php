<?php

namespace App\Exports;

use App\CustomClasses\TranslationHandler;
use App\Models\Contact;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ReportContactsExport implements FromCollection, WithHeadings
{
    protected $report, $contacts, $request;

    /**
     * Create a new instance.
     *
     * @return void
     */
    // public function __construct($id = NULL)
    public function __construct($contacts = [], $request)
    {
        // $this->report = $id;
        $this->contacts = $contacts;
        $this->request = $request;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // if ($this->report != NULL) {
        //     foreach ($this->report as $ind => $report) {
        //         $this->report[$ind]->status = $this->report[$ind]->pivot->failed_at == NULL ? "Sent" : "Failed";
        //     }
        //     return $this->report->makeHidden(['for_sms', 'for_email', 'id', 'hash_id', 'request_source', 'user_id', 'country_code', 'subscribed', 'unsubscribed_at', 'confirmed_at', 'created_at', 'updated_at', 'deleted_at']);
        // } else {
        // return Contact::all()->take(0);
        // }
        // dd($this->contacts->pluck('id')->toArray());
        if ($this->contacts != NULL) {
            return $this->contacts->makeHidden(['for_sms', 'for_email', 'id', 'hash_id', 'request_source', 'user_id', 'country_code', 'subscribed', 'unsubscribed_at', 'confirmed_at', 'created_at', 'updated_at', 'deleted_at']);
        } else {
            return Contact::all()->take(0);
        }
    }

    public function headings(): array
    {
        return [TranslationHandler::getTranslation($this->request->lang,'First Name'), TranslationHandler::getTranslation($this->request->lang,"Last Name"), TranslationHandler::getTranslation($this->request->lang,"Number"), TranslationHandler::getTranslation($this->request->lang,"Email")];
    }
}
