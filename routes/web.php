<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard.index'); // Mengarahkan ke halaman dashboard
});
