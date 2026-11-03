<aside class="admin-sidebar">
    <div class="admin-brand">
        <a href="{{ route('admin.home') }}">
            <img src="{{ asset('img/logo.svg') }}" alt="Dialogues, tableau de bord">
        </a>
        <button type="button" class="admin-menu-toggle" aria-label="Ouvrir le menu" onclick="this.closest('.admin-sidebar').classList.toggle('is-open')">
            <x-icone name="menu" />
        </button>
    </div>

    <nav class="admin-nav">
        <a href="{{ route('admin.home') }}" class="{{ request()->is('admin') ? 'is-active' : '' }}">
            <x-icone name="accueil" /> {{ trans('global.dashboard') }}
        </a>

        @can('page_access')
            <a href="{{ route('admin.pages.index') }}" class="{{ request()->is('admin/pages*') ? 'is-active' : '' }}">
                <x-icone name="document" /> {{ trans('cruds.page.title') }}
            </a>
        @endcan

        <a href="{{ route('home') }}" target="_blank">
            <x-icone name="lien-externe" /> Voir le site
        </a>

        @can('user_management_access')
            <p class="admin-nav-group">Comptes</p>
            @can('user_access')
                <a href="{{ route('admin.users.index') }}" class="{{ request()->is('admin/users*') ? 'is-active' : '' }}">
                    <x-icone name="personnes" /> {{ trans('cruds.user.title') }}
                </a>
            @endcan
            @can('role_access')
                <a href="{{ route('admin.roles.index') }}" class="{{ request()->is('admin/roles*') ? 'is-active' : '' }}">
                    <x-icone name="gestion-comptes" /> {{ trans('cruds.role.title') }}
                </a>
            @endcan
            @can('permission_access')
                <a href="{{ route('admin.permissions.index') }}" class="{{ request()->is('admin/permissions*') ? 'is-active' : '' }}">
                    <x-icone name="cle" /> {{ trans('cruds.permission.title') }}
                </a>
            @endcan
        @endcan

        <div class="admin-nav-end">
            @if(file_exists(app_path('Http/Controllers/Auth/UserProfileController.php')))
                @can('auth_profile_edit')
                    <a href="{{ route('profile.show') }}" class="{{ request()->is('profile') ? 'is-active' : '' }}">
                        <x-icone name="profil" /> {{ trans('global.my_profile') }}
                    </a>
                @endcan
            @endif
            <a href="#" onclick="event.preventDefault(); document.getElementById('logoutform').submit();">
                <x-icone name="deconnexion" /> {{ trans('global.logout') }}
            </a>
        </div>
    </nav>
</aside>
