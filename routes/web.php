<?php

Route::get('/', function () {

    return view('welcome');
});

Route::auth();

Route::get('subscribe', 'WaitlistController@index')->name('subscribe');
Route::post('subscribed', 'WaitlistController@subscribe')->name('waitlist');
Route::get('subscribed', 'WaitlistController@subscribed')->name('subscribed');


