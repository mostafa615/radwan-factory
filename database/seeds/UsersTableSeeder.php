<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = \App\User::create([
            'name' => 'super admin',
            //'last_name' => 'admin',
            'email' => 'super_admin@app.com',
            'username' => 'superadmin',
            'phone' => '01157404397',
            'type' => 'super_admin',
            'active' => 1,
            'account_confirm' => 1,
            'password' => bcrypt('123456'),
        ]);

        $user->attachRole('super_admin');

    }//end of run
}//end seeder
