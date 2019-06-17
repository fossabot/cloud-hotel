<?php
    $router->group(["prefix" => "rooms", "middleware" => "jwt"], function ($router) use (&$uuid) {
        $router->get("/", [
            'middleware' => 'can:room_list',
            'uses' => "RoomController@index",
        ]);
        $router->get("/{room_id:$uuid}", [
            'middleware' => 'can:room_show',
            'uses' => "RoomController@show",
        ]);
        $router->post("/", [
            'middleware' => 'can:room_create',
            'uses' => "RoomController@store",
        ]);
        $router->put("/{room_id:$uuid}", [
            'middleware' => 'can:room_update',
            'uses' => "RoomController@update",
        ]);
        $router->patch("/restore/{room_id:$uuid}", [
            'middleware' => 'can:room_restore',
            'uses' => "RoomController@restore",
        ]);
        $router->patch("/restore/all", [
            'middleware' => 'can:room_restoreAll',
            'uses' => "RoomController@restoreAll",
        ]);
        $router->delete("/{room_id:$uuid}", [
            'middleware' => 'can:room_destroy',
            'uses' => "RoomController@destroy",
        ]);
        $router->delete("/force/all", [
            'middleware' => 'can:room_forceDestroyAll',
            'uses' => "RoomController@forceDestroyAll",
        ]);
        $router->delete("/force/{room_id:$uuid}", [
            'middleware' => 'can:room_forceDestroy',
            'uses' => "RoomController@forceDestroy",
        ]);
    });
