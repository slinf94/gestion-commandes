@extends('admin.layouts.app')

@section('title', 'Gestion des Catégories')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Gestion des Catégories</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nouvelle Catégorie
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Description</th>
                                    <th>Parent</th>
                                    <th>Produits</th>
                                    <th>Statut</th>
                                    <th>Ordre</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categories as $category)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($category->icon)
                                                <i class="{{ $category->icon }} me-2" style="color: {{ $category->color }}"></i>
                                            @endif
                                            <strong>{{ $category->name }}</strong>
                                            @if($category->is_featured)
                                                <span class="badge bg-warning ms-2">Vedette</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>{{ Str::limit($category->description, 50) }}</td>
                                    <td>
                                        @if($category->parent)
                                            <span class="badge bg-info">{{ $category->parent->name }}</span>
                                        @else
                                            <span class="badge bg-secondary">Racine</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $category->products_count ?? 0 }}</span>
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.categories.toggle-status', $category) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-{{ $category->is_active ? 'success' : 'secondary' }}">
                                                {{ $category->is_active ? 'Actif' : 'Inactif' }}
                                            </button>
                                        </form>
                                    </td>
                                    <td>{{ $category->sort_order }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.categories.show', $category) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                {{-- Affichage des sous-catégories --}}
                                @if($category->children->count() > 0)
                                    @foreach($category->children as $child)
                                    <tr class="table-light">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="ms-4">└─</span>
                                                @if($child->icon)
                                                    <i class="{{ $child->icon }} me-2" style="color: {{ $child->color }}"></i>
                                                @endif
                                                {{ $child->name }}
                                                @if($child->is_featured)
                                                    <span class="badge bg-warning ms-2">Vedette</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>{{ Str::limit($child->description, 50) }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ $category->name }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">{{ $child->products_count ?? 0 }}</span>
                                        </td>
                                        <td>
                                            <form action="{{ route('admin.categories.toggle-status', $child) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-{{ $child->is_active ? 'success' : 'secondary' }}">
                                                    {{ $child->is_active ? 'Actif' : 'Inactif' }}
                                                </button>
                                            </form>
                                        </td>
                                        <td>{{ $child->sort_order }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.categories.show', $child) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.categories.edit', $child) }}" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.categories.destroy', $child) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                @endif
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">
                                        <i class="fas fa-folder-open fa-3x mb-3"></i>
                                        <p>Aucune catégorie trouvée</p>
                                        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus"></i> Créer la première catégorie
                                        </a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
