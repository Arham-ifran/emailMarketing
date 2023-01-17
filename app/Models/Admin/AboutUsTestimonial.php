<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AboutUsTestimonial extends Model
{
    use HasFactory;
    protected $table = 'testimonials';

    protected $fillable = [
        'name', 'message', 'status'
    ];
}
