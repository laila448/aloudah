<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Good extends Model
{
    use HasFactory , SoftDeletes;

    protected $fillable = [
      'warehouse_id',
      'type',
      'quantity',
      'weight',
      'size' ,
      'content',
      'marks',
     // 'price',
     'truck',
     'driver',
     //'desk',
     'destination',
     'ship_date',
       'date',
       'sender',
      'receiver',
      'barcode',
      'received',
         ];
}
