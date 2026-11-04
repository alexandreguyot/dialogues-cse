@extends('layouts.app', ['theme' => $page->theme])
@section('title', $page->title . ' | Dialogues CSE')
@section('description', $page->meta_description ?? '')
@section('content')
    @isset($preview)
        <div class="flash">Aperçu de la page{{ $page->is_published ? '' : ' (brouillon, non visible des visiteurs)' }}</div>
    @endisset
    <div class="content">
        @foreach($page->blocks as $block)
            @include('site.blocks.' . $block['type'], ['block' => $block])
        @endforeach
    </div>
@endsection
