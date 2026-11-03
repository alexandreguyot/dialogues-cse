<button class="auth-link auth-logout" type="submit" form="logout-form">{{ trans('global.logout') }}</button>
@push('after')
    <form id="logout-form" action="{{ route('logout') }}" method="POST" hidden>@csrf</form>
@endpush
