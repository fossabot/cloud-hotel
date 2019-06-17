<?php
    
    
    namespace App\Http\Resources;
    
    
    use Illuminate\Http\Resources\Json\JsonResource;
    
    class UserResource extends JsonResource
    {
        public function toArray($request)
        {
            $metas = $this->metas;
            $displayName = $this->metas['firstName'] . $this->metas['lastName'];
            return [
                'id' => $this->_id,
                'displayName' => $displayName,
                'email' => $this->email,
                'metas' => $metas,
                'roles' => new RoleResource($this->roles()->first()),
                'hotels' => new HotelResource($this->hotels()->first()),
            ];
        }
    }
