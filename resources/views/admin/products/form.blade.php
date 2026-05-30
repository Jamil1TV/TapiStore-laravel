@extends('layouts.admin')

@section('title', ($product->exists ? 'Edit Product' : 'Add Product').' | Admin')

@section('admin')
<div class="section-heading">
    <h1>{{ $product->exists ? 'Edit product' : 'Add product' }}</h1>
    <a href="{{ route('admin.products.index') }}">Back</a>
</div>
<form method="post" action="{{ $product->exists ? route('admin.products.update', $product) : route('admin.products.store') }}" enctype="multipart/form-data" class="tool-panel stacked-form">
    @csrf
    @if($product->exists) @method('PATCH') @endif
    <div class="row g-3">
        <label class="col-md-8">Name<input class="form-control" name="name" value="{{ old('name', $product->name) }}" required></label>
        <label class="col-md-4">SKU<input class="form-control" name="sku" value="{{ old('sku', $product->sku) }}" required></label>
        <label class="col-md-6">Category<select class="form-select" name="category_id" required>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(old('category_id', $product->category_id)==$category->id)>{{ $category->name }}</option>@endforeach</select></label>
        <label class="col-md-6">Brand<select class="form-select" name="brand_id"><option value="">None</option>@foreach($brands as $brand)<option value="{{ $brand->id }}" @selected(old('brand_id', $product->brand_id)==$brand->id)>{{ $brand->name }}</option>@endforeach</select></label>
        <label class="col-md-4">Price<input class="form-control" type="number" step="0.01" name="price" value="{{ old('price', $product->price) }}" required></label>
        <label class="col-md-4">Compare price<input class="form-control" type="number" step="0.01" name="compare_at_price" value="{{ old('compare_at_price', $product->compare_at_price) }}"></label>
        <label class="col-md-4">Cost<input class="form-control" type="number" step="0.01" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}"></label>
        <label class="col-md-6">Stock<input class="form-control" type="number" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" required></label>
        <label class="col-md-6">Low stock threshold<input class="form-control" type="number" name="low_stock_threshold" value="{{ old('low_stock_threshold', $product->low_stock_threshold ?? 5) }}" required></label>
        <label class="col-12">Summary<input class="form-control" name="summary" value="{{ old('summary', $product->summary) }}"></label>
        <label class="col-12">Description<textarea class="form-control" name="description" rows="7">{{ old('description', $product->description) }}</textarea></label>
        <label class="col-md-6">Meta title<input class="form-control" name="meta_title" value="{{ old('meta_title', $product->meta_title) }}"></label>
        <label class="col-md-6">Meta description<input class="form-control" name="meta_description" value="{{ old('meta_description', $product->meta_description) }}"></label>
        <label class="col-12">Image URLs<textarea class="form-control" name="image_urls" rows="3" placeholder="One URL per line"></textarea></label>
        <label class="col-12">Upload images<input class="form-control" type="file" name="images[]" multiple accept="image/*"></label>
        @if($product->exists)
            <label class="form-check ms-2"><input class="form-check-input" type="checkbox" name="replace_images" value="1"><span class="form-check-label">Replace existing images</span></label>
        @endif
        <label class="form-check ms-2"><input class="form-check-input" type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active))><span class="form-check-label">Active</span></label>
        <label class="form-check ms-2"><input class="form-check-input" type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product->is_featured))><span class="form-check-label">Featured</span></label>
    </div>
    <button class="btn btn-brand mt-3" type="submit">Save product</button>
</form>
@endsection
