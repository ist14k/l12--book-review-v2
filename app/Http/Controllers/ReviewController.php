<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function create(Book $book)
    {
        return view('reviews.create', compact('book'));
    }

    public function store(Request $request, Book $book)
    {
        $data = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'review' => ['nullable', 'string', 'max:1000'],
        ]);

        $book->reviews()->create($data);
        // Alternatively, you can use:
        // $data['book_id'] = $book->id;

        return redirect()
            ->route('books.show', $book)
            ->with('success', 'Review added successfully!');
    }
}
