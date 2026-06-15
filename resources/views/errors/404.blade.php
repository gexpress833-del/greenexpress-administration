@extends('errors.layout')

@section('title', 'Page introuvable')
@section('code', 'Page introuvable')
@section('heading', 'Cette page n\'existe pas')
@section('message', 'La page que vous recherchez a peut-être été déplacée ou n\'existe plus. Vérifiez l\'adresse ou revenez à l\'accueil.')
@section('icon')
    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
@endsection
