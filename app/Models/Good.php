<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Good extends Model
{
    use HasFactory;
    protected $fillable = [
      'warehouse_id',
      'type',
      'quantity',
      'weight',
      'price',
       'date',
       'sender',
      'receiver',
         ];
}
