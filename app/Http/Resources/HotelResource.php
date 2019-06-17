<?php
    
    
    namespace App\Http\Resources;
    
    
    use Illuminate\Http\Resources\Json\JsonResource;
    use Illuminate\Support\Arr;

    class HotelResource extends JsonResource
    {
        public function toArray($request)
        {
            $metas = Arr::except($this->metas, 'image');
            $metas['phone'] = $this->phone;
            $metas['email'] = $this->email;
            return [
                'id'    =>  $this->_id,
                'name'  =>  $this->name,
                'slug'  =>  $this->slug,
                'image' =>  url($this->metas['image']),
                'email' =>  $this->email,
                'metas' =>  $metas,
            ];
        }
    }
