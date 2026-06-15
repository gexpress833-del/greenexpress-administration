@extends('errors.layout')

@section('title', 'Session expirée')
@section('code', 'Session expirée')
@section('heading', 'Votre session a expiré')
@section('message', 'Pour votre sécurité, votre session a expiré après une période d\'inactivité. Veuillez recharger la page et réessayer.')
@section('icon')
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
@endsection
