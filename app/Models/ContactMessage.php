<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','email','phone','type','subject','order_number',
        'message','attachment_path','consent','status'
    ];

    protected $casts = [
        'consent' => 'boolean',
    ];
}
