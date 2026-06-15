@extends('errors.layout')

@section('title', 'Trop de requêtes')
@section('code', 'Trop de requêtes')
@section('heading', 'Vous allez un peu trop vite')
@section('message', 'Vous avez effectué trop d\'actions en peu de temps. Patientez quelques instants avant de réessayer.')
@section('icon')
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
@endsection
