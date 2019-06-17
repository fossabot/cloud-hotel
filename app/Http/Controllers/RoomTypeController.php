<?php
    
    
    namespace App\Http\Controllers;
    
    
    use App\Http\Resources\RoomResource;
    use App\Http\Resources\RoomTypeResource;
    use App\RoomType;
    use Illuminate\Http\Request;
    use Illuminate\Validation\Validator;
    
    class RoomTypeController extends Controller
    {
        /***
         * @var \App\RoomType
         */
        private $type;
        
        /**
         * RoomTypeController constructor.
         *
         * @param \Illuminate\Http\Request $request
         * @param \App\RoomType            $roomType
         */
        public function __construct(Request $request, RoomType $roomType)
        {
            parent::__construct($request);
            $this->type = $roomType;
            
            if (!!$this->request->user()) {
                $_hotel = $this->request
                    ->user()
                    ->hotels()
                    ->select('id')
                    ->first();
                if (!!$_hotel) {
                    $this->hotelId = $_hotel->id;
                }
            }
        }
        
        /***
         * List Rooms
         * @method GET
         *
         * @route /rooms
         * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
         */
        public function index()
        {
            $type = $this->type;
            
            if ($this->hotelId > 0) {
                $type = $type->whereHas('hotel', function ($q) {
                    $q->where('hotel_id', $this->hotelId);
                });
            }
            
            return _response(
                RoomTypeResource::collection(
                    $type->get()
                )
            );
        }
        
        /***
         * @param $id
         * @method GET
         *
         * @route /rooms/$id
         * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
         */
        public function show($id)
        {
            $room = $this->findRoomType($id);
            if ($room) {
                return _response(new RoomTypeResource($room));
            }
            return _responseError([
                "message" => "Not Found",
                "id" => $id,
            ], '404');
        }
        
        /***
         * @method POST
         * @route /rooms
         * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
         */
        public function store()
        {
            $role = null;
            $this->_validate(
                [
                    'name' => [
                        'required',
                    ],
                ], [], [],
                function (Validator $validator) {
                    $name = $this->request->name;
                    if (!!$name) {
                        $check = $this->type->where('name', '=', $name);
                        if ($this->hotelId > 0) {
                            $check = $check->whereHas('hotel', function ($query) {
                                $query->where('id', $this->hotelId);
                            });
                        }
                        if ($check->count()) {
                            $validator->errors()->add('name', trans('validation.unique', ['attribute' => 'name']));
                        }
                        
                    }
                }
            );
            
            $roomType = $this->type->firstOrCreate([
                'name' => $this->request->name,
            ]);
            
            if ($this->hotelId > 0) {
                $roomType->hotel()
                    ->attach([
                        $this->hotelId,
                    ]);
            }
            
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
                        'required',
                    ],
                ], [], [],
                function (Validator $validator) use (&$id) {
                    $name = $this->request->name;
                    if (!!$name && $this->updateUnique($id, 'name', $name)) {
                        $validator->errors()->add('name', trans('validation.unique', ['attribute' => 'name']));
                    }
                }
            );
            
            $roomType = $this->findRoomType($id);
            
            if ($roomType) {
                $usedCheck = $roomType
                    ->hotel()
                    ->where('id', '!=', $this->hotelId)
                    ->count();
                if ($usedCheck) {
                    $type = $this->type->create([
                        'name' => $this->request->name,
                    ]);
                    if ($roomType->room()->count() > 0) {
                        $rooms = $roomType->room()
                            ->whereHas('hotel', function ($query) {
                                $query->where('id', $this->hotelId);
                            })
                            ->get();
                        $type->room()->attach($rooms);
                        $roomType->room()->detach($rooms);
                    }
                    if ($roomType->hotel()->count() > 0) {
                        $type->hotel()->attach([$this->hotelId]);
                        $roomType->hotel()->detach([$this->hotelId]);
                    }
                    
                } else if ($roomType->name !== $this->request->name) {
                    $roomType->name = $this->request->name;
                    $roomType->save();
                }
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
            return RoomType::where('_id', '!=', $id)->where($column, '=', $value)->count() > 0;
        }
        
        public function restore($id)
        {
            $roomType = $this->findRoomType($id, true);
            
            if ($roomType) {
                $roomType->restore();
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
        
        public function detachRoomType($id)
        {
            $roomType = $this->findRoomType($id);
            
            if ($roomType) {
                $roomType->hotel()->detach([$this->hotelId]);
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
            $roomType = $this->findRoomType($id, true);
            
            if ($roomType) {
                $roomType->hotel()->sync([]);
                $roomType->room()->sync([]);
                $roomType->forceDelete();
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
            $this->type->onlyTrashed()
                ->get()
                ->each(function (RoomType $type) {
                    $type->room()->sync([]);
                    $type->hotel()->sync([]);
                    $type->forceDelete();
                });
            
            return _response([
                'success' => true,
                'message' => 'Success',
            ]);
        }
        
        public function restoreAll()
        {
            $roomTypes = $this->type->onlyTrashed();
            
            if ($roomTypes->count()) {
                $roomTypes->get()
                    ->each(function (RoomType $type) {
                        $type->restore();
                    });
                return _response([
                    'success' => true,
                    'message' => 'Success',
                ]);
            }
            
            return _responseError([
                'message' => 'Destroyed Rooms Not Found',
            ], 'response', 404);
        }
        
        /***
         * @param      $id
         * @param bool $trashed
         *
         * @return mixed
         */
        private function findRoomType($id, $trashed = false)
        {
            $type = $this->type->where('_id', $id);
            
            if ($this->hotelId > 0) {
                $type = $type->whereHas('hotel', function ($q) {
                    $q->where('hotel_id', $this->hotelId);
                });
            }
            
            if ($trashed) {
                $type = $type->onlyTrashed();
            }
            
            return $type->first();
        }
    }
