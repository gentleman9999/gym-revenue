<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'client_id', 'name'];

    public function position()
    {
        return $this->belongsToMany(Position::class, 'department_position')->withPivot('department');
    }
}
