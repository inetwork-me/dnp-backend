<!DOCTYPE html>
@if (\App\Models\Language::where('code', Session::get('locale', Config::get('app.locale')))->first()->rtl == 1)
    <html class="loading" dir="rtl" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@else
    <html class="loading" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@endif

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
                    @include('backend.layouts.components.errors')
                    @yield('content')
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