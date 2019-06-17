<?php
    
    namespace App;
    
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\SoftDeletes;
    
    class RoomCapacity extends Model
    {
        use SoftDeletes, BootModel;
        protected $table = "room_capacities";
        protected $fillable = ["name"];
        
        public function room()
        {
            return $this->belongsToMany(Room::class, 'room_capacity_rooms');
        }
        
        public function hotel()
        {
            return $this->belongsToMany(Hotel::class, 'room_capacity_hotels');
        }
    }
