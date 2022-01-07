<?php

namespace App\Models\Endusers;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MembershipType extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ['id','client_id','name'];
}
