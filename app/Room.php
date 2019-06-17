<?php
    
    namespace App;
    
    
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\SoftDeletes;

    class Room extends Model
    {
        use SoftDeletes, BootModel;
        protected $table = "rooms";
        protected $fillable = ["name"];
        
        protected $casts = [
            "metas" => "array",
        ];
    
        public function image()
        {
            return $this->hasOne(RoomImage::class, 'room_id');
        }
    
        public function hotel()
        {
            return $this->belongsToMany(Hotel::class, 'room_hotels');
        }
    
        public function capacity()
        {
            return $this->belongsToMany(RoomCapacity::class, 'room_capacity_rooms');
        }
    
        public function type()
        {
            return $this->belongsToMany(RoomType::class, 'room_type_rooms');
        }
    }
