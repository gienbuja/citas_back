<?php
namespace App\Http\Controllers;
use Carbon\Carbon;
use Gate;
use Illuminate\Http\Request;
use App\Models\Cita;

class DashboardController extends Controller
{
    public function getCitasPorMes(Request $request)
    {
        $user = $request->user();
        
        if (Gate::allows('admin-only')) {
            $citas = Cita::all();
        } else {
            $citas = Cita::where('user_id', $user->id)->get();
        }

        $result = [];
        foreach ($citas as $cita) {
            $mes = Carbon::parse($cita->fecha)->locale('es')->translatedFormat('F'); // Nombre del mes completo en español
            if (!isset($result[$mes])) {
                $result[$mes] = 0;
            }
            $result[$mes]++;
        }

        return response()->json($result);
    }
}