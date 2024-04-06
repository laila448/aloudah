<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;
    protected $fillable = [
   'address',
    'phone',
   
    ];
    public function branch_manager(){
        return $this->belongsTo(branch_manager::class, 'branchmanager_id');
    }
    public function employee(){
        return $this->hasMany(employee::class, 'branch_id');
    }
    public function trip(){
        return $this->hasMany(trip::class, 'branch_id');
    }

}
