<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, SoftDeletes;

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
        return $this->role === 'admin';
    }

    public function isGestionnaire()
    {
        return $this->role === 'gestionnaire';
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
