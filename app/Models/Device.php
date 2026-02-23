<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{

    protected $fillable = [
        'name',
        'serial_number',
        'ip_address',
        'location',
        'status'
    ];

    protected $casts = [
        'status' => 'integer',
    ];
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

}