<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Utilisateurs - Allo Mobile Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar { background: linear-gradient(135deg, #4CAF50, #2E7D32); min-height: 100vh; color: white; }
        .sidebar .nav-link { color: rgba(255,255,255,0.8); padding: 12px 20px; border-radius: 8px; margin: 5px 10px; transition: all 0.3s; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { background: rgba(255,255,255,0.1); color: white; }
        .main-content { background: white; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); margin: 20px; padding: 30px; }
        .table-responsive { border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .table thead { background: linear-gradient(135deg, #4CAF50, #2E7D32); color: white; }
        .btn-logout { background: #dc3545; border: none; border-radius: 8px; color: white; padding: 8px 15px; }
        .btn-logout:hover { background: #c82333; color: white; }
        .badge { font-size: 0.8em; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <div class="p-3">
                    <h4 class="text-center mb-4">
                        <i class="fas fa-shopping-cart me-2"></i>
                        Allo Mobile
                    </h4>
                    <nav class="nav flex-column">
                        <a class="nav-link" href="{{ route('admin.dashboard') }}">
                            <i class="fas fa-tachometer-alt me-2"></i>
                            Tableau de Bord
                        </a>
                        <a class="nav-link active" href="{{ route('admin.users.index') }}">
                            <i class="fas fa-users me-2"></i>
                            Utilisateurs
                        </a>
                        <a class="nav-link" href="{{ route('admin.products.index') }}">
                            <i class="fas fa-box me-2"></i>
                            Produits
                        </a>
                        <a class="nav-link" href="{{ route('admin.orders.index') }}">
                            <i class="fas fa-shopping-bag me-2"></i>
                            Commandes
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <div class="d-flex justify-content-between align-items-center p-3">
                    <h2>Gestion des Utilisateurs</h2>
                    <div>
                        <a href="{{ route('admin.users.create') }}" class="btn btn-success me-2">
                            <i class="fas fa-plus"></i> Nouvel Utilisateur
                        </a>
                        <span class="me-3">Bienvenue, {{ auth()->user()->full_name }}</span>
                        <form method="POST" action="{{ route('admin.logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-logout">
                                <i class="fas fa-sign-out-alt me-1"></i>
                                Déconnexion
                            </button>
                        </form>
                    </div>
                </div>

                <div class="main-content">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nom</th>
                                    <th>Email</th>
                                    <th>Téléphone</th>
                                    <th>Rôle</th>
                                    <th>Statut</th>
                                    <th>Inscrit le</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->full_name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->numero_telephone }}</td>
                                    <td>
                                        <span class="badge bg-{{ $user->role == 'admin' ? 'danger' : ($user->role == 'gestionnaire' ? 'warning' : 'info') }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $user->status == 'active' ? 'success' : ($user->status == 'pending' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($user->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $user->created_at ? $user->created_at->format('d/m/Y') : 'N/A' }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($user->trashed())
                                                <form method="POST" action="{{ route('admin.users.restore', $user->id) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-success">
                                                        <i class="fas fa-undo"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">Aucun utilisateur trouvé</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

