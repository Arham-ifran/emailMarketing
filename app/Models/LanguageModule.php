<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LanguageModule extends Model
{
    // use HasFactory;
    protected $fillable = [
        'name', 'table', 'columns', 'status'
    ];

    public function languageTranslations()
    {
        return $this->hasMany('App\Models\LanguageTranslation', 'language_module_id');
    }
}
