<?php
    $router->post("/login", "LoginController");
    $router->get("/me", [
        'middleware'    =>  [
            'jwt'
        ],
        'uses'  =>  "MeController"
    ]);
