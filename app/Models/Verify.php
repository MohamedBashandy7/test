<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Verify extends Model
{
    /** @use HasFactory<\Database\Factories\VerifyFactory> */
    use HasFactory;
    protected $fillable = [
        'email',
        'code',
    ];
 
    protected $table = 'verify';
}
