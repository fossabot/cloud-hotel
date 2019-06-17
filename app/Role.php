<?php
    
    namespace App;
    
    
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\SoftDeletes;
    
    class Role extends Model
    {
        use SoftDeletes, BootModel;
        protected $table = "roles";
        protected $fillable = ["name", "permissions", "_id"];
        protected $hidden = ["pivot"];
        protected $casts = [
            "permissions" => "array",
        ];
        
        public function users()
        {
            return $this->belongsToMany(User::class, 'role_users');
        }
    }
