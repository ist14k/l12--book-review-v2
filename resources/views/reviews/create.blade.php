@extends('layouts.app')

@section('content')
  <div class="mb-4">
    <h1 class="mb-2 text-2xl">Add Review for "{{ $book->title }}"</h1>
    <div class="book-author mb-4 text-lg font-semibold">by {{ $book->author }}</div>
  </div>

  <form action="{{ route('books.reviews.store', $book) }}" method="POST" class="space-y-4">
    @csrf

    <div>
      <label for="rating" class="mb-1 block text-sm font-medium text-gray-700">Rating</label>
      <select name="rating" id="rating" class="input">
        <option value="">Select a rating</option>
        @for ($i = 5; $i >= 1; $i--)
          <option value="{{ $i }}" {{ old('rating') == $i ? 'selected' : '' }}>
            {{ $i }} {{ Str::plural('star', $i) }}
          </option>
        @endfor
      </select>
      @error('rating')
        <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
      @enderror
    </div>

    <div>
      <label for="review" class="mb-1 block text-sm font-medium text-gray-700">Review (optional)</label>
      <textarea name="review" id="review" rows="5" class="input" placeholder="Write your review here...">{{ old('review') }}</textarea>
      @error('review')
        <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
      @enderror
    </div>

    <div class="flex items-center space-x-2">
      <button type="submit" class="btn">Add Review</button>
      <a href="{{ route('books.show', $book) }}" class="text-gray-600 hover:text-gray-800">Cancel</a>
    </div>
  </form>
@endsection
