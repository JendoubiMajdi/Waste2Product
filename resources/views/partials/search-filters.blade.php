{{-- resources/views/partials/search-filters.blade.php --}}
<div class="bg-white p-4 rounded-lg shadow mb-6">
    <form action="{{ $route }}" method="GET" class="space-y-4">
        {{-- Barre de recherche --}}
        <div>
            <label for="search" class="block text-sm font-medium text-gray-700">Rechercher</label>
            <div class="mt-1 relative rounded-md shadow-sm">
                <input type="text" name="search" id="search" 
                    value="{{ request('search') }}"
                    class="block w-full rounded-md border-gray-300 focus:border-green-500 focus:ring-green-500"
                    placeholder="Rechercher par nom ou description...">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Filtre par prix --}}
            <div>
                <label for="prix_min" class="block text-sm font-medium text-gray-700">Prix minimum</label>
                <input type="number" name="prix_min" id="prix_min" 
                    value="{{ request('prix_min') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 focus:border-green-500 focus:ring-green-500"
                    min="0" step="0.01">
            </div>

            <div>
                <label for="prix_max" class="block text-sm font-medium text-gray-700">Prix maximum</label>
                <input type="number" name="prix_max" id="prix_max" 
                    value="{{ request('prix_max') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 focus:border-green-500 focus:ring-green-500"
                    min="0" step="0.01">
            </div>

            {{-- Filtre par état --}}
            <div>
                <label for="etat" class="block text-sm font-medium text-gray-700">État</label>
                <select name="etat" id="etat" 
                    class="mt-1 block w-full rounded-md border-gray-300 focus:border-green-500 focus:ring-green-500">
                    <option value="">Tous les états</option>
                    @foreach($etats as $etatOption)
                        <option value="{{ $etatOption }}" {{ request('etat') == $etatOption ? 'selected' : '' }}>
                            {{ ucfirst($etatOption) }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Tri --}}
            <div>
                <label for="sort" class="block text-sm font-medium text-gray-700">Trier par</label>
                <select name="sort" id="sort" 
                    class="mt-1 block w-full rounded-md border-gray-300 focus:border-green-500 focus:ring-green-500">
                    <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Date</option>
                    <option value="prix" {{ request('sort') == 'prix' ? 'selected' : '' }}>Prix</option>
                    <option value="quantite" {{ request('sort') == 'quantite' ? 'selected' : '' }}>Quantité</option>
                </select>
            </div>
        </div>

        {{-- Options supplémentaires --}}
        <div class="flex items-center space-x-4">
            <label class="inline-flex items-center">
                <input type="checkbox" name="disponible" value="1" 
                    {{ request('disponible') ? 'checked' : '' }}
                    class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                <span class="ml-2 text-sm text-gray-700">Disponible uniquement</span>
            </label>

            <label class="inline-flex items-center">
                <input type="radio" name="order" value="asc" 
                    {{ request('order') == 'asc' ? 'checked' : '' }}
                    class="border-gray-300 text-green-600 focus:ring-green-500">
                <span class="ml-2 text-sm text-gray-700">Croissant</span>
            </label>

            <label class="inline-flex items-center">
                <input type="radio" name="order" value="desc" 
                    {{ request('order', 'desc') == 'desc' ? 'checked' : '' }}
                    class="border-gray-300 text-green-600 focus:ring-green-500">
                <span class="ml-2 text-sm text-gray-700">Décroissant</span>
            </label>
        </div>

        <div class="flex justify-between">
            <button type="submit" 
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                Rechercher
            </button>

            <a href="{{ $route }}" 
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                Réinitialiser les filtres
            </a>
        </div>
    </form>
</div>