<?php

namespace App\Imports;

use App\Models\Contact;
use App\Models\Group;
use App\Models\Contact_group;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns;


class ContactsImportCheck implements ToModel
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    private $errors = [], $hasError = true, $fields, $first = true, $countRows = 0;

    public function model(array $row)
    {
        if ($this->first) {
            $this->first = false;
            if (sizeOf($row) < 6) {
                $this->hasError = true;
                array_push($this->errors, "Not enough data in the field");
            } else {
                $this->fields = $row;
            }
            array_push($this->errors, "Not enough data rows");
        } else {
            $this->countRows++;
            array_splice($this->errors, 0, 1);
            if (count($this->errors) == 0)
                $this->hasError = false;
        }
    }

    public function getRows()
    {
        return $this->countRows;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function hasErrors()
    {
        return $this->hasError;
    }

    public function getfields()
    {
        return $this->fields;
    }
}
