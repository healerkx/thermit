<?php

/**
 * ListView about
 */
Route::get('/listview-lists', 'Admin\ListBuilderController@listAll');

Route::get('/listview-create', 'Admin\ListBuilderController@create');

Route::get('/listview-edit', 'Admin\ListBuilderController@edit');

Route::get('/listview', 'Admin\ListBuilderController@show');

/**
 * FormCreator about
 */
Route::get('/formcreator-lists', 'Admin\FormBuilderController@listAll');

Route::get('/formcreator-create', 'Admin\FormBuilderController@create');

Route::get('/formcreator-edit', 'Admin\FormBuilderController@edit');

Route::get('/formcreator', 'Admin\FormBuilderController@show');


