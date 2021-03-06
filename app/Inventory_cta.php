<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Inventory_cta extends Model
{
    use Sortable;

    protected $guarded = [];

    public $sortable = [    'cuenta', 
                            'name', 
                            'ciz_1',
                            'ciz_2',
                            'ciz_3',
                            'gpo_owner_id', 
                            'install_data', 
                            'delegacion_id', 
                            'work_area_id'];

    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class);
    }

    public function registros_en_baja()
    {
        $cut_off_date = Inventory::find( env('INVENTORY_ID') )->cut_off_date;

        $registros_en_baja = 
                        $this
                        ->hasMany(Resultado_Solicitud::class, 'cuenta', 'cuenta')
                        ->with('solicitud', 'solicitud.gpo_nuevo')
                        ->where('rechazo_mainframe_id', NULL)
                        ->whereHas( 'resultado_lote', function ( $list_where ) use ($cut_off_date) {
                            $list_where
                                ->where( 'resultado_lotes.attended_at', '>', $cut_off_date ); } )
                        ->whereHas( 'solicitud', function ( $list_where ) {
                            $list_where
                                ->whereIn( 'solicitudes.movimiento_id', [2, 3] ); } 
                        );

        return $registros_en_baja;
    }

    public function solicitud_with_baja()
    {
        $cut_off_date = Inventory::find( env('INVENTORY_ID') )->cut_off_date;

        $solicitud_with_baja = 
                        $this
                        ->hasMany(Resultado_Solicitud::class, 'cuenta', 'cuenta')
                        ->with('solicitud', 'solicitud.gpo_nuevo')
                        ->where('rechazo_mainframe_id', NULL)
                        ->whereHas( 'resultado_lote', function ( $list_where ) use ($cut_off_date) {
                            $list_where
                                ->where( 'resultado_lotes.attended_at', '>', $cut_off_date ); } )
                        ->whereHas( 'solicitud', function ( $list_where ) {
                            $list_where
                                ->where( 'solicitudes.movimiento_id', 2 ); } 
                        );

        return $solicitud_with_baja;
    }

    public function inventory() {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }

    public function gpo_owner() {
        return $this->belongsTo(Group::class, 'gpo_owner_id');
    }

    public function delegacion() {
        return $this->belongsTo(Delegacion::class, 'delegation_id');
    }

    public function work_area() {
        return $this->belongsTo(Work_area::class);
    }

    public function grupo()
    {
        return $this->hasOne(Group::class, 'id', 'gpo_owner_id');
    }

    public function tipo_cuenta()
    {
        return $this->hasOne(Work_area::class, 'id', 'work_area_id');
    }

    public function cizsSortable($query, $direction)
    {
        return $query->orderBy('ciz_1', $direction)
                    ->orderBy('ciz_2', $direction)
                    ->orderBy('ciz_3', $direction);
    }
}
