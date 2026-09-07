@extends('layouts.public')

@section('title', 'Descubre FESC en el campus')

@section('content')
    <x-landing.hero />
    <x-landing.how-it-works :steps="$steps" />
    <x-landing.news-section :featured="$featured" :items="$contents" :images="$newsImages" />
    <x-landing.campus-section :locations="$locations" />
    <x-landing.nfc-experience />
    <x-landing.official-cta />
@endsection
