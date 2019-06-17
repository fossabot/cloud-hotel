<?php
    $router->group(["prefix" => "room-capacities", "middleware" => "jwt"], function ($router) use (&$uuid) {
        $router->get("/", [
            'middleware' => 'can:roomCapacity_list',
            'uses' => "RoomCapacityController@index",
        ]);
        $router->get("/{roomCapacity_id:$uuid}", [
            'middleware' => 'can:roomCapacity_show',
            'uses' => "RoomCapacityController@show",
        ]);
        $router->post("/", [
            'middleware' => 'can:roomCapacity_create',
            'uses' => "RoomCapacityController@store",
        ]);
        $router->put("/{roomCapacity_id:$uuid}", [
            'middleware' => 'can:roomCapacity_update',
            'uses' => "RoomCapacityController@update",
        ]);
        $router->patch("/restore/{roomCapacity_id:$uuid}", [
            'middleware' => 'can:roomCapacity_restore',
            'uses' => "RoomCapacityController@restore",
        ]);
        $router->patch("/detach/{roomType_id:$uuid}", [
            'middleware' => 'can:roomCapacity_detach',
            'uses' => "RoomCapacityController@detachRoomCapacity",
        ]);
        $router->patch("/restore/all", [
            'middleware' => 'can:roomCapacity_restoreAll',
            'uses' => "RoomCapacityController@restoreAll",
        ]);
        $router->delete("/{roomCapacity_id:$uuid}", [
            'middleware' => 'can:roomCapacity_destroy',
            'uses' => "RoomCapacityController@destroy",
        ]);
        $router->delete("/force/all", [
            'middleware' => 'can:roomCapacity_forceDestroyAll',
            'uses' => "RoomCapacityController@forceDestroyAll",
        ]);
        $router->delete("/force/{roomCapacity_id:$uuid}", [
            'middleware' => 'can:roomCapacity_forceDestroy',
            'uses' => "RoomCapacityController@forceDestroy",
        ]);
    });
