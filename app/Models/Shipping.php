<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipping extends Model
{
    use HasFactory;
    protected $fillable = [
      'manifest_id',
      'customer_id',
     'sender',
     'receiver',
     'number',
     'source',   
     'quantity',
     'type',
      'weight',
     'size',
     'content',
     'marks',
     'notes',
     'shipping_cost',
     'against_shipping',
     'adapter',
     'advance',
     'miscellaneous',
     'prepaid',
     'discount',
     'collection',
    ];

}
