<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Branch;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
     {
        
    Admin::create([
    'name'=>'admin5',
    'email'=>'admin5@gmail.com',
    'phone_number'=>'0955768',
    'gender'=>'male',
    'password'=>Hash::make('12345678'),

         ]);


         Branch::create([
            'address'=>'damas',
            'desk'=>'senaa',
             'phone'=>'345677',
             'opening_date'=>'2020-02-02',
             'created_by'=>'admin1',
             'edited_by'=>'ad1',
             'editing_date'=>'2020-02-02',
         ]);
    }







}
