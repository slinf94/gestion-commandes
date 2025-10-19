@extends('admin.layouts.app')

@section('title', 'Gestion des Attributs')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Gestion des Attributs</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.attributes.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nouvel Attribut
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
                                    <th>Type</th>
                                    <th>Options</th>
                                    <th>Propriétés</th>
                                    <th>Statut</th>
                                    <th>Ordre</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attributes as $attribute)
                                <tr>
                                    <td>
                                        <strong>{{ $attribute->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $attribute->slug }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $attribute->type == 'text' ? 'primary' : ($attribute->type == 'select' ? 'success' : 'info') }}">
                                            {{ ucfirst($attribute->type) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($attribute->type == 'select' || $attribute->type == 'multiselect')
                                            @if($attribute->options && count($attribute->options) > 0)
                                                <small class="text-muted">{{ count($attribute->options) }} option(s)</small>
                                                <br>
                                                @foreach(array_slice($attribute->options, 0, 3) as $option)
                                                    <span class="badge bg-light text-dark me-1">{{ $option }}</span>
                                                @endforeach
                                                @if(count($attribute->options) > 3)
                                                    <span class="badge bg-secondary">+{{ count($attribute->options) - 3 }}</span>
                                                @endif
                                            @else
                                                <span class="text-muted">Aucune option</span>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            @if($attribute->is_required)
                                                <span class="badge bg-danger">Requis</span>
                                            @endif
                                            @if($attribute->is_filterable)
                                                <span class="badge bg-success">Filtrable</span>
                                            @endif
                                            @if($attribute->is_variant)
                                                <span class="badge bg-warning">Variante</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.attributes.toggle-status', $attribute) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-{{ $attribute->is_active ? 'success' : 'secondary' }}">
                                                {{ $attribute->is_active ? 'Actif' : 'Inactif' }}
                                            </button>
                                        </form>
                                    </td>
                                    <td>{{ $attribute->sort_order }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.attributes.show', $attribute) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.attributes.edit', $attribute) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.attributes.destroy', $attribute) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet attribut ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">
                                        <i class="fas fa-tags fa-3x mb-3"></i>
                                        <p>Aucun attribut trouvé</p>
                                        <a href="{{ route('admin.attributes.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus"></i> Créer le premier attribut
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

<style>
.gap-1 > * + * {
    margin-left: 0.25rem;
}
</style>
@endsection
