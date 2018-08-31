<?php

namespace App\Http\Controllers;

use App\Group;
use App\Http\Requests\CreateSolicitudRequest;
use App\Movimiento;
use App\Solicitud;
use App\Subdelegacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SolicitudesController extends Controller
{
    public function home()
    {
        $user = Auth::user()->name;
        $del_id = Auth::user()->delegacion_id;
        $del_name = Auth::user()->delegacion->name;

        $movimientos = Movimiento::where('status', '>=', 1)->orderBy('name', 'asc')->get();
        $subdelegaciones = Subdelegacion::where('delegacion_id', $del_id)->orderBy('num_sub', 'asc')->get();
        $gruposNuevo =  Group::where('status', '=',  1)->orderBy('name', 'asc')->get();
        $gruposActual = Group::where('status', '>=', 1)->orderBy('name', 'asc')->get();

        Log::info('Visitando Crear Solicitud. Usuario:' . $user . '|Del:(' . $del_id . ')-' . $del_name);

        return view(
            'ctas.solicitudes.create', [
            'movimientos' =>  $movimientos,
            'subdelegaciones' =>  $subdelegaciones,
            'gruposNuevo' =>  $gruposNuevo,
            'gruposActual' =>  $gruposActual,
        ]);
    }

    public function create(CreateSolicitudRequest $request)
    {
        $user = $request->user();
        $archivo = $request->file('archivo');

        Log::info('Enviando Crear Solicitud. Usuario:' . $user->username );

        if (null == $request->input('gpo_nuevo')) {
            $gpo_nuevo = 0;
        } else {
            $gpo_nuevo = $request->input('gpo_nuevo');
        }

        if (null == $request->input('gpo_actual')) {
            $gpo_actual = 0;
        } else {
            $gpo_actual = $request->input('gpo_actual');
        }

        $solicitud = Solicitud::create([
            'valija_id' => 0,
            'fecha_solicitud_del' => $request->input('fecha_solicitud'),
            'lote_id' => 0,
            'delegacion_id' => $user->delegacion_id,
            'subdelegacion_id' => $request->input('subdelegacion'),
            'nombre' => strtoupper($request->input('nombre')),
            'primer_apellido' => strtoupper($request->input('primer_apellido')),
            'segundo_apellido' => strtoupper($request->input('segundo_apellido')),
            'matricula' => $request->input('matricula'),
            'curp' => strtoupper($request->input('curp')),
            'cuenta' => strtoupper($request->input('cuenta')),
            'movimiento_id' => $request->input('tipo_movimiento'),
            'gpo_nuevo_id' => $gpo_nuevo,
            'gpo_actual_id' => $gpo_actual,
            'comment' => $request->input('comment'),
            'causa_rechazo_id' => 0,
            'archivo' => $archivo->store('solicitudes/' . $user->delegacion_id, 'public'),
            'user_id' => $user->id,
        ]);

        return redirect('ctas/solicitudes/'.$solicitud->id)->with('message', '¡Solicitud creada!');
//        return redirect()->back()->with('message', 'Creación de cuenta exitosa. Por favor revisa tu correo en los próximos minutos para activar tu acceso.');
    }

    public function show(Solicitud $solicitud)
    {
        Log::info('Consultando Solicitud ID:' . $solicitud->id );

        return view('ctas.solicitudes.show', [
            'solicitud' => $solicitud
        ]);
    }
}