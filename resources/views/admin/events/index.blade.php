@extends('admin.layouts.app')

@section('title', 'Events Management')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2><span class="iconify" data-icon="mdi:calendar-event"></span> Events Management</h2>
            <p class="text-muted">Manage all events and registrations</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('events.create') }}" class="btn btn-primary">
                <span class="iconify" data-icon="mdi:plus-circle"></span> Create Event
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Success!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Title</th>
                            <th style="width: 120px;">Date</th>
                            <th>Location</th>
                            <th style="width: 120px;" class="text-center">Registrations</th>
                            <th style="width: 100px;">Status</th>
                            <th style="width: 150px;" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($events as $event)
                        <tr>
                            <td>
                                <div class="fw-medium">{{ $event->title }}</div>
                                <small class="text-muted">{{ Str::limit($event->description ?? '', 60) }}</small>
                            </td>
                            <td>
                                <div class="fw-medium">{{ \Carbon\Carbon::parse($event->date)->format('M d, Y') }}</div>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($event->date)->format('g:i A') }}</small>
                            </td>
                            <td>
                                <span class="iconify" data-icon="mdi:map-marker"></span>
                                {{ $event->location }}
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info">
                                    {{ $event->registrations_count ?? 0 }} registered
                                </span>
                            </td>
                            <td>
                                @if(\Carbon\Carbon::parse($event->date)->isFuture())
                                    <span class="badge bg-success">
                                        <span class="iconify" data-icon="mdi:calendar-check"></span> Upcoming
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        <span class="iconify" data-icon="mdi:calendar-remove"></span> Past
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('events.show', $event->id) }}" class="btn btn-sm btn-info" title="View">
                                        <span class="iconify" data-icon="mdi:eye"></span>
                                    </a>
                                    <a href="{{ route('events.edit', $event->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <span class="iconify" data-icon="mdi:pencil"></span>
                                    </a>
                                    <form action="{{ route('events.destroy', $event->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this event?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                            <span class="iconify" data-icon="mdi:trash-can"></span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <span class="iconify" data-icon="mdi:calendar-blank" style="font-size: 64px; color: #ddd;"></span>
                                <p class="text-muted mt-3 mb-0">No events found</p>
                                <a href="{{ route('events.create') }}" class="btn btn-primary mt-3">
                                    <span class="iconify" data-icon="mdi:plus-circle"></span> Create First Event
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($events->hasPages())
            <div class="mt-4 d-flex justify-content-center">
                {{ $events->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
