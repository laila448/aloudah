<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'address',
        'desk',
        'phone',
        'opening_date',
        'closing_date',
        'created_by',
        'edited_by',
        'editing_date',
    ];

    public function branchManager()
    {
        return $this->belongsTo(Branch_Manager::class, 'branchmanager_id');
    }

    public function employees()
    {
        return $this->hasMany(Employee::class, 'branch_id');
    }

    public function trips()
    {
        return $this->hasMany(Trip::class, 'branch_id');
    }

    public function trucks()
    {
        return $this->hasMany(Truck::class, 'branch_id');
    }
}
