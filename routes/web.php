<?php

Route::get('/auth/password/reset/{token}')->name('password.reset');
Route::get('/{subs?}', 'HomeController@index')->where(['subs' => '.*']);
