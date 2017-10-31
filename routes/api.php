<?php

$this->middleware('auth:api')->get('/user', function (\Illuminate\Http\Request $request) {
    return $request->user();
});

$this->get('/users/user', 'UserController@user');
$this->get('/users/search', 'UserController@search');
$this->apiResource('/users', 'UserController');

$this->post('password/email', 'Auth\\ForgotPasswordController@sendResetLinkEmail');
$this->post('password/reset', 'Auth\\ResetPasswordController@reset')->name('password.reset');

$this->resource('teams', 'TeamController', [
    'only' => ['index'/*, 'store', 'show', 'update'*/]
]);
