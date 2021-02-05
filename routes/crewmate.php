<?php

use Illuminate\Support\Facades\Route;

// Redirect to dashboard
Route::get('/', function(){
	return redirect(route('crewmate.home'));
})->name('crewmate.default');

// Works only for guests users
Route::middleware(['crewmate.guest'])->group(function () {
	// Login
	Route::get('login', 'Auth\LoginController@showLoginForm')->name('crewmate.login');
	Route::post('login', 'Auth\LoginController@login');
});

// Works only for authenticated users
Route::middleware(['crewmate.auth'])->group(function () {
	// Dashboard
	Route::get('/dashboard', 'HomeController@index')->name('crewmate.home');
	// Logout
	Route::post('logout', 'Auth\LoginController@logout')->name('crewmate.logout');
});

// Reset Password (works for both guests and authenticated users
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('crewmate.password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('crewmate.password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('crewmate.password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('crewmate.password.update');
