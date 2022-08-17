<?php

namespace App\Domain\Email\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailgunCallback extends Model
{
    use HasFactory;

    protected $fillable = ['MailgunId', 'event','timestamp', 'MessageId', 'recipient', 'recipient-domain', 'IpAddress', 'sender', 'SenderIpAddress'] ;
}
