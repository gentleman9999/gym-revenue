<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsTracking extends Model
{
    use HasFactory;

    protected $fillable = ['SmsSid', 'SmsStatus', 'MessageStatus', 'To', 'MessageSid', 'AccountSid', 'From', 'ApiVersion'] ;
}
