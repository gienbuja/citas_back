<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CitaPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //

    }

    public function destroy()
    {

        if (auth()->user()->rol == 'Admin') {
            return true;
        }
        return false;
    }
}
