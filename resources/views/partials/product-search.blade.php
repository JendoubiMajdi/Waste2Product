<!-- Search and Filters -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form action="{{ route('products.index') }}" method="GET">
            <div class="row g-3">
                <!-- Search Bar -->
                <div class="col-md-4">
                    <label class="form-label">Recherche</label>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Rechercher par nom ou description..."
                           value="{{ request('search') }}">
                </div>

                <!-- Price Range -->
                <div class="col-md-4">
                    <label class="form-label">Prix (TND)</label>
                    <div class="input-group">
                        <input type="number" name="min_price" class="form-control" 
                               placeholder="Min"
                               value="{{ request('min_price') }}">
                        <span class="input-group-text">à</span>
                        <input type="number" name="max_price" class="form-control" 
                               placeholder="Max"
                               value="{{ request('max_price') }}">
                    </div>
                </div>

                <!-- Status -->
                <div class="col-md-2">
                    <label class="form-label">État</label>
                    <select name="status" class="form-select">
                        <option value="">Tous</option>
                        <option value="recyclé" {{ request('status') == 'recyclé' ? 'selected' : '' }}>Recyclé</option>
                        <option value="non recyclé" {{ request('status') == 'non recyclé' ? 'selected' : '' }}>Non recyclé</option>
                    </select>
                </div>

                <!-- Stock Filter -->
                <div class="col-md-2">
                    <label class="form-label">Stock</label>
                    <select name="stock" class="form-select">
                        <option value="">Tous</option>
                        <option value="in_stock" {{ request('stock') == 'in_stock' ? 'selected' : '' }}>En stock</option>
                        <option value="low_stock" {{ request('stock') == 'low_stock' ? 'selected' : '' }}>Stock bas</option>
                        <option value="out_of_stock" {{ request('stock') == 'out_of_stock' ? 'selected' : '' }}>Rupture</option>
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Rechercher
                    </button>
                    <a href="{{ route('products.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Réinitialiser
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>