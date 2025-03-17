<?php

namespace App\Policies;

use App\Models\Cita;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CitaPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //

    }
    public function view(User $user, Cita $cita)
    {
        return $user->rol === 'Admin' || $user->id === $cita->user_id;
    }

    public function updateCita(User $user, Cita $cita)
    {
        return $user->rol === 'Admin' || $user->id === $cita->user_id;
    }

    public function destroy(User $user, Cita $cita)
    {
        return $user->rol === 'Admin' ? Response::allow()
            : Response::deny('No es usuario administrador');
    }
}
