@extends('layouts.app')

@section('title', 'Edit Collection Point')

@section('content')

<!-- ======= Breadcrumbs ======= -->
<div class="breadcrumbs">
    <div class="container">
        <h2>Edit Collection Point</h2>
        <p>Update collection point information</p>
    </div>
</div><!-- End Breadcrumbs -->

<section id="edit-collection-point" class="edit-collection-point">
    <div class="container" data-aos="fade-up">

        <div class="row justify-content-center">
            <div class="col-lg-8">
                
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <h6 class="alert-heading"><i class="bi bi-exclamation-triangle me-2"></i>Please fix the following errors:</h6>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="bi bi-pencil me-2"></i>Edit Collection Point #{{ $collectionPoint->id }}</h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('collection_points.update', $collectionPoint) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                <input 
                                    type="text" 
                                    name="name" 
                                    id="name" 
                                    class="form-control @error('name') is-invalid @enderror" 
                                    value="{{ old('name', $collectionPoint->name) }}" 
                                    placeholder="e.g., Green Recycling Center"
                                    required
                                >
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                                <textarea 
                                    name="address" 
                                    id="address" 
                                    class="form-control @error('address') is-invalid @enderror" 
                                    rows="2" 
                                    placeholder="Enter full address"
                                    required
                                >{{ old('address', $collectionPoint->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="latitude" class="form-label">Latitude <span class="text-danger">*</span></label>
                                    <input 
                                        type="number" 
                                        step="0.00000001" 
                                        name="latitude" 
                                        id="latitude" 
                                        class="form-control @error('latitude') is-invalid @enderror" 
                                        value="{{ old('latitude', $collectionPoint->latitude) }}" 
                                        placeholder="e.g., 36.7538"
                                        required
                                    >
                                    @error('latitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="longitude" class="form-label">Longitude <span class="text-danger">*</span></label>
                                    <input 
                                        type="number" 
                                        step="0.00000001" 
                                        name="longitude" 
                                        id="longitude" 
                                        class="form-control @error('longitude') is-invalid @enderror" 
                                        value="{{ old('longitude', $collectionPoint->longitude) }}" 
                                        placeholder="e.g., 3.0588"
                                        required
                                    >
                                    @error('longitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label for="opening_time" class="form-label">Opening Time</label>
                                    <input 
                                        type="time" 
                                        name="opening_time" 
                                        id="opening_time" 
                                        class="form-control @error('opening_time') is-invalid @enderror" 
                                        value="{{ old('opening_time', $collectionPoint->working_hours ? explode('-', $collectionPoint->working_hours)[0] ?? '08:00' : '08:00') }}"
                                    >
                                    @error('opening_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="closing_time" class="form-label">Closing Time</label>
                                    <input 
                                        type="time" 
                                        name="closing_time" 
                                        id="closing_time" 
                                        class="form-control @error('closing_time') is-invalid @enderror" 
                                        value="{{ old('closing_time', $collectionPoint->working_hours ? explode('-', $collectionPoint->working_hours)[1] ?? '17:00' : '17:00') }}"
                                    >
                                    @error('closing_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="contact_phone" class="form-label">Contact Phone</label>
                                    <input 
                                        type="text" 
                                        name="contact_phone" 
                                        id="contact_phone" 
                                        class="form-control @error('contact_phone') is-invalid @enderror" 
                                        value="{{ old('contact_phone', $collectionPoint->contact_phone) }}" 
                                        placeholder="+213 XXX XXX XXX"
                                    >
                                    @error('contact_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select 
                                    name="status" 
                                    id="status" 
                                    class="form-select @error('status') is-invalid @enderror" 
                                    required
                                >
                                    <option value="active" {{ old('status', $collectionPoint->status) === 'active' ? 'selected' : '' }}>✅ Active (Visible to users)</option>
                                    <option value="inactive" {{ old('status', $collectionPoint->status) === 'inactive' ? 'selected' : '' }}>❌ Inactive (Hidden from users)</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            @if($collectionPoint->image)
                                <div class="mb-3">
                                    <label class="form-label">Current Image</label>
                                    @php
                                        $isBase64 = Str::startsWith($collectionPoint->image, '/9j/') || Str::startsWith($collectionPoint->image, 'iVBOR');
                                    @endphp
                                    @if($isBase64)
                                        <div>
                                            <img src="data:image/jpeg;base64,{{ $collectionPoint->image }}" alt="Current image" style="max-width: 200px; height: auto;" class="img-thumbnail">
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <div class="mb-3">
                                <label for="image" class="form-label">Update Image</label>
                                <input 
                                    type="file" 
                                    name="image" 
                                    id="image" 
                                    class="form-control @error('image') is-invalid @enderror" 
                                    accept="image/*"
                                >
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Upload a new photo to replace the current one (optional)</small>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="{{ route('collection_points.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-x-circle me-1"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-warning">
                                    <i class="bi bi-save me-1"></i>Update Collection Point
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>

    </div>
</section><!-- End Edit Collection Point Section -->

@endsection
