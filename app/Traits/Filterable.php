<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait Filterable
{
    public function scopeFilter(Builder $query, Request $request): Builder
    {
        // Recherche générale
        if ($request->has('search')) {
            $searchColumns = $this->searchable ?? ['nom', 'description'];
            $search = $request->get('search');
            
            $query->where(function($q) use ($searchColumns, $search) {
                foreach ($searchColumns as $column) {
                    $q->orWhere($column, 'like', "%{$search}%");
                }
            });
        }

        // Filtres de prix
        if ($request->has('prix_min')) {
            $query->where('prix', '>=', $request->prix_min);
        }
        if ($request->has('prix_max')) {
            $query->where('prix', '<=', $request->prix_max);
        }

        // Filtre par état
        if ($request->has('etat') && $request->etat !== '') {
            $query->where('etat', $request->etat);
        }

        // Filtre disponibilité
        if ($request->has('disponible')) {
            $query->where('quantite', '>', 0);
        }

        // Tri
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        
        if (in_array($sortBy, $this->sortable ?? ['created_at', 'prix', 'quantite'])) {
            $query->orderBy($sortBy, $sortOrder);
        }

        return $query;
    }
}