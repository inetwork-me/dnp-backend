@php
    if (Session::has('locale')) {
        $locale = Session::get('locale', Config::get('app.locale'));
    } else {
        $locale = env('DEFAULT_LANGUAGE');
    }
    $lang_name = \App\Models\Language::where('code', $locale)->first()->name;
@endphp

<li class="i18n-dropdown dropdown nav-item mr-2">
    <a class="nav-link d-flex align-items-center dropdown-toggle dropdown-language" id="dropdown-flag" href="javascript:;"
        data-toggle="dropdown"><img class="langimg selected-flag" src="{{ asset('assets/img/flags/' . $locale . '.png') }}"
            alt="flag"><span class="selected-language d-md-flex d-none">{{ $lang_name }}</span></a>
    <div class="dropdown-menu dropdown-menu-right text-left" aria-labelledby="dropdown-flag">
        @foreach (\App\Models\Language::where('status', 1)->get() as $key => $language)
            <a href="javascript:void(0)" data-flag="{{ $language->code }}"
                class="dropdown-item @if ($locale == $language->code) active @endif">
                <img src="{{ asset('assets/img/flags/' . $language->code . '.png') }}" class="mr-2">
                <span class="language">{{ $language->name }}</span>
            </a>
        @endforeach
    </div>
</li>
