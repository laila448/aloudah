<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'add_trip',
        'edit_trip',
        'delete_trip',
        'drawer',
        'email',
        'trip_list',
        'print_road',
        'print_trips',
        'edit_close',
        'add_manifest',
        'edit_manifest',
        'delete_manifest',
        'view_manifest',
        'add_report',
        'edit_report',
        'delete_report',
        'view_report',
        'add_misc',
        'edit_misc',
        'delete_misc',

    ];
}
