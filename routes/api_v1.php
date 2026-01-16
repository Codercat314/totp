<?php

/**
 * @var \Laravel\Lumen\Routing\Router $router
 * 
 */

$router->post('login', 'AuthController@login');
$router->get('/refresh', 'AuthController@refresh');
$router->delete('/refresh', 'AuthController@logout');
$router->delete('/refresh/all', 'AuthController@logoutAll');

$router->group(['middleware' => 'auth.jwt'], function() use ($router){
    $router->get('/protected', 'ExampleController@protected');

});
 $router->get('/open', 'ExampleController@open');