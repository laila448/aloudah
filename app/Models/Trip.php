<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;
    protected $fillable = [
                  'truck_id',
                   'driver_id',
                   'employee_id',
                   'branch_id',
                   'manifest_id',
                  'number',
                  'date',
                  'source',
                  'destination',
                  'status',
                  'arrival_date',
                  'created_by',
                  'edited_by',
                  'archived',
          ]; 

}
