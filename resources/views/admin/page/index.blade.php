@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card">
        <div class="card-header">
            <div class="card-header-container">
                <h1 class="card-title">{{ trans('cruds.page.title') }}</h1>

                @can('page_create')
                    <a class="btn btn-indigo" href="{{ route('admin.pages.create') }}">
                        {{ trans('cruds.page.add') }}
                    </a>
                @endcan
            </div>
        </div>
        @livewire('page.index')

    </div>
</div>
@endsection
