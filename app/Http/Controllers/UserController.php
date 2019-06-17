<?php
    
    
    namespace App\Http\Controllers;
    
    
    use App\Http\Resources\UserResource;
    use App\Role;
    use App\User;
    use Illuminate\Http\Request;
    use Illuminate\Validation\Validator;
    
    class UserController extends Controller
    {
        /**
         * @var \App\User
         */
        private $user;
        
        /***
         * UserController constructor.
         *
         * @param \Illuminate\Http\Request $request
         * @param \App\User                $user
         */
        public function __construct(Request $request, User $user)
        {
            parent::__construct($request);
            $this->user = $user;
    
            if(!!$this->request->user()) {
                $_hotel = $this->request
                    ->user()
                    ->hotels()
                    ->select('id')
                    ->first();
                if(!!$_hotel) {
                    $this->hotelId = $_hotel->id;
                }
            }
        }
        
        /***
         * List Users
         * @method GET
         *
         * @route /users
         * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
         */
        public function index()
        {
            $only = $this->request->get('only', null);
            $users = $this->user
                ->whereHas('roles', function ($q) {
                    $q->where('id', '!=', 1);
                })
                ->with([
                    'roles' =>  function($query) {
                        $query->select('name');
                    }
                ]);
            
            if($this->hotelId > 0) {
                $users = $users->whereHas('hotels', function($query) {
                    $query->where('id', $this->hotelId);
                });
            }
            
            if($only && $only === "trashed" && can(['user_listTrashed'])) {
                $users = $users->onlyTrashed();
            }
            return _response(
                UserResource::collection(
                    $users->get()
                )
            );
        }
        
        /***
         * @param $id
         * @method GET
         *
         * @route /users/$id
         * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
         */
        public function show($id)
        {
            $user = $this->findUser($id);
            if ($user) {
                return _response(new UserResource($user));
            }
            return _responseError([
                "message" => "Not Found",
                "id" => $id,
            ], '404');
        }
        
        /***
         * @method POST
         * @route /users
         * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
         */
        public function store()
        {
            $roles = [];
            $this->_validate(
                [
                    'email' => [
                        'required', 'email', 'unique:users,email',
                    ],
                    'password' => [
                        'required', "between:6,16",
                    ],
                    'firstName' => [
                        'required', 'alpha',
                    ],
                    'lastName' => [
                        'required', 'alpha',
                    ],
                    'roles' => [
                        'required',
                    ],
                ], [], [],
                function (Validator $validator) use (&$roles) {
                    $_roles = $this->request->roles;
                    if (!!$_roles) {
                        $roles = Role::where('id', '!=', 1)->where('_id', $_roles);
                        if ($roles->count() === 0) {
                            $validator->errors()->add('roles', trans('validation.exists', ['attribute' => 'roles']));
                        }
                        $roles = $roles->get();
                    }
                }
            );
            
            $user = $this->user->create([
                'email' => $this->request->email,
                'password' => bcrypt($this->request->password),
                'metas' => [
                    'firstName' => $this->request->firstName,
                    'lastName' => $this->request->lastName,
                ],
            ]);
    
            if($this->hotelId > 0) {
                $user->hotels()->sync([$this->hotelId]);
            }
            
            $user->roles()->sync($roles);
            // TODO SEND MAIL
            
            return _response([
                'success' => true,
                'message' => 'Success',
            ], 202);
        }
        
        public function update($id)
        {
            $roles = [];
            $this->_validate(
                [
                    'firstName' => [
                        'required', 'alpha',
                    ],
                    'lastName' => [
                        'required', 'alpha',
                    ],
                    'roles' => [
                        'required',
                    ],
                    'password' => [
                        'between:6,16'
                    ]
                ], [], [],
                function (Validator $validator) use (&$roles) {
                    $_roles = $this->request->roles;
                    if (!!$_roles) {
                        $roles = Role::where('id', '!=', 1)->where('_id', $_roles);
                        if ($roles->count() === 0) {
                            $validator->errors()->add('roles', trans('validation.exists', ['attribute' => 'roles']));
                        }
                        $roles = $roles->get();
                    }
                }
            );
            
            $user = $this->findUser($id);
            
            if ($user) {
                $user->metas = array_merge($user->metas, [
                    'firstName' =>  $this->request->firstName,
                    'lastName'  => $this->request->lastName,
                ]);
                if(!!$this->request->password) {
                    $user->password = bcrypt($this->request->password);
                }
                $user->save();
                $user->roles()->sync($roles);
                
                return _response([
                    'success' => true,
                    'message' => 'Success',
                ]);
            }
            return _responseError([
                "message" => "Not Found",
                "id" => $id,
            ], '404');
        }
        
        public function destroy($id)
        {
            $user = $this->findUser($id);
            
            if ($user) {
                $user->status = false;
                $user->save();
                $user->delete();
                return _response([
                    'success' => true,
                    'message' => 'Success',
                ]);
            }
            return _responseError([
                "message" => "Not Found",
                "id" => $id,
            ], '404');
        }
        
        public function restore($id)
        {
            $user = $this->findUser($id, true);
            
            if ($user) {
                $user->status = true;
                $user->save();
                $user->restore();
                return _response([
                    'success' => true,
                    'message' => 'Success',
                ]);
            }
            return _responseError([
                "message" => "Not Found",
                "id" => $id,
            ], '404');
        }
        
        public function forceDestroy($id)
        {
            $user = $this->findUser($id, true);
            
            if ($user) {
                $user->roles()->sync([]);
                $user->hotels()->sync([]);
                $user->forceDelete();
                return _response([
                    'success' => true,
                    'message' => 'Success',
                ]);
            }
            return _responseError([
                "message" => "Not Found",
                "id" => $id,
            ], '404');
        }
        
        public function forceDestroyAll()
        {
            $this->user
                ->whereHas('roles', function ($q) {
                    $q->where('id', '!=', 1);
                })
                ->onlyTrashed()
                ->get()
                ->each(function (User $user) {
                    $user->roles()->sync([]);
                    $user->hotels()->sync([]);
                    $user->forceDelete();
                });
            
            return _response([
                'success' => true,
                'message' => 'Success',
            ]);
        }
        
        public function restoreAll()
        {
            $users = $this->user
                ->whereHas('roles', function ($q) {
                    $q->where('id', '!=', 1);
                })
                ->onlyTrashed();
            
            if ($users->count()) {
                $users->get()
                    ->each(function (User $user) {
                        $user->status = true;
                        $user->save();
                        $user->restore();
                    });
                return _response([
                    'success' => true,
                    'message' => 'Success',
                ]);
            }
            
            return _responseError([
                'message' => 'Destroyed Users Not Found',
            ], 'response', 404);
        }
        
        /***
         * @param $id
         *
         * @return \App\User|null
         */
        private function findUser($id, $trashed = false)
        {
            $user = $this->user
                ->whereHas('roles', function ($q) {
                    $q->where('id', '!=', 1);
                })
                ->where('_id', $id)->where('status', !$trashed);
            
            if ($trashed) {
                $user = $user->onlyTrashed();
            }
            
            return $user->first();
        }
    }
