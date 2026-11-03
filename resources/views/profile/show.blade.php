@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card bg-blueGray-100 lg:w-6/12">
        <div class="card-header">
            <div class="card-header-container">
                <h6 class="card-title">
                    {{ __('global.my_profile') }}
                </h6>
            </div>
        </div>

        <div class="card-body">
            <div class="pt-3">
                @livewire('update-profile-information-form')

                    <hr class="mt-6 border-b-1 border-blueGray-300">

                    @livewire('update-password-form')

                    <hr class="mt-6 border-b-1 border-blueGray-300">

                    <h2 class="profile-section-title">Authenticator</h2>
                    @if(auth()->user()->hasTwoFactor())
                        <p class="status is-online">Activé</p>
                        @unless(auth()->user()->is_admin)
                            <form class="mt-4" method="POST" action="{{ route('two-factor.disable') }}">
                                @csrf
                                @method('DELETE')
                                <div class="form-group {{ $errors->has('code') ? 'invalid' : '' }}">
                                    <label class="form-label" for="two-factor-code">Code à 6 chiffres</label>
                                    <input class="form-control" id="two-factor-code" name="code" type="text" inputmode="numeric" autocomplete="one-time-code" required>
                                    <div class="validation-message">{{ $errors->first('code') }}</div>
                                </div>
                                <button class="btn btn-rose mt-3">Désactiver</button>
                            </form>
                        @endunless
                    @else
                        <a class="btn btn-indigo" href="{{ route('two-factor.setup') }}">Activer l'authenticator</a>
                    @endif
            </div>
        </div>
    </div>


</div>
@endsection