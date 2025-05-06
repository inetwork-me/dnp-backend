<div class="app-sidebar menu-fixed" data-background-color="man-of-steel" data-scroll-to-active="true">
    <!-- main menu header-->
    <!-- Sidebar Header For Starter Kit starts-->
    <div class="sidebar-header">
        <div class="logo clearfix">
            <a class="logo-text float-left" href="javascript:;">
                <div class="logo-img">
                    @if (get_setting('system_logo_white') != null)
                        <img class="mw-100" src="{{ uploaded_asset(get_setting('system_logo_white')) }}"
                            class="brand-icon" alt="{{ get_setting('site_name') }}">
                    @else
                        <img class="mw-100" src="{{ static_asset('assets/img/logo.png') }}" class="brand-icon"
                            alt="{{ get_setting('site_name') }}">
                    @endif
                </div>
            </a>
            <a class="nav-toggle d-none d-lg-none d-xl-block" id="sidebarToggle" href="javascript:;">
                <svg width="20" height="20" viewBox="0 0 15 15" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M0 3.5A3.5 3.5 0 0 1 3.5 0h8a3.5 3.5 0 1 1 0 7h-8A3.5 3.5 0 0 1 0 3.5M3.5 2a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3M15 11.5a3.5 3.5 0 0 1-3.5 3.5h-8a3.5 3.5 0 1 1 0-7h8a3.5 3.5 0 0 1 3.5 3.5M11.5 13a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3"
                        fill="#fff" />
                </svg>
            </a>
            <a class="nav-close d-block d-lg-block d-xl-none" id="sidebarClose" href="javascript:;"><i
                    class="ft-x"></i></a>
        </div>
    </div>
    <!-- Sidebar Header Ends-->
    <!-- / main menu header-->
    <!-- main menu content-->
    <div class="sidebar-content main-menu-content">
        <div class="nav-container">
            {!! render_menu() !!}
        </div>
    </div>

    <!-- main menu content-->
    <div class="sidebar-background"></div>
    <!-- main menu footer-->
    <!-- include includes/menu-footer-->
    <!-- main menu footer-->
</div>
