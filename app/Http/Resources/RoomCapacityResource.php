<?php
    
    
    namespace App\Http\Resources;
    
    
    use Illuminate\Http\Resources\Json\JsonResource;
    
    class RoomCapacityResource extends JsonResource
    {
        public function toArray($request)
        {
            $data = [
                'id' => $this->_id,
                'name' => $this->name,
                'room' =>  $this->room
            ];
    
    
            $hotelId = $request->user()->hotels()
                ->select('id')
                ->first();
            if (!!$hotelId) {
                $data['room'] = $this->room()->wherePivot('hotel_id', '=', $hotelId->id)->count();
            }
    
            return $data;
        }
    }
