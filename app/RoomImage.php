<?php
    
    namespace App;
    
    
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\SoftDeletes;
    
    class RoomImage extends Model
    {
        use SoftDeletes, BootModel;
        protected $table = "room_images";
        protected $fillable = ["room_id", "hotel_id", 'image', "_id"];
        protected $hidden = ["pivot"];
        
        public function hotel()
        {
            return $this->hasOne(Hotel::class);
        }
        
        public function room()
        {
            return $this->hasOne(Room::class);
        }
    }
