
    {{----Valijas--}}
    @if( count($listado_valijas) )
        <br>
        <h5 class="text-success">Total de valijas: {{ $listado_valijas->count() }}</h5>
        <div class="table table-hover table-sm">
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Del:Delegación_valija [(Delegación_origen)-#Oficio_Delegación|#Gestión] {(AÑO:YYYY)}</th>
                    </tr>
                </thead>
                <tbody class="text-monospace">
    @endif

    @php
        $var = 0;
        $lista_descargo = NULL;
    @endphp

    @forelse( $listado_valijas as $row_valija )
        @php
            $var += 1;
            $del_valija = str_pad($row_valija->delegacion_id, 2, "0", STR_PAD_LEFT);
            $del_sol = str_pad($row_valija->sol_id, 2, "0", STR_PAD_LEFT);
            $texto_renglon = ' [' . $del_sol . '-' . $row_valija->num_oficio_del . '|' . $row_valija->num_oficio_ca . '] ';
        @endphp
        <tr>
            <th scope="row">{{ $var }}</th>
            @if( isset($row_valija->id) )
                @if( $del_valija <> $del_sol )
                    @php
                        $color_no_match = 'text-danger';
                        $texto_no_coincide = 'La delegación de ésta valija no coincide con la delegación del user-id en algunos formatos';
                    @endphp
                @else
                    @php
                        $color_no_match = 'text-success';
                        $texto_no_coincide = '';
                    @endphp
                @endif
                <td class="small {{ $color_no_match }}">{{ 'Del:' . $del_valija . $texto_renglon . $texto_no_coincide }}</td>
            @else
                @php
                    $texto_renglon = '[' . $del_sol . '|' . env('APP_NAME') . ']';
                @endphp
                <td class="small text-success">{{ '(SIN VALIJA) ' . $texto_renglon }}</td>
            @endif

            @php
                $lista_descargo .= ' ' . $texto_renglon;
            @endphp
        </tr>

    @empty
        <h5 class="text-danger">No hay valijas</h5>
        <br>
    @endforelse
    <tr>
        <th scope="row"><br></th>
    </tr>
    <tr>
        <th class="text-info">Lista descargo: </th>
        <td class="small text-info">{{ $lista_descargo }}</td>
    </tr>
    </tbody>

    @if(count($listado_valijas))
        </table>
    </div>
    @endif