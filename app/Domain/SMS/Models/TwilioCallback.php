<?php

namespace App\Domain\SMS\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwilioCallback extends Model
{
    use HasFactory;

    protected $fillable = ['SmsSid', 'SmsStatus', 'MessageStatus', 'To', 'MessageSid', 'AccountSid', 'From', 'ApiVersion'] ;
}
