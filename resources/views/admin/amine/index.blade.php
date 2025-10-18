@extends('back.layout')

@section('title', 'Dons List')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h4>Dons List</h4>
                </div>
                <div class="card-body">

                    {{-- Filter Form --}}
                    <form method="GET" class="row g-3 mb-4">
                        <div class="col-md-4">
                            <select name="type" class="form-select">
                                <option value="">All</option>
                                <option value="money" {{ $type == 'money' ? 'selected' : '' }}>Money</option>
                                <option value="product" {{ $type == 'product' ? 'selected' : '' }}>Product</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </div>
                    </form>

                    {{-- Table --}}
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>User</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($dons as $don)
                                    <tr>
                                        <td>{{ $don->user->name }}</td>
                                        <td>{{ ucfirst($don->type) }}</td>
                                        <td>{{ $don->amount }}</td>
                                        <td>{{ $don->description }}</td>
                                        <td>
                                            <span class="badge bg-{{ $don->status == 'approved' ? 'success' : 'secondary' }}">
                                                {{ ucfirst($don->status) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <form action="{{ route('admin.dons.destroy', $don) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No donations found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="d-flex justify-content-center mt-4">
                        {{ $dons->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
