<?php

Route::middleware('auth:api')->get('/user', function (\Illuminate\Http\Request $request) {
    return $request->user();
});

Route::get('/users/user', 'UserController@user');
Route::apiResource('/users', 'UserController');
