@extends('layouts.app')

@section('title', 'Create Waste Deposit')

@section('content')

<!-- ======= Breadcrumbs ======= -->
<div class="breadcrumbs">
    <div class="container">
        <h2>Create Waste Deposit</h2>
        <p>Register a new waste deposit at a collection point</p>
    </div>
</div><!-- End Breadcrumbs -->

<section id="create-waste" class="create-waste">
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
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-trash me-2"></i>Waste Information
                            @if($aiAvailable)
                            <span class="badge bg-success float-end">
                                <i class="bi bi-robot me-1"></i>AI Enabled
                            </span>
                            @endif
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('wastes.store') }}" method="POST" enctype="multipart/form-data" id="wasteForm">
                            @csrf
                            
                            <!-- AI Image Upload Section -->
                            @if($aiAvailable)
                            <div class="alert alert-info mb-4">
                                <i class="bi bi-lightbulb me-2"></i>
                                <strong>AI-Powered Classification:</strong> Upload an image of your waste and our AI will automatically identify the type!
                            </div>

                            <div class="mb-4">
                                <label class="form-label">
                                    <i class="bi bi-image me-1"></i>Waste Image
                                    <span class="badge bg-info ms-2">AI Classification</span>
                                </label>
                                <input 
                                    type="file" 
                                    name="image" 
                                    id="wasteImage"
                                    class="form-control @error('image') is-invalid @enderror" 
                                    accept="image/*"
                                    onchange="previewAndClassifyImage(this)"
                                >
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Supported formats: JPEG, PNG, GIF (Max: 5MB)</small>
                                
                                <!-- Image Preview -->
                                <div id="imagePreview" class="mt-3" style="display: none;">
                                    <img id="previewImg" src="" class="img-fluid rounded" style="max-height: 200px;">
                                    <button type="button" class="btn btn-sm btn-danger mt-2" onclick="clearImage()">
                                        <i class="bi bi-x"></i> Remove Image
                                    </button>
                                </div>

                                <!-- AI Classification Results -->
                                <div id="classificationResult" class="mt-3" style="display: none;">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title">
                                                <i class="bi bi-robot me-2"></i>AI Classification Result
                                            </h6>
                                            <div id="classificationContent"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Classification Loading -->
                                <div id="classificationLoading" class="mt-3" style="display: none;">
                                    <div class="text-center">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="mt-2">Classifying waste with AI...</p>
                                    </div>
                                </div>
                            </div>
                            @else
                            <div class="alert alert-warning mb-4">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <strong>AI Service Unavailable:</strong> The AI classification service is currently offline. Please enter the waste type manually.
                            </div>
                            @endif

                            <input type="hidden" name="use_ai_classification" id="useAiClassification" value="false">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Waste Type <span class="text-danger">*</span></label>
                                    @if($aiAvailable)
                                    <select 
                                        name="type" 
                                        id="wasteType"
                                        class="form-select @error('type') is-invalid @enderror" 
                                        required
                                    >
                                        <option value="">-- Select or upload image --</option>
                                        @foreach($wasteCategories as $category)
                                        <option value="{{ $category }}" {{ old('type') == $category ? 'selected' : '' }}>
                                            {{ $category }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @else
                                    <input 
                                        type="text" 
                                        name="type" 
                                        id="wasteType"
                                        class="form-control @error('type') is-invalid @enderror" 
                                        value="{{ old('type') }}" 
                                        placeholder="e.g., Plastic, Metal, Paper"
                                        required
                                    >
                                    @endif
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted" id="typeHelp">
                                        @if($aiAvailable)
                                        Upload an image for automatic classification
                                        @else
                                        Enter the waste type manually
                                        @endif
                                    </small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Quantity (kg) <span class="text-danger">*</span></label>
                                    <input 
                                        type="number" 
                                        name="quantite" 
                                        min="10" 
                                        step="0.01"
                                        class="form-control @error('quantite') is-invalid @enderror" 
                                        value="{{ old('quantite') }}" 
                                        placeholder="Minimum 10 kg"
                                        required
                                    >
                                    @error('quantite')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Minimum quantity: 10 kg</small>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Deposit Date <span class="text-danger">*</span></label>
                                    <input 
                                        type="date" 
                                        name="dateDepot" 
                                        min="{{ \Carbon\Carbon::tomorrow()->format('Y-m-d') }}"
                                        class="form-control @error('dateDepot') is-invalid @enderror" 
                                        value="{{ old('dateDepot') }}" 
                                        required
                                    >
                                    @error('dateDepot')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">Deposit must be scheduled for tomorrow or later</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Collection Point <span class="text-danger">*</span></label>
                                    <select 
                                        name="collection_point_id" 
                                        class="form-select @error('collection_point_id') is-invalid @enderror" 
                                        required
                                    >
                                        <option value="">-- Select a collection point --</option>
                                        @foreach($collectionPoints as $point)
                                            <option 
                                                value="{{ $point->id }}" 
                                                {{ old('collection_point_id') == $point->id ? 'selected' : '' }}
                                            >
                                                #{{ $point->id }} - {{ $point->name }} ({{ $point->address }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('collection_point_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Location Details <span class="text-danger">*</span></label>
                                <textarea 
                                    name="localisation" 
                                    class="form-control @error('localisation') is-invalid @enderror" 
                                    rows="3" 
                                    placeholder="Enter specific location details within the collection point"
                                    required
                                >{{ old('localisation') }}</textarea>
                                @error('localisation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="{{ route('wastes.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-x-circle me-1"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-1"></i>Create Waste Deposit
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>

    </div>
</section><!-- End Create Waste Section -->

@if($aiAvailable)
<script>
let classificationData = null;

function previewAndClassifyImage(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Preview image
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('imagePreview').style.display = 'block';
        }
        reader.readAsDataURL(file);
        
        // Classify image
        classifyImage(file);
    }
}

async function classifyImage(file) {
    const formData = new FormData();
    formData.append('image', file);
    formData.append('_token', '{{ csrf_token() }}');
    
    // Show loading
    document.getElementById('classificationLoading').style.display = 'block';
    document.getElementById('classificationResult').style.display = 'none';
    
    try {
        const response = await fetch('{{ route('wastes.classify') }}', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            classificationData = data;
            displayClassification(data);
            
            // Auto-fill waste type
            document.getElementById('wasteType').value = data.waste_type;
            document.getElementById('useAiClassification').value = 'true';
            
            // Update help text
            document.getElementById('typeHelp').innerHTML = 
                `<span class="text-success"><i class="bi bi-check-circle me-1"></i>AI detected: ${data.waste_type} (${(data.confidence * 100).toFixed(1)}% confidence)</span>`;
        } else {
            showClassificationError(data.error || 'Classification failed');
        }
    } catch (error) {
        console.error('Classification error:', error);
        showClassificationError('Network error. Please try again.');
    } finally {
        document.getElementById('classificationLoading').style.display = 'none';
    }
}

function displayClassification(data) {
    let html = `
        <div class="mb-2">
            <strong>Detected Type:</strong> 
            <span class="badge bg-success fs-6">${data.waste_type}</span>
        </div>
        <div class="mb-2">
            <strong>Confidence:</strong> 
            <div class="progress" style="height: 25px;">
                <div class="progress-bar ${getConfidenceColor(data.confidence)}" 
                     role="progressbar" 
                     style="width: ${data.confidence * 100}%">
                    ${(data.confidence * 100).toFixed(1)}%
                </div>
            </div>
        </div>
    `;
    
    if (data.all_predictions && data.all_predictions.length > 1) {
        html += `
            <div class="mt-3">
                <strong>All Predictions:</strong>
                <ul class="list-unstyled mt-2">
        `;
        
        data.all_predictions.slice(0, 3).forEach(pred => {
            html += `
                <li class="mb-1">
                    <span class="badge bg-secondary">${pred.waste_type}</span>
                    <small class="text-muted">${(pred.confidence * 100).toFixed(1)}%</small>
                </li>
            `;
        });
        
        html += `
                </ul>
            </div>
        `;
    }
    
    html += `
        <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="changeWasteType()">
            <i class="bi bi-pencil"></i> Change Type Manually
        </button>
    `;
    
    document.getElementById('classificationContent').innerHTML = html;
    document.getElementById('classificationResult').style.display = 'block';
}

function getConfidenceColor(confidence) {
    if (confidence >= 0.8) return 'bg-success';
    if (confidence >= 0.5) return 'bg-warning';
    return 'bg-danger';
}

function showClassificationError(message) {
    document.getElementById('classificationContent').innerHTML = `
        <div class="alert alert-danger mb-0">
            <i class="bi bi-exclamation-triangle me-2"></i>${message}
        </div>
    `;
    document.getElementById('classificationResult').style.display = 'block';
}

function clearImage() {
    document.getElementById('wasteImage').value = '';
    document.getElementById('imagePreview').style.display = 'none';
    document.getElementById('classificationResult').style.display = 'none';
    document.getElementById('wasteType').value = '';
    document.getElementById('useAiClassification').value = 'false';
    document.getElementById('typeHelp').innerHTML = 'Upload an image for automatic classification';
    classificationData = null;
}

function changeWasteType() {
    document.getElementById('useAiClassification').value = 'false';
    document.getElementById('typeHelp').innerHTML = 
        '<span class="text-warning"><i class="bi bi-info-circle me-1"></i>Manual override - AI suggestion ignored</span>';
}

// Listen for manual changes to waste type
document.getElementById('wasteType').addEventListener('change', function() {
    if (classificationData && this.value !== classificationData.waste_type) {
        changeWasteType();
    }
});
</script>
@endif

@endsection
