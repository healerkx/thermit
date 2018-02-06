
Route::post('/<?=$path?>', '<?=$controllerName?>@create');

Route::post('/<?=$path?>/{id}', '<?=$controllerName?>@update');

Route::get('/<?=$path?>/{id}', '<?=$controllerName?>@retrieve');

Route::get('/<?=$pathPlural?>', '<?=$controllerName?>@retrieveList');
