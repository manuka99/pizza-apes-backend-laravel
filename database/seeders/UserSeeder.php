<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // User::factory()->times(10)->create();
        User::updateOrCreate([
            'fname'=> "manuka",
            'lname'=> "yasas",
            'email'=> "manukayasas99@gmail.com",
            'password'=> "12345678",
            ]);
        // $user = new  User();
        // $user->fname = "manuka";
        // $user->lname = "yasas";
        // $user->email = "manukayasas99@gmail.com";
        // $user->password = "12345678";
        // $user->update();
    }
}
