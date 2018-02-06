<?php


Route::post('/thsample', 'Example\ThSampleController@create');

Route::post('/thsample/{id}', 'Example\ThSampleController@update');

Route::get('/thsample/{id}', 'Example\ThSampleController@retrieve');

Route::get('/thsamples', 'Example\ThSampleController@retrieveList');



Route::post('/kxuser', 'Hello\KxUserController@create');

Route::post('/kxuser/{id}', 'Hello\KxUserController@update');

Route::get('/kxuser/{id}', 'Hello\KxUserController@retrieve');

Route::get('/kxusers', 'Hello\KxUserController@retrieveList');
