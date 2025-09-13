<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    /** @use HasFactory<\Database\Factories\ReviewFactory> */
    use HasFactory;

    protected $fillable = [
        'book_id',
        'rating',
        'review',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    protected static function booted()
    {
        $clearCache = function (Review $review) {
            cache()->forget('book_show_' . $review->book_id);

            // Clear books index cache as well
            if (cache()->getStore() instanceof \Illuminate\Cache\TaggableStore) {
                cache()->tags('books_index')->flush();
            } else {
                cache()->increment('books_index_version');
            }
        };

        static::created($clearCache);
        static::updated($clearCache);
        static::deleted($clearCache);
    }
}
