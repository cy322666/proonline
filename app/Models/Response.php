<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Response extends Model
{
    use HasFactory;

    protected $fillable = [
        'entity_id',
        'type',
        'user_id_at',
        'user_id_to',
        'status',
    ];
}
