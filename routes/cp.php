<?php
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['statamic.cp.authenticated'], 'namespace' => 'AltDesign\AltGoogle2FA\Http\Controllers'], function() {
    Route::get('/alt-design/alt-google-2fa/', 'AltGoogle2FAController@index')->name('alt-google-2fa.index');
    Route::post('/alt-design/alt-google-2fa/', 'AltGoogle2FAController@update')->name('alt-google-2fa.save');
});

