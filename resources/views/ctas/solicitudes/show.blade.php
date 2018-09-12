@extends('layouts.app')

@section('content')
    <div class="row">
        <a class="nav-link" href="{{ url('/') }}">Regresar</a>
    </div>
    @if(session()->has('message'))
        <div class="alert alert-success">
            {{ session()->get('message') }}
        </div>
    @endif
    <br>
    <h3 class="card-title"><strong>Datos de la Solicitud</strong></h3>
    <br>
    @include('ctas.solicitudes.solicitud')
@endsection
