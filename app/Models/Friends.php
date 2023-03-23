<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;

class Friends extends Model
{
    use HasFactory;
    protected $fillable = [
        'first_user',
        'second_user',
        'status',
    ];
}
