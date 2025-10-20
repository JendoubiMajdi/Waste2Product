<!-- Search and Filters -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('wastes.index') }}" method="GET">
            <div class="row g-3">
                <!-- Type Filter -->
                <div class="col-md-3">
                    <label class="form-label">Type de déchet</label>
                    <select name="type" class="form-select">
                        <option value="">Tous les types</option>
                        <option value="Plastic" {{ request('type') == 'Plastic' ? 'selected' : '' }}>Plastic</option>
                        <option value="Metal" {{ request('type') == 'Metal' ? 'selected' : '' }}>Metal</option>
                        <option value="Glass" {{ request('type') == 'Glass' ? 'selected' : '' }}>Glass</option>
                        <option value="Paper" {{ request('type') == 'Paper' ? 'selected' : '' }}>Paper</option>
                    </select>
                </div>

                <!-- Location Search -->
                <div class="col-md-3">
                    <label class="form-label">Localisation</label>
                    <input type="text" name="location" class="form-control" 
                           placeholder="Rechercher par lieu..."
                           value="{{ request('location') }}">
                </div>

                <!-- Date Range -->
                <div class="col-md-4">
                    <label class="form-label">Période</label>
                    <div class="input-group">
                        <input type="date" name="start_date" class="form-control" 
                               value="{{ request('start_date') }}">
                        <span class="input-group-text">à</span>
                        <input type="date" name="end_date" class="form-control" 
                               value="{{ request('end_date') }}">
                    </div>
                </div>

                <!-- Quantity Range -->
                <div class="col-md-2">
                    <label class="form-label">Quantité (kg)</label>
                    <input type="number" name="min_quantity" class="form-control" 
                           placeholder="Min kg"
                           value="{{ request('min_quantity') }}">
                </div>

                <!-- Action Buttons -->
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Rechercher
                    </button>
                    <a href="{{ route('wastes.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Réinitialiser
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>