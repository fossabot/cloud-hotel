<?php
    $router->group(["prefix" => "roles", "middleware" => "jwt"], function ($router) use (&$uuid) {
        $router->get("/", [
            'middleware' => 'can:role_list',
            'uses' => "RoleController@index",
        ]);
        $router->get("/permissions", ['middleware' => 'can:permission_list',
            function () {
                return _response(config('permissions'), 202);
            },
        ]);
        $router->get("/{role_id:$uuid}", [
            'middleware' => 'can:role_show',
            'uses' => "RoleController@show",
        ]);
        $router->post("/", [
            'middleware' => 'can:role_create',
            'uses' => "RoleController@store",
        ]);
        $router->put("/{role_id:$uuid}", [
            'middleware' => 'can:role_update',
            'uses' => "RoleController@update",
        ]);
        $router->patch("/restore/{role_id:$uuid}", [
            'middleware' => 'can:role_restore',
            'uses' => "RoleController@restore",
        ]);
        $router->patch("/restore/all", [
            'middleware' => 'can:role_restoreAll',
            'uses' => "RoleController@restoreAll",
        ]);
        $router->delete("/{role_id:$uuid}", [
            'middleware' => 'can:role_destroy',
            'uses' => "RoleController@destroy",
        ]);
        $router->delete("/force/all", [
            'middleware' => 'can:role_forceDestroyAll',
            'uses' => "RoleController@forceDestroyAll",
        ]);
        $router->delete("/force/{role_id:$uuid}", [
            'middleware' => 'can:role_forceDestroy',
            'uses' => "RoleController@forceDestroy",
        ]);
    });
