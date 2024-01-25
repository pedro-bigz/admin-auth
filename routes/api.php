<?php

use Illuminate\Routing\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['web'])->group(static function () {
    Route::namespace('Brackets\AdminAuth\Http\Controllers\Auth')->group(static function () {
        Route::post('/login', 'LoginController@login');

        Route::any('/logout', 'LoginController@logout')->name('brackets/admin-auth::logout');

        Route::post('/password-reset', 'ForgotPasswordController@sendResetLinkEmail');
        Route::post('/password-reset/reset', 'ResetPasswordController@reset');

        Route::get('/activation/{token}', 'ActivationController@activate')->name('brackets/admin-auth::admin/activation/activate');
    });
});

Route::middleware(['web', 'admin', 'auth:' . config('admin-auth.defaults.guard')])->group(static function () {
    Route::namespace('Brackets\AdminAuth\Http\Controllers')->group(static function () {
        Route::get('/admin', 'AdminHomepageController@index')->name('brackets/admin-auth::admin');
    });
});
