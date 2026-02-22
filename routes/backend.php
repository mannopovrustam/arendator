<?php


use App\Http\Controllers\Backend\KadastrController;
use App\Http\Controllers\Backend\ListingController;
use App\Http\Controllers\Backend\ListokController;

Route::auto('listings', ListingController::class);
Route::resource('listings', ListingController::class);

Route::auto('kadastr', KadastrController::class);
Route::resource('kadastr', KadastrController::class);

Route::auto('listok', ListokController::class);
Route::resource('listok', ListokController::class);

Route::post('person-info', [\App\Services\PersonInfo::class, 'postPassportData']);
