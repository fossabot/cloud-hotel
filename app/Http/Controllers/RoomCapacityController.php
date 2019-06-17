<?php
    
    
    namespace App\Http\Controllers;
    
    
    use App\Http\Resources\RoomResource;
    use App\Http\Resources\RoomCapacityResource;
    use App\RoomCapacity;
    use Illuminate\Http\Request;
    use Illuminate\Validation\Validator;
    
    class RoomCapacityController extends Controller
    {
        /***
         * @var \App\RoomCapacity
         */
        private $capacity;
        
        /**
         * RoomCapacityController constructor.
         *
         * @param \Illuminate\Http\Request $request
         * @param \App\RoomCapacity            $roomCapacity
         */
        public function __construct(Request $request, RoomCapacity $roomCapacity)
        {
            parent::__construct($request);
            $this->capacity = $roomCapacity;
            
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
         * @route /room-capacities/
         * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
         */
        public function index()
        {
            $capacity = $this->capacity;
            
            if ($this->hotelId > 0) {
                $capacity = $capacity->whereHas('hotel', function ($q) {
                    $q->where('hotel_id', $this->hotelId);
                });
            }
     
            return _response(
                RoomCapacityResource::collection(
                    $capacity->get()
                )
            );
        }
        
        /***
         * @param $id
         * @method GET
         *
         * @route /room-capacities//$id
         * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
         */
        public function show($id)
        {
            $room = $this->findRoomCapacity($id);
            if ($room) {
                return _response(new RoomCapacityResource($room));
            }
            return _responseError([
                "message" => "Not Found",
                "id" => $id,
            ], '404');
        }
        
        /***
         * @method POST
         * @route /room-capacities/
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
                        $check = $this->capacity->where('name', '=', $name);
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
            
            $roomCapacity = $this->capacity->firstOrCreate([
                'name' => $this->request->name,
            ]);
            
            if ($this->hotelId > 0) {
                $roomCapacity->hotel()
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
            
            $roomCapacity = $this->findRoomCapacity($id);
            
            if ($roomCapacity) {
    
                $usedCheck = $roomCapacity
                    ->hotel()
                    ->where('id', '!=', $this->hotelId)
                    ->count();
                if ($usedCheck) {
                    $capacity = $this->capacity->create([
                        'name' => $this->request->name,
                    ]);
                    if($roomCapacity->room()->count() > 0) {
                        $rooms = $roomCapacity->room()
                            ->whereHas('hotel', function($query) {
                                $query->where('id', $this->hotelId);
                            })
                            ->get();
                        $capacity->room()->attach($rooms);
                        $roomCapacity->room()->detach($rooms);
                    }
                    if($roomCapacity->hotel()->count() > 0) {
                        $capacity->hotel()->attach([$this->hotelId]);
                        $roomCapacity->hotel()->detach([$this->hotelId]);
                    }
        
                } else if ($roomCapacity->name !== $this->request->name) {
                    $roomCapacity->name = $this->request->name;
                    $roomCapacity->save();
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
            return RoomCapacity::where('_id', '!=', $id)->where($column, '=', $value)->count() > 0;
        }
    
    
        public function detachRoomCapacity($id)
        {
            $capacity = $this->findRoomCapacity($id);
        
            if ($capacity) {
                $capacity->hotel()->detach([$this->hotelId]);
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
            $roomCapacity = $this->findRoomCapacity($id);
            
            if ($roomCapacity) {
                $roomCapacity->delete();
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
            $roomCapacity = $this->findRoomCapacity($id, true);
            
            if ($roomCapacity) {
                $roomCapacity->restore();
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
            $roomCapacity = $this->findRoomCapacity($id, true);
            
            if ($roomCapacity) {
                $roomCapacity->hotel()->sync([]);
                $roomCapacity->room()->sync([]);
                $roomCapacity->forceDelete();
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
            $this->capacity->onlyTrashed()
                ->get()
                ->each(function (RoomCapacity $capacity) {
                    $capacity->room()->sync([]);
                    $capacity->hotel()->sync([]);
                    $capacity->forceDelete();
                });
            
            return _response([
                'success' => true,
                'message' => 'Success',
            ]);
        }
        
        public function restoreAll()
        {
            $roomCapacitys = $this->capacity->onlyTrashed();
            
            if ($roomCapacitys->count()) {
                $roomCapacitys->get()
                    ->each(function (RoomCapacity $capacity) {
                        $capacity->restore();
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
        private function findRoomCapacity($id, $trashed = false)
        {
            $capacity = $this->capacity->where('_id', $id);
     
            if ($this->hotelId > 0) {
                $capacity = $capacity->whereHas('hotel', function ($q) {
                    $q->where('hotel_id', $this->hotelId);
                });
            }
            
            if ($trashed) {
                $capacity = $capacity->onlyTrashed();
            }
            
            return $capacity->first();
        }
    }
