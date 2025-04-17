<!DOCTYPE html>
<html class="loading" lang="en">

@include('backend.layouts.components.head')

<body class="vertical-layout vertical-menu 2-columns  navbar-sticky nav-collapsed" data-menu="vertical-menu" data-col="2-columns">
    @include('backend.layouts.components.nav')
    <div class="wrapper">
        @include('backend.layouts.components.sidebar')
        <div class="main-panel">
            <!-- BEGIN : Main Content-->
            <div class="main-content">
                <div class="content-overlay"></div>
                <div class="content-wrapper">
                    @include('backend.layouts.components.breadcrumb')
                    @yield('body')
                </div>
            </div>
            <!-- END : End Main Content-->
            <!-- BEGIN : Footer-->
            @include('backend.layouts.components.footer')
        </div>
    </div>
    @include('backend.layouts.components.scripts')
</body>

</html>