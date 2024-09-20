<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    protected $table = 'notifications';
    protected $fillable = [
        'user_id',
        'user_type',
        'title',
        'body',
        'is_read',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];
}
