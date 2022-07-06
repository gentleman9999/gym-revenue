<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;

    protected $fillable = ['id','client_id', 'name'];

    public function department()
    {
        return $this->belongsToMany(Department::class, 'department_position')->withPivot('position');
    }
}
