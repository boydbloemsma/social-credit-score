<?php

use App\Events\AccountOpened;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/fire', function () {
    \App\Events\BalanceIncreased::commit(
        account_id: 1,
        score: 15,
        description: 'Was nice to me',
    );
    return view('welcome');
});
