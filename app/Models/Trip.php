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
                   'destination_id',
                   'manifest_id',
                  'number',
                  'date',
                  'status',
                  'arrival_date',
                  'created_by',
                  'edited_by',
                  'archived',
          ]; 


        protected $hidden = ['created_at','updated_at'];


       public function truck()
         {
        return $this->belongsTo(Truck::class, 'truck_id');
         }

       public function driver()
        {
         return $this->belongsTo(Driver::class, 'driver_id');
        }
        //   public function driver()
        //   {
        //       return $this->belongsTo(Driver::class);
        //   }
          
        //   public function truck()
        //   {
        //       return $this->belongsTo(Truck::class);
        //   }
          public function manifest()
          {
              return $this->belongsTo(Manifest::class);
          }

          public function branch()
          {
              return $this->belongsTo(Branch::class);
          }
          

}
