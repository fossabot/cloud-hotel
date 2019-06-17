<?php
    
    namespace App\Http\Controllers;
    
    
    use App\JWT;
    use App\User;
    use Illuminate\Http\Request;
    use Illuminate\Support\Str;
    use Illuminate\Validation\Validator;
    
    class LoginController extends Controller
    {
        /***
         * @var \App\User
         */
        private $user;
        
        /***
         * LoginController constructor.
         *
         * @param \Illuminate\Http\Request $request
         * @param \App\User                $user
         */
        public function __construct(Request $request, User $user)
        {
            parent::__construct($request);
            $this->user = $user;
        }
        
        public function __invoke()
        {
            $user = null;
            
            $this->_validate([
                'email' => [
                    'required', 'email', 'exists:users,email',
                ],
                'password' => [
                    'required',
                ],
            ], [], [],
            function (Validator $validator) use (&$user) {
                $user = $this->user->where('email', $this->request->email)->first();
    
                if (!$user || !!$user && !checkPassword($this->request->password, $user->password)) {
                    $validator->errors()->add('email', 'Your email address or password is incorrect');
                }
            });
            
            return _response(
                $this->jwt($user)
            );
        }
        protected function jwt(User $user)
        {
            $user->remember_token = Str::random(100);
            $user->save();
            $expire = now()->addHours(env('TOKEN_EXPIRE'))->timestamp;
            $payload = [
                'iss' => env('APP_NAME'),
                'sub' => $user->_id,
                'iat' => now(),
                'exp' => $expire,
                'remember_token' => $user->remember_token
            ];
        
            return [
                "token" => JWT::encode($payload, env('JWT_SECRET')),
                "expire" => $expire,
            ];
        }
    }
