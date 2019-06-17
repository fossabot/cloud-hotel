<?php
	
	
	namespace App\Http\Middleware;
	
	use Closure;
	use Illuminate\Http\Request;
    
    class AutoTrimmer
	{
        public function handle(Request $request, Closure $next)
        {
    
            $input = $request->all();
            if ($input) {
                array_walk_recursive($input, function (&$item, &$key) {
                    if (is_string($item)) {
                        $item = trim($item);
                    }
                    
                    $item = ($item === "") ? null : $item;
                });
                $request->merge($input);
            }
            
            return $next($request);
        }
	}
