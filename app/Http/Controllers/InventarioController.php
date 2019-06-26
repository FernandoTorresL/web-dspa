<?php

namespace App\Http\Controllers;

use App\Inventory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;

class InventarioController extends Controller
{

    public function home()
    {
        $user_id = Auth::user()->id;
        $user_name = Auth::user()->name;
        $user_job_id = Auth::user()->job_id;
        $user_del_id = Auth::user()->delegacion_id;
        $user_del_name = Auth::user()->delegacion->name;

        $texto_log = ' User_id:' . $user_id . '|User:' . $user_name . '|Del:' . $user_del_id . '|Job:' . $user_job_id;

        Log::info('Visitando Inventario ' . $texto_log);

        if ( Gate::allows( 'ver_inventario_del') || Gate::allows( 'ver_inventario_gral') )
        {
            $inventory_id = env('INVENTORY_ID');
            $list_inventario = DB::table('detalle_ctas AS D')
                ->select('D.cuenta', 'D.name', 'D.install_data',
                    'G1.name AS gpo_name', 'W.name AS work_area_name',
                    DB::raw("EXISTS(SELECT 1 FROM detalle_ctas WHERE ciz_id = 1 AND inventory_id = $inventory_id AND cuenta = D.cuenta) AS CIZ1"),
                    DB::raw("EXISTS(SELECT 1 FROM detalle_ctas WHERE ciz_id = 2 AND inventory_id = $inventory_id AND cuenta = D.cuenta) AS CIZ2"),
                    DB::raw("EXISTS(SELECT 1 FROM detalle_ctas WHERE ciz_id = 3 AND inventory_id = $inventory_id AND cuenta = D.cuenta) AS CIZ3") )
                ->join('groups AS G1', 'D.gpo_owner_id', '=', 'G1.id')
                ->join('work_areas AS W', 'D.work_area_id', '=', 'W.id')
                ->where( 'D.inventory_id', $inventory_id );

            //if is a 'Delegational' user, add delegacion_id to the query
            if ( Gate::allows('ver_inventario_del') )
                $list_inventario = $list_inventario->where('D.delegacion_id', $user_del_id);

            $list_inventario = $list_inventario
                        ->distinct()
                        ->orderby('D.work_area_id', 'desc')
                        ->orderby('D.cuenta')
                        ->paginate( env('ROWS_ON_PAGINATE'),
                            ['D.cuenta', 'D.name', 'D.install_data',
                            'G1.name AS gpo_name', 'W.name AS work_area_name'] );

            $cut_off_date = Inventory::find( $inventory_id )->cut_off_date;
        }
        else {

            Log::warning('Sin permisos-Consultar Inventario ' . $texto_log);
            return redirect('ctas')->with('message', 'No tiene permitido consultar el inventario.');
        }

        return view('ctas/inventario/show',
            compact('list_inventario' ,
                    'cut_off_date',
                    'user_del_name',
                    'user_del_id') );

    }
}
