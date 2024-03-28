<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Manifest extends Model
{
    use HasFactory;
    protected $fillable = [
        'trip_id',
       'number',
       'status',
       'general_total',
        'discount',
       'net_total',
       'misc_paid',
       'against_shipping',
       'adapter',
       'advance',
       'collection',
         ];
         public function trip(){
             return $this->belongsTo(trip::class, 'trip_id');
         }
         public function shipping(){
            return $this->hasMany(shipping::class, 'manifest_id');
        }
}
