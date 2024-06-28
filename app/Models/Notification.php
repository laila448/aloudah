<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_manager_id',
        'warehouse_manager_id',
        'title',
        'body',
        'type',
        'is_read',
        'data',
        'status',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function branchManager()
    {
        return $this->belongsTo(Branch_Manager::class);
    }

    public function warehouseManager()
    {
        return $this->belongsTo(Warehouse_Manager::class);
    }
}
