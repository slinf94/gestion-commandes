<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Traits\LogsActivity;
use App\Models\Role;
use App\Models\Permission;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, SoftDeletes, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'email_verified_at',
        'password',
        'numero_telephone',
        'numero_whatsapp',
        'localisation',
        'quartier',
        'commercial_id',
        'role',
        'status',
        'date_naissance',
        'avatar',
        'photo', // Photo de profil
        'two_factor_enabled',
        'two_factor_secret',
        'remember_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
        'email_verified_at' => 'datetime',
        'date_naissance' => 'date',
        'password' => 'hashed',
        'two_factor_enabled' => 'boolean',
    ];
    }

    /**
     * Attributes to log when the model changes
     */
    protected $logAttributes = [
        'nom',
        'prenom',
        'email',
        'numero_telephone',
        'numero_whatsapp',
        'localisation',
        'quartier',
        'role',
        'status'
    ];

    /**
     * Attributes to ignore when logging
     */
    protected $logIgnoredAttributes = [
        'password',
        'remember_token',
        'two_factor_secret'
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    // Relations
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Relation avec les rôles
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role');
    }

    /**
     * Relation avec le commercial
     */
    public function commercial()
    {
        return $this->belongsTo(User::class, 'commercial_id');
    }

    /**
     * Relation pour les clients d'un commercial
     */
    public function clients()
    {
        return $this->hasMany(User::class, 'commercial_id');
    }

    /**
     * Vérifier si l'utilisateur a un rôle
     */
    public function hasRole($role)
    {
        if (is_string($role)) {
            return $this->roles()->where('slug', $role)->exists();
        }

        return $this->roles()->where('id', $role)->exists();
    }

    /**
     * Vérifier si l'utilisateur a une permission
     */
    public function hasPermission($permission)
    {
        foreach ($this->roles as $role) {
            if ($role->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Vérifier si l'utilisateur a au moins l'un des rôles
     */
    public function hasAnyRole($roles)
    {
        foreach ($roles as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Vérifier si l'utilisateur a toutes les permissions
     */
    public function hasAllPermissions($permissions)
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Attacher un rôle à l'utilisateur
     */
    public function attachRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('slug', $role)->first();
        }

        if ($role) {
            $this->roles()->syncWithoutDetaching([$role->id]);
        }
    }

    /**
     * Détacher un rôle de l'utilisateur
     */
    public function detachRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('slug', $role)->first();
        }

        if ($role) {
            $this->roles()->detach($role->id);
        }
    }



    public function productNotes()
    {
        return $this->hasMany(ProductNote::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    // Accessors
    public function getFullNameAttribute()
    {
        return trim($this->prenom . ' ' . $this->nom);
    }

    public function getInitialsAttribute()
    {
        $firstInitial = $this->prenom ? strtoupper(substr($this->prenom, 0, 1)) : '';
        $lastInitial = $this->nom ? strtoupper(substr($this->nom, 0, 1)) : '';
        return $firstInitial . $lastInitial;
    }

    public function getFullAddressAttribute()
    {
        $parts = array_filter([
            $this->localisation,
            $this->quartier,
            $this->ville
        ]);
        return implode(', ', $parts);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeAdmins($query)
    {
        return $query->whereIn('role', ['admin', 'gestionnaire']);
    }

    public function scopeClients($query)
    {
        return $query->where('role', 'client');
    }

    // Helper methods
    public function isAdmin()
    {
        // Vérifier via la colonne role directement (ancien système)
        if ($this->role === 'super-admin' || $this->role === 'admin') {
            return true;
        }
        
        // Vérifier via les rôles attachés (nouveau système)
        if ($this->hasRole('super-admin') || $this->hasRole('admin')) {
            return true;
        }
        // Fallback sur l'ancien système (champ role)
        return $this->role === 'admin';
    }

    public function isGestionnaire()
    {
        // Vérifier via la colonne role directement (ancien système)
        if ($this->role === 'gestionnaire') {
            return true;
        }
        
        // Vérifier via les rôles attachés (nouveau système)
        if ($this->hasRole('gestionnaire')) {
            return true;
        }
        // Fallback sur l'ancien système (champ role)
        return $this->role === 'gestionnaire';
    }

    /**
     * Vérifier si l'utilisateur est Super Admin
     */
    public function isSuperAdmin()
    {
        return $this->hasRole('super-admin');
    }

    /**
     * Vérifier si l'utilisateur est Vendeur
     */
    public function isVendeur()
    {
        return $this->hasRole('vendeur');
    }

    public function isClient()
    {
        return $this->role === 'client';
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isSuspended()
    {
        return $this->status === 'suspended';
    }

    public function isInactive()
    {
        return $this->status === 'inactive';
    }
}
