<?php
    $router->group(["prefix" => "hotels", "middleware" => "jwt"], function ($router) use (&$uuid) {
        $router->get("/", [
            'middleware' => 'can:hotel_list',
            'uses' => "HotelController@index",
        ]);
        $router->get("/{hotel_id:$uuid}", [
            'middleware' => 'can:hotel_show',
            'uses' => "HotelController@show",
        ]);
        $router->post("/", [
            'middleware' => 'can:hotel_create',
            'uses' => "HotelController@store",
        ]);
        $router->post("/{hotel_id:$uuid}", [
            'middleware' => 'can:hotel_update',
            'uses' => "HotelController@update",
        ]);
        $router->patch("/restore/{hotel_id:$uuid}", [
            'middleware' => 'can:hotel_restore',
            'uses' => "HotelController@restore",
        ]);
        $router->patch("/restore/all", [
            'middleware' => 'can:hotel_restoreAll',
            'uses' => "HotelController@restoreAll",
        ]);
        $router->delete("/{hotel_id:$uuid}", [
            'middleware' => 'can:hotel_destroy',
            'uses' => "HotelController@destroy",
        ]);
        $router->delete("/force/all", [
            'middleware' => 'can:hotel_forceDestroyAll',
            'uses' => "HotelController@forceDestroyAll",
        ]);
        $router->delete("/force/{hotel_id:$uuid}", [
            'middleware' => 'can:hotel_forceDestroy',
            'uses' => "HotelController@forceDestroy",
        ]);
    });
    
    $router->post('/hotels/login', [ 'uses' => 'HotelLoginController']);
