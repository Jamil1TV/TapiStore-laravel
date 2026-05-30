@extends('layouts.admin')

@section('title', 'Categories | Admin')

@section('admin')
<div class="section-heading"><h1>Categories</h1></div>
<div class="row g-4">
    <div class="col-lg-5">
        <form class="tool-panel stacked-form" method="post" action="{{ route('admin.categories.store') }}">
            @csrf
            <h2>Add category</h2>
            <input class="form-control" name="name" placeholder="Name" required>
            <select class="form-select" name="parent_id"><option value="">No parent</option>@foreach($parents as $parent)<option value="{{ $parent->id }}">{{ $parent->name }}</option>@endforeach</select>
            <input class="form-control" name="image_url" placeholder="Image URL">
            <input class="form-control" type="number" name="sort_order" placeholder="Sort order" value="0">
            <textarea class="form-control" name="description" placeholder="Description"></textarea>
            <label class="form-check"><input class="form-check-input" type="checkbox" name="is_active" value="1" checked><span class="form-check-label">Active</span></label>
            <button class="btn btn-brand" type="submit">Save</button>
        </form>
    </div>
    <div class="col-lg-7">
        <div class="tool-panel table-responsive">
            <table class="table align-middle">
                <thead><tr><th>Name</th><th>Parent</th><th>Active</th><th></th></tr></thead>
                <tbody>
                @foreach($categories as $category)
                    <tr>
                        <form method="post" action="{{ route('admin.categories.update', $category) }}">
                            @csrf @method('PATCH')
                            <td><input class="form-control" name="name" value="{{ $category->name }}"></td>
                            <td><select class="form-select" name="parent_id"><option value="">None</option>@foreach($parents as $parent)<option value="{{ $parent->id }}" @selected($category->parent_id === $parent->id)>{{ $parent->name }}</option>@endforeach</select></td>
                            <td><input type="checkbox" name="is_active" value="1" @checked($category->is_active)></td>
                            <td class="text-end">
                                <input type="hidden" name="image_url" value="{{ $category->image_url }}">
                                <input type="hidden" name="description" value="{{ $category->description }}">
                                <input type="hidden" name="sort_order" value="{{ $category->sort_order }}">
                                <button class="btn icon-btn ghost" data-bs-toggle="tooltip" title="Save"><i data-lucide="save"></i></button>
                        </form>
                                <form class="d-inline" method="post" action="{{ route('admin.categories.destroy', $category) }}">@csrf @method('DELETE')<button class="btn icon-btn ghost text-danger" data-bs-toggle="tooltip" title="Delete"><i data-lucide="trash-2"></i></button></form>
                            </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
