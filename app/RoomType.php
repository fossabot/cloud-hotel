<?php
    
    namespace App;
    
    
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\SoftDeletes;

    class RoomType extends Model
    {
        use SoftDeletes, BootModel;
        protected $table = "room_types";
        protected $fillable = ["name"];
        
        public function room()
        {
            return $this->belongsToMany(Room::class, 'room_type_rooms');
        }
    
        public function hotel()
        {
            return $this->belongsToMany(Hotel::class,'room_type_hotels');
        }
    }
