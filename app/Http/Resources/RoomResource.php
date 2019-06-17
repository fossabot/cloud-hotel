<?php
    
    
    namespace App\Http\Resources;
    
    
    use Illuminate\Http\Resources\Json\JsonResource;
    
    class RoomResource extends JsonResource
    {
        public function toArray($request)
        {
            $data = [
                'id' => $this->_id,
                'name' => $this->name,
                'type' => $this->type ? new RoomTypeResource($this->type()->first()) : null,
                'capacity' => $this->capacity ? new RoomCapacityResource($this->capacity()->first()) : null,
            ];
            
            if ($this->image) {
                $hotelId = $request->user()->hotels()
                    ->select('id')
                    ->first();
                if (!!$hotelId) {
                    $data['image'] = url(
                        $this->image()->where('hotel_id', $hotelId->id)->first()->image
                    );
                }
            }
            return $data;
        }
    }
