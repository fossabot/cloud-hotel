<?php

use Illuminate\Database\Seeder;

class DefaultRole extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Role::create([
            'name'  =>  'Root',
            'permissions'   =>  config('permissions')
        ]);
    
        \App\Role::create([
            'name'  =>  'User',
            'permissions'   =>  null
        ]);
    
        \App\Role::create([
            'name'  =>  'Manager',
            'permissions'   =>  null
        ]);
    
        \App\Role::create([
            'name'  =>  'Resepsiyonist',
            'permissions'   =>  null
        ]);
    }
}
