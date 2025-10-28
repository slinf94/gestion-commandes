<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relation avec les utilisateurs
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_role');
    }

    /**
     * Relation avec les permissions
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }

    /**
     * Vérifier si le rôle a une permission
     */
    public function hasPermission($permission)
    {
        if (is_string($permission)) {
            return $this->permissions()->where('slug', $permission)->exists();
        }

        return $this->permissions()->where('id', $permission)->exists();
    }

    /**
     * Attacher une permission au rôle
     */
    public function attachPermission($permission)
    {
        if (is_string($permission)) {
            $permission = Permission::where('slug', $permission)->first();
        }

        if ($permission) {
            $this->permissions()->syncWithoutDetaching([$permission->id]);
        }
    }

    /**
     * Détacher une permission du rôle
     */
    public function detachPermission($permission)
    {
        if (is_string($permission)) {
            $permission = Permission::where('slug', $permission)->first();
        }

        if ($permission) {
            $this->permissions()->detach($permission->id);
        }
    }
}


