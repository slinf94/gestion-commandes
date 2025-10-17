<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ $statistics['total_activities'] ?? 0 }}</h4>
                        <p class="mb-0">Total Activités</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-history fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ $statistics['activities_today'] ?? 0 }}</h4>
                        <p class="mb-0">Aujourd'hui</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-calendar-day fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ $statistics['unique_users'] ?? 0 }}</h4>
                        <p class="mb-0">Utilisateurs Actifs</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ $statistics['activities_this_week'] ?? 0 }}</h4>
                        <p class="mb-0">Cette Semaine</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-calendar-week fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(isset($statistics['most_active_user']) && $statistics['most_active_user'])
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-trophy me-2"></i>
                    Utilisateur le Plus Actif
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar bg-primary text-white me-3" style="width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        {{ $statistics['most_active_user']->causer ? $statistics['most_active_user']->causer->initials : '?' }}
                    </div>
                    <div>
                        <h6 class="mb-0">
                            {{ $statistics['most_active_user']->causer ? $statistics['most_active_user']->causer->full_name : 'Utilisateur inconnu' }}
                        </h6>
                        <small class="text-muted">
                            {{ $statistics['most_active_user']->activity_count }} activités
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

