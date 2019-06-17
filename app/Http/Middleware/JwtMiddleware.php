<?php
    
    namespace App\Http\Middleware;
    
    use App\JWT;
    use Closure;
    use Exception;
    use App\User;
    use Firebase\JWT\ExpiredException;
    use Illuminate\Http\Request;
    
    class JwtMiddleware
    {
        public function handle(Request $request, Closure $next, $guard = null)
        {
            $token = $request->header('authorization');
            
            if (!$token) {
                return response()->json([
                    'error' => 'Token not provided.',
                    'type' => 'token',
                ], 401);
            }
            try {
                $credentials = JWT::decode($token, env('JWT_SECRET'), ['HS256']);
            } catch (ExpiredException $e) {
                return response()->json([
                    'error' => 'Provided token is expired.',
                    'type' => 'token',
                ], 400);
            } catch (Exception $e) {
                return response()->json([
                    'error' => 'An error while decoding token.',
                    'type' => 'token',
                ], 400);
            }
            $user = User::where('_id', '=', $credentials->sub)
                ->with([
                    'roles' => function($query) {
                        $query->select('name', 'permissions');
                    }
                ])
                ->where('remember_token', '=', $credentials->remember_token)
                ->first();
            
            if ($user === null) {
                return response()->json([
                    'error' => 'Provided token is expired.',
                    'type' => 'token',
                ], 400);
            }
            
            $request->setUserResolver(function () use (&$user) {
                return $user;
            });
            
            return $next($request);
        }
    }
