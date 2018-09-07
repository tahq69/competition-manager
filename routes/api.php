<?php

use Illuminate\Routing\Router;

$this->group(['prefix' => 'user'], function (Router $route) {
    $route->get('messages/count/unread', 'MessageController@countUnread');
    $route->get('messages/read/{message}', 'MessageController@read');
    $route->post('messages/{message}/reply', 'MessageController@reply');
    $route->resource('messages', 'MessageController', ['only' => ['index', 'store']]);
    $route->get('', function (\Illuminate\Http\Request $request) {
        return $request->user();
    })->middleware('auth:api');
});

$this->get('/users/user', 'UserController@user');
$this->get('/users/search', 'UserController@search');
$this->apiResource('/users', 'UserController');

$this->post('password/email', 'Auth\\ForgotPasswordController@sendResetLinkEmail');
$this->post('password/reset', 'Auth\\ResetPasswordController@reset')->name('password.reset');

$this->resource('teams', 'TeamController', ['only' => ['index', 'show', 'store', 'update']]);
$this->resource('teams.members', 'TeamMemberController', ['only' => ['index', 'show', 'store', 'update']]);
$this->resource('teams.members.roles', 'TeamMemberRoleController', ['only' => ['index', 'store']]);
$this->resource('competitions', 'CompetitionController', ['only' => ['index', 'show', 'store', 'update']]);
$this->resource('competitions.areas', 'AreaController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
$this->resource('competitions.disciplines', 'DisciplineController', ['only' => ['index', 'show', 'update', 'store']]);

$this->get(
    '/competitions/{competition}/disciplines/{discipline}/categories',
    'CategoryGroupController@categories'
)->name('competitions.disciplines.categories');

$this->resource('competitions.disciplines.groups', 'CategoryGroupController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
$this->resource('competitions.disciplines.groups.categories', 'CategoryController', ['only' => ['index', 'show', 'store', 'update', 'destroy']]);
