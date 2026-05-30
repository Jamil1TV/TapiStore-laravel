@extends('layouts.admin')

@section('title', 'Reviews | Admin')

@section('admin')
<div class="section-heading"><h1>Review moderation</h1></div>
<div class="tool-panel">
    @foreach($reviews as $review)
        <div class="review-admin-row">
            <div>
                <strong>{{ $review->product->name }}</strong>
                <p>{{ $review->body }}</p>
                <small>{{ $review->user->name }} · {{ $review->rating }} stars · {{ $review->status }}</small>
            </div>
            <form method="post" action="{{ route('admin.reviews.update', $review) }}" class="d-flex gap-2">
                @csrf @method('PATCH')
                <select class="form-select" name="status">
                    @foreach(['pending','approved','rejected'] as $status)
                        <option value="{{ $status }}" @selected($review->status === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
                <button class="btn icon-btn btn-brand" data-bs-toggle="tooltip" title="Save"><i data-lucide="save"></i></button>
            </form>
        </div>
    @endforeach
    {{ $reviews->links() }}
</div>
@endsection
