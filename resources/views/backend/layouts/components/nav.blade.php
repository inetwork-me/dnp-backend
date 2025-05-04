<nav class="navbar navbar-expand-lg navbar-light header-navbar navbar-fixed bg-white">
    <div class="container-fluid navbar-wrapper">
        <div class="navbar-header d-flex">
            <div class="navbar-toggle menu-toggle d-xl-none d-block float-left align-items-center justify-content-center"
                data-toggle="collapse"><i class="ft-menu font-medium-3"></i></div>
            <span class="welcome_nav_txt">{{ translate('Welcome') }}</span>
        </div>
        <div class="navbar-container">
            <div class="collapse navbar-collapse d-block" id="navbarSupportedContent">
                <ul class="navbar-nav">
                    @include('backend.layouts.components.languages_nav')
                    @include('backend.layouts.components.notifications_nav')
                    @include('backend.layouts.components.user_nav')
                </ul>
            </div>
        </div>
    </div>
</nav>
