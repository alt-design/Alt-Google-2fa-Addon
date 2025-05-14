<?php
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['statamic.cp.authenticated'], 'namespace' => 'AltDesign\AltGoogle2FA\Http\Controllers'], function() {
    Route::get('/alt-design/alt-google-2fa/', 'AltGoogle2FAController@index')->name('alt-google-2fa.index');
    Route::post('/alt-design/alt-google-2fa/', 'AltGoogle2FAController@update')->name('alt-google-2fa.save');
    Route::get('/alt-design/alt-disable-2fa', 'AltGoogle2FAController@disableTargetUser')->name('alt-google-2fa.disable-target-user');
});

