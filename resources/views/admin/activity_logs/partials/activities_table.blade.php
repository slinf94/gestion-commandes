@forelse($activities as $activity)
<tr>
    <td>
        <div class="d-flex align-items-center">
            @if($activity->causer)
                <div class="user-avatar me-3">
                    {{ $activity->causer->initials }}
                </div>
                <div>
                    <strong>{{ $activity->causer->full_name }}</strong>
                    <br>
                    <small class="text-muted">{{ $activity->causer->email }}</small>
                </div>
            @else
                <div class="user-avatar me-3">
                    <i class="fas fa-cog"></i>
                </div>
                <div>
                    <strong>Système</strong>
                    <br>
                    <small class="text-muted">Action automatique</small>
                </div>
            @endif
        </div>
    </td>
    <td>
        <div class="d-flex align-items-center">
            <span class="activity-badge activity-{{ $activity->activity_type }}">
                @switch($activity->activity_type)
                    @case('created')
                        <i class="fas fa-plus-circle me-1"></i>Créé
                        @break
                    @case('updated')
                        <i class="fas fa-edit me-1"></i>Modifié
                        @break
                    @case('deleted')
                        <i class="fas fa-trash me-1"></i>Supprimé
                        @break
                    @case('restored')
                        <i class="fas fa-undo me-1"></i>Restauré
                        @break
                    @case('logged_in')
                        <i class="fas fa-sign-in-alt me-1"></i>Connexion
                        @break
                    @case('logged_out')
                        <i class="fas fa-sign-out-alt me-1"></i>Déconnexion
                        @break
                    @default
                        <i class="fas fa-info-circle me-1"></i>Autre
                @endswitch
            </span>
            <br>
            <small class="text-muted mt-1 d-block">{{ $activity->description }}</small>
        </div>
    </td>
    <td>
        @if($activity->subject_type)
            <div>
                <span class="badge bg-light text-dark">
                    {{ class_basename($activity->subject_type) }}
                </span>
                @if($activity->subject_id)
                    <br>
                    <small class="text-muted">ID: {{ $activity->subject_id }}</small>
                @endif
            </div>
        @else
            <span class="text-muted">-</span>
        @endif
    </td>
    <td>
        <div>
            <strong>{{ $activity->created_at->format('d/m/Y') }}</strong>
            <br>
            <small class="text-muted">{{ $activity->created_at->format('H:i:s') }}</small>
        </div>
    </td>
    <td>
        <div class="btn-group" role="group">
            <a href="{{ route('admin.activity-logs.show', $activity) }}" 
               class="btn btn-sm btn-outline-primary" 
               title="Voir les détails">
                <i class="fas fa-eye"></i>
            </a>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="5" class="text-center py-4">
        <i class="fas fa-history fa-3x text-muted mb-3"></i>
        <p class="text-muted">Aucune activité trouvée</p>
        @if(request()->hasAny(['user_id', 'subject_type', 'log_name', 'activity_type', 'date_from', 'date_to', 'search']))
            <p class="text-muted">Essayez de modifier vos filtres de recherche</p>
            <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-times me-2"></i>Effacer les filtres
            </a>
        @endif
    </td>
</tr>
@endforelse


