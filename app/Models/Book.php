<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    /** @use HasFactory<\Database\Factories\BookFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'description',
    ];

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // query scopes
    public function scopeWithReviewsCount(Builder $query, $from = null, $to = null)
    {
        return $query->withCount([
            'reviews' => fn(Builder $query) => $this->rangeBuilder($query, 'created_at', $from, $to)
        ]);
    }

    public function scopeWithAverageRating(Builder $query, $from = null, $to = null)
    {
        return $query->withAvg([
            'reviews' => fn(Builder $query) => $this->rangeBuilder($query, 'created_at', $from, $to)
        ], 'rating');
    }

    public function scopeTitle(Builder $query, string $title)
    {
        return $query->where('title', 'like', '%' . $title . '%');
    }

    public function scopePopular(Builder $query, $from = null, $to = null)
    {
        return $query->withReviewsCount($from, $to)->orderBy('reviews_count', 'desc');
    }

    public function scopeHighestRated(Builder $query, $from = null, $to = null)
    {
        return $query->withAverageRating($from, $to)->orderBy('reviews_avg_rating', 'desc');
    }

    public function scopeMinReviews(Builder $query, int $count)
    {
        return $query->having('reviews_count', '>=', $count);
    }

    private function rangeBuilder(Builder $query, $column, $from = null, $to = null)
    {
        if ($from && !$to) {
            $query->where($column, '>=', $from);
        }
        if (!$from && $to) {
            $query->where($column, '<=', $to);
        }
        if ($from && $to) {
            $query->whereBetween($column, [$from, $to]);
        }
    }

    public function scopePopularLastMonth(Builder $query): Builder
    {
        return $query->popular(now()->subMonth(), now())
            ->highestRated(now()->subMonth(), now())
            ->minReviews(2);
    }

    public function scopePopularLast6Months(Builder $query): Builder
    {
        return $query->popular(now()->subMonths(6), now())
            ->highestRated(now()->subMonths(6), now())
            ->minReviews(5);
    }

    public function scopeHighestRatedLastMonth(Builder $query): Builder
    {
        return $query->highestRated(now()->subMonth(), now())
            ->popular(now()->subMonth(), now())
            ->minReviews(2);
    }

    public function scopeHighestRatedLast6Months(Builder $query): Builder
    {
        return $query->highestRated(now()->subMonths(6), now())
            ->popular(now()->subMonths(6), now())
            ->minReviews(5);
    }

    protected static function booted()
    {
        static::updated(function (Book $book) {
            cache()->forget('book_show_' . $book->id);
        });

        static::deleted(function (Book $book) {
            cache()->forget('book_show_' . $book->id);
        });
    }
}
