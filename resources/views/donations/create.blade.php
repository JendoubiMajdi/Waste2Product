@extends('layouts.app')

@section('title', 'Make a Donation')

@section('content')
<!-- Page Title -->
<div class="breadcrumbs">
    <div class="container">
        <h2>Make a Donation</h2>
        <p>Support our waste transformation mission</p>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-body p-4">
                        <h4 class="card-title mb-4">Donation Information</h4>

                        @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <form action="{{ route('donations.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="mb-3">
                                <label for="type" class="form-label">Donation Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                    <option value="">Select type...</option>
                                    <option value="money" {{ old('type') == 'money' ? 'selected' : '' }}>Money</option>
                                    <option value="food" {{ old('type') == 'food' ? 'selected' : '' }}>Food</option>
                                    <option value="clothes" {{ old('type') == 'clothes' ? 'selected' : '' }}>Clothes</option>
                                    <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3" id="amountField" style="display: none;">
                                <label for="amount" class="form-label">Amount (TND)</label>
                                <input type="number" class="form-control @error('amount') is-invalid @enderror" 
                                       id="amount" name="amount" value="{{ old('amount') }}" 
                                       step="0.01" min="0" placeholder="0.00">
                                @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="4" required 
                                          placeholder="Describe your donation...">{{ old('description') }}</textarea>
                                @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Maximum 1000 characters</small>
                            </div>

                            <div class="mb-3">
                                <label for="image" class="form-label">Image (Optional)</label>
                                <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                       id="image" name="image" accept="image/*">
                                @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Max size: 2MB. Formats: JPEG, PNG, JPG, GIF</small>
                            </div>

                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                Your donation will be reviewed by our team before being published.
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-circle"></i> Submit Donation
                                </button>
                                <a href="{{ route('donations.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type');
    const amountField = document.getElementById('amountField');
    const amountInput = document.getElementById('amount');

    typeSelect.addEventListener('change', function() {
        if (this.value === 'money') {
            amountField.style.display = 'block';
            amountInput.required = true;
        } else {
            amountField.style.display = 'none';
            amountInput.required = false;
            amountInput.value = '';
        }
    });

    // Trigger on page load if money is pre-selected
    if (typeSelect.value === 'money') {
        amountField.style.display = 'block';
        amountInput.required = true;
    }
});
</script>
@endsection
