<?php
    
    require_once __DIR__ . '/../vendor/autoload.php';
    
    (new Laravel\Lumen\Bootstrap\LoadEnvironmentVariables(
        dirname(__DIR__)
    ))->bootstrap();
    
    /*
	|--------------------------------------------------------------------------
	| Create The Application
	|--------------------------------------------------------------------------
	|
	| Here we will load the environment and create the application instance
	| that serves as the central piece of this framework. We'll use this
	| application as an "IoC" container and router for this framework.
	|
	*/
    
    $app = new Laravel\Lumen\Application(
        dirname(__DIR__)
    );

// $app->withFacades();
    
    $app->withEloquent();
    $app->configure('permissions');
    $app->configure('image');
    $app->configure('cors');
    
    /*
	|--------------------------------------------------------------------------
	| Register Container Bindings
	|--------------------------------------------------------------------------
	|
	| Now we will register a few bindings in the service container. We will
	| register the exception handler and the console kernel. You may add
	| your own bindings here if you like or you can make another file.
	|
	*/
    
    $app->singleton(
        Illuminate\Contracts\Debug\ExceptionHandler::class,
        App\Exceptions\Handler::class
    );
    
    $app->singleton(
        Illuminate\Contracts\Console\Kernel::class,
        App\Console\Kernel::class
    );
    
    /*
	|--------------------------------------------------------------------------
	| Register Middleware
	|--------------------------------------------------------------------------
	|
	| Next, we will register the middleware with the application. These can
	| be global middleware that run before and after each request into a
	| route or middleware that'll be assigned to some specific routes.
	|
	*/
 $app->middleware([
     \Barryvdh\Cors\HandleCors::class,
     \App\Http\Middleware\AutoTrimmer::class,
//     App\Http\Middleware\ExampleMiddleware::class
 ]);

 $app->routeMiddleware([
     'jwt'  => \App\Http\Middleware\JwtMiddleware::class,
     'can'  => \App\Http\Middleware\UserCanMiddleware::class
 ]);
    
    /*
	|--------------------------------------------------------------------------
	| Register Service Providers
	|--------------------------------------------------------------------------
	|
	| Here we will register all of the application's service providers which
	| are used to bind services into the container. Service providers are
	| totally optional, so you are not required to uncomment this line.
	|
	*/

 $app->register(App\Providers\AppServiceProvider::class);
// $app->register(App\Providers\AuthServiceProvider::class);
// $app->register(App\Providers\EventServiceProvider::class);
    $app->register(Intervention\Image\ImageServiceProvider::class);
    $app->register(Barryvdh\Cors\ServiceProvider::class);
    $app->register(Appzcoder\LumenRoutesList\RoutesCommandServiceProvider::class);
    /*
	|--------------------------------------------------------------------------
	| Load The Application Routes
	|--------------------------------------------------------------------------
	|
	| Next we will include the routes file so that they can all be added to
	| the application. This will provide all of the URLs the application
	| can respond to, as well as the controllers that may handle them.
	|
	*/
    
    $app->router->group([
        'namespace' => 'App\Http\Controllers',
    ], function ($router) {
        $uuid = "[a-f0-9]{8}-?[a-f0-9]{4}-?4[a-f0-9]{3}-?[89ab][a-f0-9]{3}-?[a-f0-9]{12}";
        require __DIR__ . '/../routes/roles.php';
        require __DIR__ . '/../routes/room.php';
        require __DIR__ . '/../routes/roomType.php';
        require __DIR__ . '/../routes/roomCapacity.php';
        require __DIR__ . '/../routes/users.php';
        require __DIR__ . '/../routes/hotel.php';
        require __DIR__ . '/../routes/web.php';
    });
    
    return $app;
