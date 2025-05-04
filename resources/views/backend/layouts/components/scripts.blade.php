<div class="sidenav-overlay"></div>
<div class="drag-target"></div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="{{ asset('assets') }}/vendors/js/vendors.min.js"></script>
<script src="{{ asset('assets') }}/vendors/js/switchery.min.js"></script>
<script src="{{ asset('assets') }}/js/core/app-menu.js"></script>
<script src="{{ asset('assets') }}/js/core/app.js"></script>
<script src="{{ asset('assets') }}/js/notification-sidebar.js"></script>
<script src="{{ asset('assets') }}/js/customizer.js"></script>
<script src="{{ asset('assets') }}/js/scroll-top.js"></script>
<script src="{{ asset('assets') }}/js/scripts.js"></script>
<script src="{{ asset('assets') }}/js/vendors.js"></script>
<script src="{{ asset('assets') }}/js/aiz-core.js?v={{ rand(1000, 9999) }}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js" integrity="sha384-+sLIOodYLS7CIrQpBjl+C7nPvqq+FbNUBDunl/OZv93DB7Ln/533i8e/mZXLi/P+" crossorigin="anonymous"></script>

<script>
    if ($('#lang-change').length > 0) {
        $('#lang-change .dropdown-menu a').each(function() {
            $(this).on('click', function(e) {
                e.preventDefault();
                var $this = $(this);
                var locale = $this.data('flag');
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '{{ route('language.change') }}', true);
                xhr.setRequestHeader('Content-Type', 'application/json;charset=UTF-8');
                xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
                xhr.onreadystatechange = function() {
                  if (xhr.readyState === 4 && xhr.status === 200) {
                    location.reload();
                  }
                };
                xhr.send(JSON.stringify({ locale: locale }));

            });
        });
    }
</script>

<script>
    function togglePassword() {
        const passwordInput = document.getElementsByClassName("password");
        const eyeIcon = document.querySelector(".toggle-eye");
        const isPassword = passwordInput[0].type === "password";

        passwordInput[0].type = isPassword ? "text" : "password";
        eyeIcon.innerHTML = isPassword ?
            '<svg width="20" height="20" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="m.5 7.5-.464-.186a.5.5 0 0 0 0 .372zm14 0 .464.186a.5.5 0 0 0 0-.372zm-7 4.5c-2.314 0-3.939-1.152-5.003-2.334a9.4 9.4 0 0 1-1.449-2.164l-.08-.18-.004-.007v-.001L.5 7.5l-.464.186v.002l.003.004.026.063.078.173a10.4 10.4 0 0 0 1.61 2.406C2.94 11.653 4.814 13 7.5 13zm-7-4.5.464.186.004-.008a3 3 0 0 1 .08-.18 9.4 9.4 0 0 1 1.449-2.164C3.56 4.152 5.186 3 7.5 3V2C4.814 2 2.939 3.348 1.753 4.666a10.4 10.4 0 0 0-1.61 2.406 6 6 0 0 0-.104.236l-.002.004v.001H.035zm7-4.5c2.314 0 3.939 1.152 5.003 2.334a9.4 9.4 0 0 1 1.449 2.164l.08.18.004.007v.001L14.5 7.5l.464-.186v-.002l-.003-.004-.026-.063-.078-.173a10.4 10.4 0 0 0-1.61-2.406C12.06 3.348 10.186 2 7.5 2zm7 4.5-.464-.186-.003.008-.015.035-.066.145a9.4 9.4 0 0 1-1.449 2.164C11.44 10.848 9.814 12 7.5 12v1c2.686 0 4.561-1.348 5.747-2.665a10.4 10.4 0 0 0 1.61-2.407 6 6 0 0 0 .104-.236l.002-.004v-.001h.001zM7.5 9A1.5 1.5 0 0 1 6 7.5H5A2.5 2.5 0 0 0 7.5 10zM9 7.5A1.5 1.5 0 0 1 7.5 9v1A2.5 2.5 0 0 0 10 7.5zM7.5 6A1.5 1.5 0 0 1 9 7.5h1A2.5 2.5 0 0 0 7.5 5zm0-1A2.5 2.5 0 0 0 5 7.5h1A1.5 1.5 0 0 1 7.5 6z" fill="#6B7280"/></svg>' // eye open
            :
            '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 64 64" xml:space="preserve"><path fill="none" stroke="#6B7280" stroke-width="3" stroke-miterlimit="10" d="M1 32s11 15 31 15 31-15 31-15-11-15-31-15S1 32 1 32z"/><circle fill="none" stroke="#6B7280" stroke-width="3" stroke-miterlimit="10" cx="32" cy="32" r="7"/><path fill="none" stroke="#6B7280" stroke-width="3" stroke-miterlimit="10" d="M9 55 55 9"/></svg>'; // eye slash
    }
</script>

@yield('script')
