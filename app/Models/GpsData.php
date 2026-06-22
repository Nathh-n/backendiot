<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GpsData extends Model
{
    use HasFactory;

    // Mengizinkan Laravel untuk mengisi kolom ini
    protected $fillable = ['latitude', 'longitude'];
}