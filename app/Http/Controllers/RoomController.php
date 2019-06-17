<?php
    
    
    namespace App\Http\Controllers;
    
    
    use App\Http\Resources\RoomResource;
    use App\Room;
    use App\Role;
    use App\RoomCapacity;
    use App\RoomType;
    use App\RoomImage;
    use App\User;
    use Illuminate\Http\Request;
    use Illuminate\Support\Str;
    use Illuminate\Validation\Validator;
    
    class RoomController extends Controller
    {
        /**
         * @var \App\Room
         */
        private $room;
        
        /***
         * @var \App\User
         */
        private $user;
        
        /***
         * @var \App\RoomCapacity
         */
        private $capacity;
        
        /***
         * @var \App\RoomType
         */
        private $type;
        
        /***
         * @var \App\RoomImage
         */
        private $image;
        
        /***
         * RoomController constructor.
         *
         * @param \Illuminate\Http\Request $request
         * @param \App\Room                $room
         */
        public function __construct(Request $request, Room $room, RoomCapacity $capacity, RoomType $type, RoomImage $image)
        {
            parent::__construct($request);
            $this->room = $room;
            $this->capacity = $capacity;
            $this->image = $image;
            $this->type = $type;
            
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
            $room = $this->room
                ->with([
                    'type' => function ($query) {
                        $query->whereHas('hotel', function ($query) {
                            $query->where('id', $this->hotelId);
                        });
                    },
                    'capacity' => function ($query) {
                        $query->whereHas('hotel', function ($query) {
                            $query->where('id', $this->hotelId);
                        });
                    },
                    'image'
                ]);
            
            if ($this->hotelId > 0) {
                $room = $room->whereHas('hotel', function ($q) {
                    $q->where('hotel_id', $this->hotelId);
                });
            }
            
            return _response(
                RoomResource::collection(
                    $room->get()
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
            $room = $this->findRoom($id);
            if ($room) {
                return _response(new RoomResource($room));
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
            $this->_validate(
                [
                    'name' => [
                        'required', 'alpha_num',
                    ],
                    'type_id' => [
                        'required', 'exists:room_types,_id',
                    ],
                    'capacity_id' => [
                        'required', 'exists:room_capacities,_id',
                    ],
                    'image' => [
                        'required', 'mimes:jpeg,png', 'max:5120',
                    ],
                ]
            );
            
            $room = $this->room->firstOrCreate([
                'name' => $this->request->name,
            ]);
            
            if ($this->request->hasFile('image')) {
                $this->image->create([
                    'room_id' => $room->id,
                    'hotel_id' => $this->hotelId,
                    'image' => upload($this->request->file('image'), 'rooms'),
                ]);
            }
            
            $room->capacity()->attach(
                $this->capacity->where('_id', $this->request->capacity_id)->first(),
                ['hotel_id' => $this->hotelId]
            );
            $room->type()->attach(
                $this->type->where('_id', $this->request->type_id)->first(),
                ['hotel_id' => $this->hotelId]
            );
            $room->hotel()->attach([$this->hotelId]);
            
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
                        'alpha_numeric',
                    ],
                    'type_id' => [
                        'min:1', 'array', 'exists:room_types,_id',
                    ],
                    'capacity_id' => [
                        'min:1', 'array', 'exists:room_capacities,_id',
                    ],
                    'image' => [
                        'mimes:jpeg,png', 'max:5120',
                    ],
                ], [], [],
                function (Validator $validator) use (&$id) {
                    $name = $this->request->name;
                    
                    if (!!$name && $this->updateUnique($id, 'name', $name)) {
                        $validator->errors()->add('name', trans('validation.unique', ['attribute' => 'name']));
                    }
                }
            );
            
            $room = $this->findRoom($id);
            
            if ($room) {
                $usedCheck = $room
                    ->hotel()
                    ->where('id', '!=', $this->hotelId)
                    ->count();
                if ($usedCheck) {
                    $room = $this->room->firstOrCreate([
                        'name' => $this->request->name,
                    ], [
                        'image' => $this->request->hasFile('image') ? upload($this->request->file('image'), 'rooms') : null,
                    ]);
                    
                    $room->capacity()->attach(
                        $this->capacity->where('_id', $this->request->capacity_id)->first()
                    );
                    $room->type()->attach(
                        $this->type->where('_id', $this->request->type_id)->first()
                    );
                    $room->hotel()->attach([$this->hotelId]);
                    
                } else {
                    if ($room->name !== $this->request->name) {
                        $room->name = $this->request->name;
                    }
                    
                    if ($this->request->hasFile('image')) {
                        $this->removeImage($room->name);
                        $room->name = upload($this->request->file('image'));
                    }
                    
                    if (is_array($this->request->capacity_id) && count($this->request->capacity_id) > 0) {
                        $room->capacity()->sync($this->request->capacity_id);
                    }
                    if (is_array($this->request->type_id) && count($this->request->type_id) > 0) {
                        $room->type()->sync([$this->request->type_id]);
                    }
                    
                    $room->save();
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
            return Room::where('_id', '!=', $id)->where($column, '=', $value)->count() > 0;
        }
        
        public function destroy($id)
        {
            $room = $this->findRoom($id);
            
            if ($room) {
                $room->delete();
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
            $room = $this->findRoom($id, true);
            
            if ($room) {
                $room->restore();
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
            $room = $this->findRoom($id, true);
            
            if ($room) {
                $this->removeImage($room->image);
                $room->capacity()->sync([]);
                $room->type()->sync([]);
                $room->forceDelete();
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
            $this->room->onlyTrashed()
                ->get()
                ->each(function (Room $room) {
                    $this->removeImage($room->image);
                    $room->capacity()->sync([]);
                    $room->type()->sync([]);
                    $room->forceDelete();
                });
            
            return _response([
                'success' => true,
                'message' => 'Success',
            ]);
        }
        
        public function restoreAll()
        {
            $rooms = $this->room->onlyTrashed();
            
            if ($rooms->count()) {
                $rooms->get()
                    ->each(function (Room $room) {
                        $room->status = true;
                        $room->save();
                        $room->restore();
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
         * @param $id
         *
         * @return \App\Room|null
         */
        private function findRoom($id, $trashed = false)
        {
            $room = $this->room->where('_id', $id)->where('status', !$trashed);
            
            if ($trashed) {
                $room = $room->onlyTrashed();
            }
            
            return $room->first();
        }
    }
