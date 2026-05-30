@extends('layouts.admin')

@section('title', 'Customers | Admin')

@section('admin')
<div class="section-heading"><h1>Customers</h1></div>
<div class="tool-panel table-responsive">
    <table class="table align-middle">
        <thead><tr><th>Name</th><th>Email</th><th>Orders</th><th>Joined</th><th></th></tr></thead>
        <tbody>
        @foreach($customers as $customer)
            <tr><td>{{ $customer->name }}</td><td>{{ $customer->email }}</td><td>{{ $customer->orders_count }}</td><td>{{ $customer->created_at->format('M d, Y') }}</td><td class="text-end"><a class="btn btn-outline-dark btn-sm" href="{{ route('admin.customers.show', $customer) }}">Open</a></td></tr>
        @endforeach
        </tbody>
    </table>
    {{ $customers->links() }}
</div>
@endsection
