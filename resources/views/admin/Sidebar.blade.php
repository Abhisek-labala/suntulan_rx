<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                <li class="{{ Request::routeIs('admin.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard') }}">
                        <i class="fa fa-home"></i> <span>Dashboard</span>
                    </a>
                </li>
                <li class="{{ Request::routeIs('admin.analytics') ? 'active' : '' }}">
                    <a href="{{ route('admin.analytics') }}">
                        <i class="fa fa-bar-chart"></i> <span>Analytics</span>
                    </a>
                </li>
                @if(in_array(auth()->user()->role, ['FLM', 'admin']))
                <li class="{{ Request::routeIs('rx.*') ? 'active' : '' }}">
                    <a href="{{ route('rx.index') }}">
                        <i class="fa fa-notes-medical"></i> <span>RX Details</span>
                    </a>
                </li>
                @endif
                @if(in_array(auth()->user()->role, ['admin', 'TLM']))
                <li class="{{ Request::routeIs('sales-team.*') ? 'active' : '' }}">
                    <a href="{{ route('sales-team.index') }}">
                        <i class="fa fa-users"></i> <span>Sales Team</span>
                    </a>
                </li>
                <li class="submenu">
                    <a href="javascript:void(0);">
                        <i class="fa fa-sitemap"></i> <span>Organizational Setup</span>
                        <span class="menu-arrow"></span>
                    </a>
                    <ul style="display: none;">
                        <li><a href="{{ route('zones.index') }}"><i class="fa fa-globe"></i> Zones</a></li>
                        <li><a href="{{ route('regions.index') }}"><i class="fa fa-map-marker"></i> Regions</a></li>
                        <li><a href="{{ route('hqs.index') }}"><i class="fa fa-building"></i> HQs</a></li>
                        <li><a href="{{ route('designations.index') }}"><i class="fa fa-id-badge"></i> Designations</a></li>
                    </ul>
                </li>
                @endif
                <li>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>

                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fa fa-sign-out" aria-hidden="true"></i>
                        <span>Logout</span>
                    </a>
                </li>

            </ul>
        </div>
    </div>
</div>
