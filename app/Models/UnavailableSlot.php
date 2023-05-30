<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnavailableSlot extends Model
{
    use HasFactory;

    protected $fillable = ['unavailable_datetime', 'booking_id'];
}

