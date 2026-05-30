<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard'); // Mengarahkan ke halaman dashboard
});
