@extends('layouts.admin')

@section('title', 'Products | Admin')

@section('admin')
<div class="section-heading">
    <h1>Products</h1>
    <a class="btn btn-brand" href="{{ route('admin.products.create') }}"><i data-lucide="plus"></i> Product</a>
</div>
<form class="admin-search" method="get"><input class="form-control" name="q" value="{{ request('q') }}" placeholder="Search name or SKU"></form>
<div class="table-responsive tool-panel">
    <table class="table align-middle">
        <thead><tr><th>Product</th><th>Category</th><th>Stock</th><th>Price</th><th></th></tr></thead>
        <tbody>
        @foreach($products as $product)
            <tr>
                <td class="d-flex align-items-center gap-2"><img class="table-thumb" src="{{ $product->image_url }}" alt=""> <span>{{ $product->name }}<br><small class="text-secondary">{{ $product->sku }}</small></span></td>
                <td>{{ $product->category->name }}</td>
                <td>{{ $product->stock_quantity }}</td>
                <td>${{ number_format((float) $product->price, 2) }}</td>
                <td class="text-end">
                    <a class="btn icon-btn ghost" href="{{ route('admin.products.edit', $product) }}" data-bs-toggle="tooltip" title="Edit"><i data-lucide="pencil"></i></a>
                    <form class="d-inline" method="post" action="{{ route('admin.products.destroy', $product) }}">@csrf @method('DELETE')<button class="btn icon-btn ghost text-danger" data-bs-toggle="tooltip" title="Delete"><i data-lucide="trash-2"></i></button></form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $products->links() }}
</div>
@endsection
