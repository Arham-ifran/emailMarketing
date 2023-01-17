<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Contact;

class EmailSendingLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'campaign_id', 'contact_id', 'sent_at', 'opened_at', 'bounced_at', 'unsubscribed_at', 'failed_at', 'failed_reason', 'history_id', 'subject_id', 'content_id'
    ];
}
