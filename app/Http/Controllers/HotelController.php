<?php
    
    
    namespace App\Http\Controllers;
    
    
    use App\Http\Resources\HotelResource;
    use App\Hotel;
    use App\Role;
    use App\User;
    use Illuminate\Http\Request;
    use Illuminate\Support\Str;
    use Illuminate\Validation\Validator;
    
    class HotelController extends Controller
    {
        /**
         * @var \App\Hotel
         */
        private $hotel;
        
        /***
         * @var \App\User
         */
        private $user;
        
        /***
         * HotelController constructor.
         *
         * @param \Illuminate\Http\Request $request
         * @param \App\Hotel               $hotel
         * @param \App\User                $user
         */
        public function __construct(Request $request, Hotel $hotel, User $user)
        {
            parent::__construct($request);
            $this->user = $user;
            $this->hotel = $hotel;
            
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
         * List Hotels
         * @method GET
         *
         * @route /hotels
         * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
         */
        public function index()
        {
            $only = $this->request->get('only', null);
            $hotels = $this->hotel;
            if($only && $only === "trashed" && can(['hotel_listTrashed'])) {
                $hotels = $hotels->onlyTrashed();
            } 
            return _response(
                HotelResource::collection(
                    $hotels->get()
                )
            );
        }
        
        /***
         * @param $id
         * @method GET
         *
         * @route /hotels/$id
         * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
         */
        public function show($id)
        {
            $hotel = $this->findHotel($id);
            if ($hotel) {
                return _response(new HotelResource($hotel));
            }
            return _responseError([
                "message" => "Not Found",
                "id" => $id,
            ], '404');
        }
        
        /***
         * @method POST
         * @route /hotels
         * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
         */
        public function store()
        {
            $role = null;
            $this->_validate(
                [
                    'account_email' => [
                        'required', 'email',
                    ],
                    'account_password' => [
                        'required', "between:6,16",
                    ],
                    'account_firstName' => [
                        'required',
                    ],
                    'account_lastName' => [
                        'required',
                    ],
                    'email' => [
                        'required', 'email', 'unique:hotels,email',
                    ],
                    'name' => [
                        'required', 'unique:hotels,name',
                    ],
                    'slug' => [
                        'required', 'unique:hotels,slug',
                    ],
                    'address' => [
                        'required',
                    ],
                    'city' => [
                        'required',
                    ],
                    'state' => [
                        'required',
                    ],
                    'country' => [
                        'required',
                    ],
                    'zipCode' => [
                        'required',
                    ],
                    'phone' => [
                        'required', 'unique:hotels,phone',
                    ],
                    'image' => [
                        'required', 'mimes:jpeg,png', 'max:5120',
                    ],
                    'role' => [
                        'required',
                    ],
                ], [], [],
                
                function (Validator $validator) use (&$role) {
                    $_role = $this->request->role;
                    if (!!$_role) {
                        $role = Role::where('id', '!=', 1)->where('_id', $_role);
                        if ($role->count() === 0) {
                            $validator->errors()->add('roles', trans('validation.exists', ['attribute' => 'roles']));
                        }
                        $role = $role->first();
                    }
                }
            );
            $metas = $this->request->except([
                'name', 'slug', 'email', 'phone', 'image', 'account_email',
                'account_password', 'account_firstName', 'account_lastName',
            ]);
            $metas['image'] = upload($this->request->file('image'));
            
            $hotel = $this->hotel->create([
                'name' => $this->request->name,
                'slug' => $this->request->slug ?: Str::slug($this->request->name, '-'),
                'email' => $this->request->email,
                'phone' => $this->request->phone,
                'metas' => $metas,
            ]);
            
            $user = $this->user->firstOrCreate([
                'email' => $this->request->account_email,
            ], [
                'password' => bcrypt($this->request->account_password),
                'metas' => [
                    'firstName' => $this->request->account_firstName,
                    'lastName' => $this->request->account_lastName,
                ],
            ]);
            
            $hotel->users()->sync($user);
            $user->roles()->sync($role);
            
            return _response([
                'success' => true,
                'message' => 'Success',
            ], 202);
        }
        
        public function update($id)
        {
            $this->_validate(
                [
                    'email' => [
                        'email',
                    ],
                    'name' => [
                        'required',
                    ],
                    'slug' => [
                        'required',
                    ],
                    'address' => [
                        'required',
                    ],
                    'city' => [
                        'required',
                    ],
                    'state' => [
                        'required',
                    ],
                    'country' => [
                        'required',
                    ],
                    'zipCode' => [
                        'required',
                    ],
                    'phone' => [
                        'required',
                    ],
                    'image' => [
                        'mimes:jpeg,png', 'max:5120',
                    ],
                ], [], [],
                function (Validator $validator) use (&$id) {
                    $name = $this->request->name;
                    $slug = $this->request->slug;
                    $phone = $this->request->phone;
                    
                    if (!!$name && $this->updateUnique($id, 'name', $name)) {
                        $validator->errors()->add('name', trans('validation.unique', ['attribute' => 'name']));
                    }
                    
                    if (!!$slug && $this->updateUnique($id, 'slug', $slug)) {
                        $validator->errors()->add('slug', trans('validation.unique', ['attribute' => 'slug']));
                    }
                    
                    if (!!$phone && $this->updateUnique($id, 'phone', $phone)) {
                        $validator->errors()->add('phone', trans('validation.unique', ['attribute' => 'phone']));
                    }
                }
            );
            
            $hotel = $this->findHotel($id);
            
            if ($hotel) {
                $metas = $this->request->except([
                    'name', 'slug', 'email', 'phone', 'image',
                ]);
                
                if($hotel->name !== $this->request->name) {
                    $hotel->name = $this->request->name;
                }
                
                if($hotel->slug !== $this->request->slug) {
                    $hotel->slug = $this->request->slug;
                }
                
                if($hotel->email !== $this->request->email) {
                    $hotel->email = $this->request->email;
                }
                
                if($hotel->phone !== $this->request->phone) {
                    $hotel->phone = $this->request->phone;
                }
                
                if ($this->request->hasFile('image')) {
                    $this->removeImage($hotel->metas['image']);
                    $metas['image'] = upload($this->request->file('image'));
                }
                
                $hotel->metas = array_merge($hotel->metas, $metas);
                $hotel->save();
                
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
        
        /***
         * @param $id
         * @param $column
         * @param $value
         *
         * @return bool
         */
        public function updateUnique($id, $column, $value): bool
        {
            return Hotel::where('_id', '!=', $id)->where($column, '=', $value)->count() > 0;
        }
        
        public function destroy($id)
        {
            $hotel = $this->findHotel($id);
            
            if ($hotel) {
                $hotel->status = false;
                $hotel->save();
                $hotel->delete();
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
            $hotel = $this->findHotel($id, true);
            
            if ($hotel) {
                $hotel->status = true;
                $hotel->save();
                $hotel->restore();
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
            $hotel = $this->findHotel($id, true);
            
            if ($hotel) {
                $this->removeImage($hotel->metas['image']);
                $hotel->users()->sync([]);
                $hotel->forceDelete();
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
            $this->hotel->onlyTrashed()
                ->get()
                ->each(function (Hotel $hotel) {
                    $this->removeImage($hotel->metas['image']);
                    $hotel->users()->sync([]);
                    $hotel->forceDelete();
                });
            
            return _response([
                'success' => true,
                'message' => 'Success',
            ]);
        }
        
        public function restoreAll()
        {
            $hotels = $this->hotel->onlyTrashed();
            
            if ($hotels->count()) {
                $hotels->get()
                    ->each(function (Hotel $hotel) {
                        $hotel->status = true;
                        $hotel->save();
                        $hotel->restore();
                    });
                return _response([
                    'success' => true,
                    'message' => 'Success',
                ]);
            }
            
            return _responseError([
                'message' => 'Destroyed Hotels Not Found',
            ], 'response', 404);
        }
        
        /***
         * @param $id
         *
         * @return \App\Hotel|null
         */
        private function findHotel($id, $trashed = false)
        {
            $hotel = $this->hotel->where('_id', $id)->where('status', !$trashed);
            
            if ($trashed) {
                $hotel = $hotel->onlyTrashed();
            }
            
            return $hotel->first();
        }
    }
