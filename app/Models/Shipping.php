<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipping extends Model
{
    use HasFactory;
    protected $fillable = [
      'source_id', 
      'destination_id',
      'manifest_id',
      'number',
       'sender',
     'receiver',
     'sender_number',
    'manifest_number',
     'receiver_number',
     'source',   
     'quantity',
     'price_id',
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
     'barcode',
    ];
    protected $hidden = ['created_at','updated_at'];

    public function branchSource(){
      return $this->belongsTo(Branch::class , 'source_id');
   }
   public function branchDest(){
    return $this->belongsTo(Branch::class , 'destination_id');
 }


 public function price()
 {
     return $this->belongsTo(Price::class);
 }
}
