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

        factory(User::class)->create(['email' => 'solo@solo', 'name' => 'gio solo']);
        factory(Post::class, 10)->create(['user_id' => 1]);
    }
}
