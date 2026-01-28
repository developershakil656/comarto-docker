<?php
use Illuminate\Support\Facades\Route;

# php artisan passport:client --personal

Route::get('/', function () {
  return view('welcome');
});
Route::get('/login', function () {
  return error_response();
})->name('login');