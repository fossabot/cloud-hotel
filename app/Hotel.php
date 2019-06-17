<?php
    
    namespace App;
    
    
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\SoftDeletes;

    class Hotel extends Model
    {
        use SoftDeletes, BootModel;
        protected $table = "hotels";
        protected $fillable = ["name", "slug", "metas", "_id", "status", "email", "phone"];
        
        protected $casts = [
            "metas" => "array",
        ];
    
        public function users()
        {
            return $this->belongsToMany(User::class, 'hotel_users');
        }
    }
