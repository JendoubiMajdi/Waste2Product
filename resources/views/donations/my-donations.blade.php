@extends('layouts.app')

@section('title', 'My Donations')

@section('content')
<!-- Page Title -->
<div class="breadcrumbs">
    <div class="container">
        <h2>My Donations</h2>
        <p>View and manage your donation history</p>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="row mb-4">
            <div class="col-md-12">
                <a href="{{ route('donations.create') }}" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Make New Donation
                </a>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($donations as $donation)
                            <tr>
                                <td><span class="badge bg-primary">{{ ucfirst($donation->type) }}</span></td>
                                <td>{{ Str::limit($donation->description, 50) }}</td>
                                <td>
                                    @if($donation->amount)
                                    ${{ number_format($donation->amount, 2) }}
                                    @else
                                    -
                                    @endif
                                </td>
                                <td>
                                    @if($donation->status == 'approved')
                                    <span class="badge bg-success">Approved</span>
                                    @elseif($donation->status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                    @else
                                    <span class="badge bg-danger">Rejected</span>
                                    @endif
                                </td>
                                <td>{{ $donation->created_at->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ route('donations.show', $donation) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">
                                    <p class="text-muted mb-0">You haven't made any donations yet.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-4">
            {{ $donations->links() }}
        </div>
    </div>
</section>
@endsection
