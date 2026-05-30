@extends('layouts.admin')

@section('title', 'Banners | Admin')

@section('admin')
<div class="section-heading"><h1>Banners</h1></div>
<div class="row g-4">
    <div class="col-lg-5">
        <form class="tool-panel stacked-form" method="post" action="{{ route('admin.banners.store') }}">
            @csrf
            <h2>Add banner</h2>
            <input class="form-control" name="title" placeholder="Title" required>
            <input class="form-control" name="subtitle" placeholder="Subtitle">
            <input class="form-control" name="image_url" placeholder="Image URL" required>
            <input class="form-control" name="link_url" placeholder="Link URL">
            <input class="form-control" name="cta_label" placeholder="CTA label">
            <input class="form-control" name="placement" value="home" required>
            <input class="form-control" type="number" name="sort_order" value="0">
            <label class="form-check"><input class="form-check-input" type="checkbox" name="is_active" value="1" checked><span class="form-check-label">Active</span></label>
            <button class="btn btn-brand" type="submit">Save</button>
        </form>
    </div>
    <div class="col-lg-7">
        <div class="tool-panel">
            @foreach($banners as $banner)
                <div class="banner-row">
                    <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}">
                    <div><strong>{{ $banner->title }}</strong><p>{{ $banner->subtitle }}</p></div>
                    <form method="post" action="{{ route('admin.banners.destroy', $banner) }}">@csrf @method('DELETE')<button class="btn icon-btn ghost text-danger"><i data-lucide="trash-2"></i></button></form>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
