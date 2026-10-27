@extends('errors.layout')

@section('title', 'Página não encontrada')
@section('code', '404')
@section('heading', 'Esta conversa perdeu o fio')
@section('message', 'A página que procura não existe ou foi movida. Explore os nossos serviços ou volte à página inicial.')
@section('actions')
    <a href="{{ url('/servicos') }}" class="inline-flex items-center gap-2 rounded-full border border-brown-200 bg-white px-7 py-3.5 font-display text-[15px] font-semibold text-brown-800 hover:border-brown-800">Ver serviços</a>
@endsection
