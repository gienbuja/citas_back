<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\CitaCreada;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cita;
use App\Policies\CitaPolicy;

class CitaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::user()->rol == 'Admin') {
            return response()->json(Cita::with('user')->get());
        }
        $citas = Cita::where('user_id', Auth::id())->get();
        return response()->json($citas);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'descripcion' => 'required|string|max:255',
            'hora' => 'required|string',
            'fecha' => 'required|date|after_or_equal:today',
        ]);

        $cita = new Cita();
        $cita->fecha = $request->input('fecha');
        $cita->hora = $request->input('hora');
        $cita->descripcion = $request->input('descripcion');
        $cita->user_id = Auth::id();
        $cita->save();

        // Enviar notificación al usuario
        $user = Auth::user();
        $user->notify(new CitaCreada($cita));
        $cita->refresh();
        if ($user->rol == 'Admin') {
            $cita->user = $user;
        }
        return response()->json($cita, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Cita $cita)
    {
        Gate::authorize('view', $cita);
        return response()->json($cita, 200);
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cita $cita)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cita $cita)
    {
        Gate::authorize('updateCita', $cita);
        $request->validate([
            'descripcion' => 'required|string|max:255',
            'hora' => 'required|date',
            'fecha' => 'required|date',
            'estado' => 'required|string|in:Pendiente,Confirmada,Cancelada'
        ]);

        $cita->fecha = $request->input('fecha');
        $cita->hora = $request->input('hora');
        $cita->descripcion = $request->input('descripcion');
        $cita->estado = $request->input('estado');
        $cita->save();

        $cita->refresh();
        if (Auth::user()->rol == 'Admin') {
            $cita->user = $cita->user;
        }

        return response()->json($cita, 200);
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cita $cita)
    {
        Gate::authorize('destroy', $cita);
        $cita->delete();
        return response()->json(['message' => 'Cita eliminada'], 200);
        //
    }
}
