<?php
    $router->group(["prefix" => "room-types", "middleware" => "jwt"], function ($router) use (&$uuid) {
        $router->get("/", [
            'middleware' => 'can:roomType_list',
            'uses' => "RoomTypeController@index",
        ]);
        $router->get("/{roomType_id:$uuid}", [
            'middleware' => 'can:roomType_show',
            'uses' => "RoomTypeController@show",
        ]);
        $router->post("/", [
            'middleware' => 'can:roomType_create',
            'uses' => "RoomTypeController@store",
        ]);
        $router->put("/{roomType_id:$uuid}", [
            'middleware' => 'can:roomType_update',
            'uses' => "RoomTypeController@update",
        ]);
        $router->patch("/restore/{roomType_id:$uuid}", [
            'middleware' => 'can:roomType_restore',
            'uses' => "RoomTypeController@restore",
        ]);
        $router->patch("/detach/{roomType_id:$uuid}", [
            'middleware' => 'can:roomType_detach',
            'uses' => "RoomTypeController@detachRoomType",
        ]);
        $router->patch("/restore/all", [
            'middleware' => 'can:roomType_restoreAll',
            'uses' => "RoomTypeController@restoreAll",
        ]);
        $router->delete("/{roomType_id:$uuid}", [
            'middleware' => 'can:roomType_destroy',
            'uses' => "RoomTypeController@destroy",
        ]);
        $router->delete("/force/all", [
            'middleware' => 'can:roomType_forceDestroyAll',
            'uses' => "RoomTypeController@forceDestroyAll",
        ]);
        $router->delete("/force/{roomType_id:$uuid}", [
            'middleware' => 'can:roomType_forceDestroy',
            'uses' => "RoomTypeController@forceDestroy",
        ]);
    });
