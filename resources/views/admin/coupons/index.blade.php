@extends('layouts.admin')

@section('title', 'Coupons | Admin')

@section('admin')
<div class="section-heading"><h1>Coupons</h1></div>
<div class="row g-4">
    <div class="col-lg-4">
        <form class="tool-panel stacked-form" method="post" action="{{ route('admin.coupons.store') }}">
            @csrf
            <h2>Add coupon</h2>
            <input class="form-control" name="code" placeholder="SAVE20" required>
            <select class="form-select" name="type"><option value="fixed">Fixed</option><option value="percentage">Percentage</option></select>
            <input class="form-control" type="number" step="0.01" name="value" placeholder="Value" required>
            <input class="form-control" type="number" step="0.01" name="min_order_amount" placeholder="Minimum order">
            <input class="form-control" type="number" name="usage_limit" placeholder="Usage limit">
            <label class="form-check"><input class="form-check-input" type="checkbox" name="is_active" value="1" checked><span class="form-check-label">Active</span></label>
            <button class="btn btn-brand" type="submit">Save</button>
        </form>
    </div>
    <div class="col-lg-8">
        <div class="tool-panel table-responsive">
            <table class="table align-middle">
                <thead><tr><th>Code</th><th>Type</th><th>Value</th><th>Used</th><th></th></tr></thead>
                <tbody>@foreach($coupons as $coupon)<tr><td>{{ $coupon->code }}</td><td>{{ $coupon->type }}</td><td>{{ $coupon->value }}</td><td>{{ $coupon->used_count }}</td><td class="text-end"><form method="post" action="{{ route('admin.coupons.destroy', $coupon) }}">@csrf @method('DELETE')<button class="btn icon-btn ghost text-danger"><i data-lucide="trash-2"></i></button></form></td></tr>@endforeach</tbody>
            </table>
        </div>
    </div>
</div>
@endsection
