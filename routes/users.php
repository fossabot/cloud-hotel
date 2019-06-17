<?php
    $router->group(["prefix" => "users", "middleware" => "jwt"], function ($router) use (&$uuid) {
        $router->get("/", [
            'middleware' => 'can:user_list',
            'uses' => "UserController@index",
        ]);
        $router->get("/{user_id:$uuid}", [
            'middleware' => 'can:user_show',
            'uses' => "UserController@show",
        ]);
        $router->post("/", [
            'middleware' => 'can:user_create',
            'uses' => "UserController@store",
        ]);
        $router->put("/{user_id:$uuid}", [
            'middleware' => 'can:user_update',
            'uses' => "UserController@update",
        ]);
        $router->patch("/restore/{user_id:$uuid}", [
            'middleware' => 'can:user_restore',
            'uses' => "UserController@restore",
        ]);
        $router->patch("/restore/all", [
            'middleware' => 'can:user_restoreAll',
            'uses' => "UserController@restoreAll",
        ]);
        $router->delete("/{user_id:$uuid}", [
            'middleware' => 'can:user_destroy',
            'uses' => "UserController@destroy",
        ]);
        $router->delete("/force/all", [
            'middleware' => 'can:user_forceDestroyAll',
            'uses' => "UserController@forceDestroyAll",
        ]);
        $router->delete("/force/{user_id:$uuid}", [
            'middleware' => 'can:user_forceDestroy',
            'uses' => "UserController@forceDestroy",
        ]);
    });
