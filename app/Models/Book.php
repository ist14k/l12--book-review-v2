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
    public function scopeTitle(Builder $query, string $title)
    {
        return $query->where('title', 'like', '%' . $title . '%');
    }

    public function scopePopular(Builder $query, $from = null, $to = null)
    {
        return $query->withCount([
            'reviews' => fn(Builder $query) => $this->rangeBuilder($query, 'created_at', $from, $to)
        ])->orderBy('reviews_count', 'desc');
    }

    public function scopeHighestRated(Builder $query, $from = null, $to = null)
    {
        return $query->withAvg([
            'reviews' => fn(Builder $query) => $this->rangeBuilder($query, 'created_at', $from, $to)
        ], 'rating')->orderBy('reviews_avg_rating', 'desc');
    }

    public function scopeMinReviews(Builder $query, int $count = 1)
    {
        return $query->where('reviews_count', '>=', $count);
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
}
