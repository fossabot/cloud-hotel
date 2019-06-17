<?php
	
	
	namespace App\Http\Middleware;
    
    
    use Closure;
	use Illuminate\Http\Request;
    
    class UserCanMiddleware
	{
        public function handle(Request $request, Closure $next, ...$permissions)
        {
            can($permissions);
            return $next($request);
	    }
	}
