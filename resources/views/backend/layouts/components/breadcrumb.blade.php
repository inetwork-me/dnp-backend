<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="#">
                <img src="{{ asset('assets/img') }}/svg/home2.png" alt="">
                {{ translate('Home') }}
            </a>
        </li>
        <li class="breadcrumb-item">@yield('title')</li>
    </ol>
</nav>
