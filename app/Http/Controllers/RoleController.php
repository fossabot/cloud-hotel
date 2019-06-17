<?php
    
    
    namespace App\Http\Controllers;
    
    
    use App\Http\Resources\RoleResource;
    use App\Role;
    use Illuminate\Http\Request;
    use Illuminate\Validation\Validator;
    
    class RoleController extends Controller
    {
        /**
         * @var \App\Role
         */
        private $role;
        
        /***
         * RoleController constructor.
         *
         * @param \Illuminate\Http\Request $request
         * @param \App\Role                $role
         */
        public function __construct(Request $request, Role $role)
        {
            parent::__construct($request);
            $this->role = $role;
        }
        
        /***
         * List Roles
         * @method GET
         *
         * @route /roles
         * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
         */
        public function index()
        {
            $only = $this->request->get('only', null);
            $roles = $this->role->where('id', '!=', 1);
            if($only && $only === "trashed" && can(['role_listTrashed'])) {
                $roles = $roles->onlyTrashed();
            }
            
            return _response(
                RoleResource::collection($roles->get())
            );
        }
        
        /***
         * @param $id
         * @method GET
         *
         * @route /roles/$id
         * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
         */
        public function show($id)
        {
            $role = $this->findRole($id);
            if ($role) {
                return _response(new RoleResource($role));
            }
            return _responseError([
                "message" => "Not Found",
                "id" => $id,
            ], '404');
        }
        
        /***
         * @method POST
         * @route /roles
         * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
         */
        public function store()
        {
            $roles = [];
            $this->_validate(
                [
                    'name' => [
                        'required', 'alpha', 'unique:roles,name',
                    ],
                    'permissions' => [
                        'required', 'array', 'min:1',
                    ],
                ], [], [],
                function (Validator $validator) use (&$roles) {
                    $permissions = $this->request->permissions;
                    
                    if (is_array($permissions) &&
                        count($permissions) &&
                        count(array_diff($permissions, config('permissions'))) > 0) {
                        return $validator->errors()->add('permissions', trans('validation.exists', ['attribute' => 'permissions']));
                    }
                }
            );
            
            $this->role->create([
                'name' => $this->request->name,
                'permissions' => $this->request->permissions,
            ]);
            
            return _response([
                'success' => true,
                'message' => 'Success',
            ], 202);
        }
        
        public function update($id)
        {
            $this->_validate(
                [
                    'name' => [
                        'alpha',
                    ],
                    'permissions' => [
                        'array', 'min:1',
                    ],
                ], [], [],
                function (Validator $validator) use (&$roles, &$id) {
                    $permissions = $this->request->permissions;
                    $name = $this->request->name;
                    
                    if (is_array($permissions) &&
                        count($permissions) &&
                        count(array_diff($permissions, config('permissions'))) > 0) {
                        return $validator->errors()->add('permissions', trans('validation.exists', ['attribute' => 'permissions']));
                    }
                    
                    if (!!$name) {
                        $check = Role::where('id', '!=', 1)->where('name', '=', $name)->where('_id', '!=', $id)->count() > 0;
                        if ($check) {
                            return $validator->errors()->add('permissions', trans('validation.unique', ['attribute' => 'name']));
                        }
                    }
                    
                }
            );
            
            $role = $this->findRole($id);
            
            if ($role) {
                $role->name = $this->request->name;
                $role->permissions = $this->request->permissions;
                $role->save();
                
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
            $role = $this->findRole($id);
            
            if ($role) {
                $role->delete();
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
            $role = $this->findRole($id, true);
            
            if ($role) {
                $role->restore();
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
            $role = $this->findRole($id, true);
            
            if ($role) {
                Role::find(2)
                    ->users()
                    ->sync(
                        $role->users()->get()
                    );
                $role->users()->sync([]);
                $role->forceDelete();
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
            $this->role
                ->whereNotIn('id', [1, 2])
                ->onlyTrashed()
                ->get()
                ->each(function (Role $role) {
                    Role::find(2)
                        ->users()
                        ->sync(
                            $role->users()->get()
                        );
                    $role->users()->sync([]);
                    $role->forceDelete();
                });
            
            return _response([
                'success' => true,
                'message' => 'Success',
            ]);
        }
        
        public function restoreAll()
        {
            $roles = $this->role->whereNotIn('id', [1, 2])->onlyTrashed();
            
            if ($roles->count()) {
                $roles->get()
                    ->each(function (Role $role) {
                        $role->restore();
                    });
                return _response([
                    'success' => true,
                    'message' => 'Success',
                ]);
            }
            
            return _responseError([
                'message' => 'Destroyed Roles Not Found',
            ], 'response', 404);
        }
        
        /***
         * @param $id
         *
         * @return \App\Role|null
         */
        private function findRole($id, $trashed = false)
        {
            $role = $this->role
                ->whereNotIn('id', [1, 2])
                ->where('_id', $id);
            
            if ($trashed) {
                $role = $role->onlyTrashed();
            }
            
            return $role->first();
        }
    }
