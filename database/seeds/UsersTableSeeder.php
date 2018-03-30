<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Remove any existing data
        DB::table('users')->truncate();

        $faker = Faker\Factory::create('En_US');

        // Generate some dummy data
        for ($i=0; $i<30; $i++) {
            User::create([
                'email' => $faker->unique()->email,
                'name' => $faker->name(),
                'description' => $faker->text(3500),
                'status' => rand(0, 1)
            ]);
        }
    }
}
