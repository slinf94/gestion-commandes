@extends('admin.layouts.app')

@section('title', 'Changer le Mot de Passe')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.profile.show') }}">Mon Profil</a></li>
    <li class="breadcrumb-item active">Mot de Passe</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-key me-2"></i>
                        Changer le Mot de Passe
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.profile.show') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.profile.password.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="current_password">Mot de passe actuel *</label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                                   id="current_password" name="current_password" required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password">Nouveau mot de passe *</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                   id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Le mot de passe doit contenir au moins 8 caractères.
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">Confirmer le nouveau mot de passe *</label>
                            <input type="password" class="form-control"
                                   id="password_confirmation" name="password_confirmation" required>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>
                                Changer le Mot de Passe
                            </button>
                            <a href="{{ route('admin.profile.show') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>
                                Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-shield-alt me-2"></i>
                        Conseils de Sécurité
                    </h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h5><i class="fas fa-lightbulb me-2"></i>Bonnes pratiques :</h5>
                        <ul class="mb-0">
                            <li>Utilisez au moins 8 caractères</li>
                            <li>Combinez lettres, chiffres et symboles</li>
                            <li>Évitez les mots de passe évidents</li>
                            <li>Ne partagez jamais votre mot de passe</li>
                        </ul>
                    </div>

                    <div class="alert alert-warning">
                        <h5><i class="fas fa-exclamation-triangle me-2"></i>Important :</h5>
                        <p class="mb-0">
                            Après le changement, vous devrez vous reconnecter avec votre nouveau mot de passe.
                        </p>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user me-2"></i>
                        Profil
                    </h3>
                </div>
                <div class="card-body">
                    <p class="text-muted">Vous pouvez également modifier vos autres informations :</p>
                    <a href="{{ route('admin.profile.edit') }}" class="btn btn-info btn-block">
                        <i class="fas fa-edit me-2"></i>
                        Modifier le Profil
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validation en temps réel
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('password_confirmation');

    function validatePassword() {
        if (password.value !== confirmPassword.value) {
            confirmPassword.setCustomValidity('Les mots de passe ne correspondent pas');
        } else {
            confirmPassword.setCustomValidity('');
        }
    }

    password.addEventListener('change', validatePassword);
    confirmPassword.addEventListener('keyup', validatePassword);
});
</script>
@endpush
@endsection















