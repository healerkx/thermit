<?php
/**
 * Created by PhpStorm.
 * User: healer
 * Date: 2017/11/24
 * Time: 15:57
 */

include 'admin_mine.php';
// include 'admin_mine1.php';
// include 'admin_mine2.php';
// include 'admin_mine3.php';

/**/

/*
Route::get('/login', function () {
    return view('admin/login');
});
*/

Route::get('/index', 'Admin\AdminController@index');

Route::get('/blank', 'Admin\AdminController@blank');



Route::get('somePage5', 'Admin\AdminController@somePage5');
