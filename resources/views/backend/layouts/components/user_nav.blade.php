<li class="dropdown nav-item mr-1">
    <a class="nav-link dropdown-toggle user-dropdown d-flex align-items-end" id="dropdownBasic2" href="javascript:;"
        data-toggle="dropdown">
        <img src="{{ uploaded_asset(Auth::user()->avatar_original) }}" class="img-fit"
            onerror="this.onerror=null;this.src='{{ asset('assets/img/user.png') }}';" height="35"
            width="35">
    </a>
    <div class="dropdown-menu text-left dropdown-menu-right m-0 pb-0" aria-labelledby="dropdownBasic2">
        <a class="dropdown-item" href="{{ route('profile.index') }}">
            <div class="d-flex align-items-center"><i class="ft-user mr-2"></i><span>{{ translate('Profile') }}</span></div>
        </a>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item" href="{{ route('logout') }}">
            <div class="d-flex align-items-center"><i class="ft-power mr-2"></i><span>{{ translate('Logout') }}</span></div>
        </a>
    </div>
</li>
