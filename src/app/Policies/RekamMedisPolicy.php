<?php

namespace App\Policies;

use App\Models\RekamMedis;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RekamMedisPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin', 'dokter', 'perawat']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, RekamMedis $rekamMedis): bool
    {
        // Dokter hanya dapat melihat rekam medis pasien yang mereka tangani
        if ($user->hasRole('dokter')) {
            $dokterId = $user->dokter->id ?? null;
            return $dokterId && $rekamMedis->dokter_id == $dokterId;
        }
        
        return $user->hasAnyRole(['super_admin', 'admin', 'perawat']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'admin', 'dokter']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, RekamMedis $rekamMedis): bool
    {
        // Dokter hanya dapat mengupdate rekam medis pasien yang mereka tangani
        if ($user->hasRole('dokter')) {
            $dokterId = $user->dokter->id ?? null;
            return $dokterId && $rekamMedis->dokter_id == $dokterId;
        }
        
        return $user->hasAnyRole(['super_admin', 'admin']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, RekamMedis $rekamMedis): bool
    {
        return $user->hasRole('super_admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, RekamMedis $rekamMedis): bool
    {
        return $user->hasRole('super_admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, RekamMedis $rekamMedis): bool
    {
        return $user->hasRole('super_admin');
    }
}
