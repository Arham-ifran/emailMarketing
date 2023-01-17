<?php

namespace App\Exports;

use App\CustomClasses\TranslationHandler;
use App\Models\Contact;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ContactsExport implements FromCollection, WithStrictNullComparison, WithHeadings
{
    protected $request;

    /**
     * Create a new instance.
     *
     * @return void
     */
    // public function __construct($id = NULL)
    public function __construct( $request)
    {
        $this->request = $request;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $data = Contact::take(1)->get(['first_name', 'last_name', 'for_sms', 'for_email', 'number', 'email']);
        $data[0]->first_name = "Sam";
        $data[0]->last_name = "Smith";
        $data[0]->for_sms = "1";
        $data[0]->for_email = "1";
        $data[0]->number = "491579230199";
        $data[0]->email = "example@email.com";
        return $data;
    }

    public function headings(): array
    {
        // return ['first_name', 'last_name', 'for_sms', 'for_email', 'country_code', 'number', 'email'];
        // return ['first_name', 'last_name', 'for_sms', 'for_email', 'number', 'email'];
        return [TranslationHandler::getTranslation($this->request->lang,'First Name'), TranslationHandler::getTranslation($this->request->lang,'Last Name'), TranslationHandler::getTranslation($this->request->lang,'For SMS'), TranslationHandler::getTranslation($this->request->lang,'For Email'), TranslationHandler::getTranslation($this->request->lang,'Number'), TranslationHandler::getTranslation($this->request->lang,'Email')];
    }
}
