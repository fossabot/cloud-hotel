<?php
    
    namespace App;
    
    use Illuminate\Auth\Access\AuthorizationException;
    use Illuminate\Auth\Authenticatable;
    use Laravel\Lumen\Auth\Authorizable;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
    use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
    use Illuminate\Database\Eloquent\SoftDeletes;
    
    class User extends Model
    {
        use SoftDeletes, BootModel;
        
        protected $casts = [
            "metas" => "Array",
        ];
        
        protected $fillable = ["_id", "email", "password", "metas", "status"];
        
        protected $hidden = [
            'password', 'remember_token',
        ];
        
        public function roles()
        {
            return $this->belongsToMany(Role::class, 'role_users');
        }
        
        public function hotels()
        {
            return $this->belongsToMany(Hotel::class, 'hotel_users');
        }
    
        /***
         * @param array $permissions
         *
         * @return bool|void
         * @throws \Illuminate\Auth\Access\AuthorizationException
         */
        public function can(array $permissions = []) :bool
        {
            $roles = $this->roles()
                ->select('permissions')
                ->get()
                ->pluck('permissions')
                ->flatten()
                ->values()
                ->toArray()
            ;
            
            if(count(array_intersect($roles, $permissions)) === 0)
            {
                throw new AuthorizationException("This Action is unauthorized", 401);
                return false;
            }
            return true;
        }
    }
