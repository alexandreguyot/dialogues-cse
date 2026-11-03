@extends('layouts.admin')
@section('content')
<section class="dashboard-hero">
    <h1>Bonjour {{ Str::before(auth()->user()->name, ' ') }}</h1>
    @can('page_access')
        <p>
            Le site compte {{ trans_choice('{0} aucune page créée ici|{1} :count page en ligne|[2,*] :count pages en ligne', $publishedCount) }}@if($draftCount) et {{ trans_choice('{1} :count brouillon|[2,*] :count brouillons', $draftCount) }}@endif.
        </p>
    @endcan
    <div class="dashboard-actions">
        @can('page_create')
            <a class="btn btn-indigo" href="{{ route('admin.pages.create') }}">{{ trans('cruds.page.add') }}</a>
        @endcan
        <a class="btn btn-secondary" href="{{ route('home') }}" target="_blank">Voir le site</a>
    </div>
</section>

@can('page_access')
    <section class="dashboard-recent">
        <h2>Dernières modifications</h2>
        @forelse($recentPages as $page)
            <a class="dashboard-page" href="{{ Gate::allows('page_edit') ? route('admin.pages.edit', $page) : route('admin.pages.index') }}">
                <span class="dashboard-page-title">{{ $page->title }}</span>
                <span class="status {{ $page->is_published ? 'is-online' : '' }}">{{ $page->is_published ? trans('cruds.page.published') : trans('cruds.page.draft') }}</span>
                <span class="dashboard-page-date">{{ $page->updated_at?->format('d/m/Y à H:i') }}</span>
            </a>
        @empty
            <p class="dashboard-empty">Créez votre première page : elle reprendra automatiquement le design du site.</p>
        @endforelse
    </section>
@endcan
@endsection
