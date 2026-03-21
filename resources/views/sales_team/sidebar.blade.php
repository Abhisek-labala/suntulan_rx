<div class="sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                {{-- Dashboard removed as requested --}}
                <li class="{{ Request::routeIs('rx.*') ? 'active' : '' }}">
                    <a href="{{ route('rx.index') }}">
                        <i class="fa fa-notes-medical"></i> <span>RX Details</span>
                    </a>
                </li>
                <li>
                    <form id="logout-form-sales" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>

                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form-sales').submit();">
                        <i class="fa fa-sign-out" aria-hidden="true"></i>
                        <span>Logout</span>
                    </a>
                </li>

            </ul>
        </div>
    </div>
</div>
