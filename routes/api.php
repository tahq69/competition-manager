<?php

Route::middleware('auth:api')->get('/user', function (\Illuminate\Http\Request $request) {
    return $request->user();
});

Route::get('/users/user', 'UserController@user');
Route::get('/users/search', 'UserController@search');
Route::apiResource('/users', 'UserController');
