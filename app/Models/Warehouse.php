<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'warehouse_name',
        'address',
        'branch_id',
        'area',
        'notes',
        'warehouse_manager_id',
    ];

    public function wmanager()
    {
        return $this->belongsTo(Warehouse_Manager::class, 'warehouse_manager_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function good()
    {
        return $this->hasMany(Good::class, 'warehouse_id');
    }
}
