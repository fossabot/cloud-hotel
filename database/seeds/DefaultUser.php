<?php

use Illuminate\Database\Seeder;

class DefaultUser extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rootUser = \App\User::create([
            'email' =>  env('ROOT_EMAIL'),
            'password'  =>  bcrypt(env('ROOT_PASSWORD')),
            'metas' =>  [
                'firstName' =>  'Root',
                'lastName'  =>  'Root'
            ]
        ]);
        
        $rootUser->roles()->attach(
            \App\Role::find(1)
        );
    }
}
