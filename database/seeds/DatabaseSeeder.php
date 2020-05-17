<?php

use App\Post;
use App\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //$this->call(PostSeeder::class);

        factory(User::class)->create(['email' => 'solo@solo', 'name' => 'gio solo', 'password' => bcrypt('solo')]);
        factory(User::class)->create(['email' => 'test@test', 'name' => 'test solo', 'password' => bcrypt('test')]);
        factory(Post::class, 5)->create(['user_id' => 1]);
        factory(Post::class, 5)->create(['user_id' => 2]);
    }
}
