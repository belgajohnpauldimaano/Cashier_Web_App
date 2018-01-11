<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'students'], function () {
    Route::get('student-list', 'Admin\StudentController@api_student_list')->name('student.api_student_list');
    Route::get('student-list-mobile', 'Admin\StudentController@api_student_list_mobile')->name('student.api_student_list_mobile');
});
