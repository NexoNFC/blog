@extends('layouts.admin')

@section('title', 'Estadísticas')
@section('heading', 'Estadísticas')
@section('subtitle', 'Indicadores de uso y contenido')

@section('content')
    <x-ui.alert type="info">
        Vista preparada para estadísticas generales, escaneos NFC y contenido. Los reportes reales se conectarán cuando exista persistencia.
    </x-ui.alert>
@endsection
