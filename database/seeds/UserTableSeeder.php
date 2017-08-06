<?php

use Illuminate\Database\Seeder;


class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $User               = new App\User();
        $User->email        = 'admin1';
        $User->password     = bcrypt('123456');
        $User->first_name   = 'admin 1';
        $User->last_name    = '';
        $User->role         = '1';
        $User->save();

        $User               = new App\User();
        $User->email        = 'admin2';
        $User->password     = bcrypt('123456');
        $User->first_name   = 'admin 2';
        $User->last_name    = '';
        $User->role         = '1';
        $User->save();

        $User               = new App\User();
        $User->email        = 'cashier1';
        $User->password     = bcrypt('123456');
        $User->first_name   = 'cashier 1';
        $User->last_name    = '';
        $User->role         = '2';
        $User->save();
        
        $User               = new App\User();
        $User->email        = 'cashier2';
        $User->password     = bcrypt('123456');
        $User->first_name   = 'cashier 2';
        $User->last_name    = '';
        $User->role         = '2';
        $User->save();

        $User               = new App\User();
        $User->email        = 'accounting1';
        $User->password     = bcrypt('123456');
        $User->first_name   = 'accounting 1';
        $User->last_name    = '';
        $User->role         = '3';
        $User->save();

        $User               = new App\User();
        $User->email        = 'accounting2';
        $User->password     = bcrypt('123456');
        $User->first_name   = 'accounting 2';
        $User->last_name    = '';
        $User->role         = '3';
        $User->save();
    }
}
