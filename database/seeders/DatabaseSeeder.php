<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        Book::factory(50)->create()->each(function ($book) {
            Review::factory()->count(rand(20, 30))->create([
                'book_id' => $book->id,
                'created_at' => fake()->dateTimeBetween($book->created_at, 'now'),
                'updated_at' => function (array $attributes) {
                    return fake()->dateTimeBetween($attributes['created_at'], 'now');
                },
            ]);
        });

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
