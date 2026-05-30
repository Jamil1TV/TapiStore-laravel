@extends('layouts.admin')

@section('title', 'Brands | Admin')

@section('admin')
<div class="section-heading"><h1>Brands</h1></div>
<div class="row g-4">
    <div class="col-lg-5">
        <form class="tool-panel stacked-form" method="post" action="{{ route('admin.brands.store') }}">
            @csrf
            <h2>Add brand</h2>
            <input class="form-control" name="name" placeholder="Name" required>
            <input class="form-control" name="logo_url" placeholder="Logo URL">
            <textarea class="form-control" name="description" placeholder="Description"></textarea>
            <label class="form-check"><input class="form-check-input" type="checkbox" name="is_active" value="1" checked><span class="form-check-label">Active</span></label>
            <button class="btn btn-brand" type="submit">Save</button>
        </form>
    </div>
    <div class="col-lg-7">
        <div class="tool-panel">
            @foreach($brands as $brand)
                <div class="admin-inline-row">
                    <form class="d-flex align-items-center gap-2 flex-grow-1" method="post" action="{{ route('admin.brands.update', $brand) }}">
                        @csrf @method('PATCH')
                        <input class="form-control" name="name" value="{{ $brand->name }}">
                        <input type="hidden" name="description" value="{{ $brand->description }}">
                        <input type="hidden" name="logo_url" value="{{ $brand->logo_url }}">
                        <label class="form-check"><input class="form-check-input" type="checkbox" name="is_active" value="1" @checked($brand->is_active)></label>
                        <button class="btn icon-btn ghost" data-bs-toggle="tooltip" title="Save"><i data-lucide="save"></i></button>
                    </form>
                    <form method="post" action="{{ route('admin.brands.destroy', $brand) }}">
                        @csrf @method('DELETE')
                        <button class="btn icon-btn ghost text-danger" data-bs-toggle="tooltip" title="Delete"><i data-lucide="trash-2"></i></button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
