<aside class="main-sidebar sidebar-dark-primary elevation-4" style="min-height: 917px;">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
        <span class="brand-text font-weight-light">{{ trans('panel.site_title') }}</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user (optional) -->

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs("admin.home") ? "active" : "" }}" href="{{ route("admin.home") }}">
                        <i class="fas fa-fw fa-tachometer-alt nav-icon">
                        </i>
                        <p>
                            {{ trans('global.dashboard') }}
                        </p>
                    </a>
                </li>
                @can('user_management_access')
                    <li class="nav-item has-treeview {{ request()->is("admin/permissions*") ? "menu-open" : "" }} {{ request()->is("admin/roles*") ? "menu-open" : "" }} {{ request()->is("admin/users*") ? "menu-open" : "" }} {{ request()->is("admin/teams*") ? "menu-open" : "" }} {{ request()->is("admin/audit-logs*") ? "menu-open" : "" }}">
                        <a class="nav-link nav-dropdown-toggle {{ request()->is("admin/permissions*") ? "active" : "" }} {{ request()->is("admin/roles*") ? "active" : "" }} {{ request()->is("admin/users*") ? "active" : "" }} {{ request()->is("admin/teams*") ? "active" : "" }} {{ request()->is("admin/audit-logs*") ? "active" : "" }}" href="#">
                            <i class="fa-fw nav-icon fas fa-users">

                            </i>
                            <p>
                                {{ trans('cruds.userManagement.title') }}
                                <i class="right fa fa-fw fa-angle-left nav-icon"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('permission_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.permissions.index") }}" class="nav-link {{ request()->is("admin/permissions") || request()->is("admin/permissions/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-unlock-alt">

                                        </i>
                                        <p>
                                            {{ trans('cruds.permission.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('role_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.roles.index") }}" class="nav-link {{ request()->is("admin/roles") || request()->is("admin/roles/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-briefcase">

                                        </i>
                                        <p>
                                            {{ trans('cruds.role.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('user_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.users.index") }}" class="nav-link {{ request()->is("admin/users") || request()->is("admin/users/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-user">

                                        </i>
                                        <p>
                                            {{ trans('cruds.user.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('team_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.teams.index") }}" class="nav-link {{ request()->is("admin/teams") || request()->is("admin/teams/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-users">

                                        </i>
                                        <p>
                                            {{ trans('cruds.team.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                           
                        </ul>
                    </li>
                @endcan
                @can('hesaplar_goruntuleme')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('admin/hesaplar') || request()->is('admin/hesaplar/*') ? 'active' : '' }}" href="{{ route('admin.hesaplar.index') }}">
                                <i class="fa-fw fas fa-wallet nav-icon">
                                </i>
                                <p>
                                    Hesaplar
                                </p>
                            </a>
                        </li>
                    @endcan
                    @can('masraflar_goruntuleme')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('admin/masraflar') || request()->is('admin/masraflar/*') ? 'active' : '' }}" href="{{ route('admin.masraflar.index') }}">
                                <i class="fa-fw fas fa-receipt nav-icon">
                                </i>
                                <p>
                                    Masraflar
                                </p>
                            </a>
                        </li>
                    @endcan
                    @can('projeler_goruntuleme')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('admin/projeler') || request()->is('admin/projeler/*') ? 'active' : '' }}" href="{{ route('admin.projeler.index') }}">
                                <i class="fa-fw fas fa-city nav-icon">
                                </i>
                                <p>
                                    Projeler
                                </p>
                            </a>
                        </li>
                    @endcan
                    @can('personeller_goruntuleme')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('admin/personeller') || request()->is('admin/personeller/*') ? 'active' : '' }}" href="{{ route('admin.personeller.index') }}">
                                <i class="fa-fw fas fa-users nav-icon">
                                </i>
                                <p>
                                    Personeller
                                </p>
                            </a>
                        </li>
                    @endcan
                    @can('tedarikci_goruntuleme')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('admin/tedarikciler') || request()->is('admin/tedarikciler/*') ? 'active' : '' }}" href="{{ route('admin.tedarikciler.index') }}">
                                <i class="fa-fw fas fa-industry nav-icon">
                                </i>
                                <p>
                                    Tedarikçiler
                                </p>
                            </a>
                        </li>
                    @endcan
                @if(\Illuminate\Support\Facades\Schema::hasColumn('teams', 'owner_id') && \App\Models\Team::where('owner_id', auth()->user()->id)->exists())
                    <li class="nav-item">
                        <a class="{{ request()->is("admin/team-members") || request()->is("admin/team-members/*") ? "active" : "" }} nav-link" href="{{ route("admin.team-members.index") }}">
                            <i class="fa-fw fa fa-users nav-icon">
                            </i>
                            <p>
                                {{ trans("global.team-members") }}
                            </p>
                        </a>
                    </li>
                @endif
                @if(file_exists(app_path('Http/Controllers/Auth/ChangePasswordController.php')))
                    @can('profile_password_edit')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('profile/password') || request()->is('profile/password/*') ? 'active' : '' }}" href="{{ route('profile.password.edit') }}">
                                <i class="fa-fw fas fa-key nav-icon">
                                </i>
                                <p>
                                    {{ trans('global.change_password') }}
                                </p>
                            </a>
                        </li>
                    @endcan
                @endif
                
                <li class="nav-item">
                    <a href="#" class="nav-link" onclick="event.preventDefault(); document.getElementById('logoutform').submit();">
                        <p>
                            <i class="fas fa-fw fa-sign-out-alt nav-icon">

                            </i>
                            <p>{{ trans('global.logout') }}</p>
                        </p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>