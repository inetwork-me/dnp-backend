<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="app-url" content="{{ getBaseURL() }}">
    <meta name="file-base-url" content="{{ getFileBaseURL() }}">
    <title>{{ translate('Admin') }} || @yield('title')</title>
    <link rel="icon" href="{{ uploaded_asset(get_setting('site_icon')) }}">
    <link rel="apple-touch-icon" href="{{ uploaded_asset(get_setting('site_icon')) }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link
        href="https://fonts.googleapis.com/css?family=Rubik:300,400,500,700,900|Montserrat:300,400,500,600,700,800,900"
        rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets') }}/fonts/feather/style.min.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets') }}/fonts/simple-line-icons/style.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets') }}/fonts/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets') }}/vendors/css/perfect-scrollbar.min.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets') }}/vendors/css/prism.min.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets') }}/vendors/css/switchery.min.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets') }}/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets') }}/css/bootstrap-extended.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets') }}/css/colors.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets') }}/css/components.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets') }}/css/themes/layout-dark.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/css/plugins/switchery.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets') }}/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- END: Custom CSS-->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


    <script>
        var AIZ = AIZ || {};
        AIZ.local = {
            nothing_selected: '{!! translate('Nothing selected', null, true) !!}',
            nothing_found: '{!! translate('Nothing found', null, true) !!}',
            choose_file: '{{ translate('Choose file') }}',
            file_selected: '{{ translate('File selected') }}',
            files_selected: '{{ translate('Files selected') }}',
            add_more_files: '{{ translate('Add more files') }}',
            adding_more_files: '{{ translate('Adding more files') }}',
            drop_files_here_paste_or: '{{ translate('Drop files here, paste or') }}',
            browse: '{{ translate('Browse') }}',
            upload_complete: '{{ translate('Upload complete') }}',
            upload_paused: '{{ translate('Upload paused') }}',
            resume_upload: '{{ translate('Resume upload') }}',
            pause_upload: '{{ translate('Pause upload') }}',
            retry_upload: '{{ translate('Retry upload') }}',
            cancel_upload: '{{ translate('Cancel upload') }}',
            uploading: '{{ translate('Uploading') }}',
            processing: '{{ translate('Processing') }}',
            complete: '{{ translate('Complete') }}',
            file: '{{ translate('File') }}',
            files: '{{ translate('Files') }}',
        }
    </script>

    @yield('modal')

    <link rel="stylesheet" href="{{ asset('assets') }}/css/vendors.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/css/aiz-core.css?v={{ rand(1000, 9999) }}">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/line-awesome/1.3.0/line-awesome/css/line-awesome.min.css"
        integrity="sha512-vebUliqxrVkBy3gucMhClmyQP9On/HAWQdKDXRaAlb/FKuTbxkjPKUyqVOxAcGwFDka79eTF+YXwfke1h3/wfg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

        <style>
            /* Normal sidebar */
            .aiz-sidebar {
                width: 250px;
                transition: width 0.3s;
            }
        
            /* Collapsed sidebar */
            .sidebar-collapsed .aiz-sidebar {
                width: 80px;
            }
        
            /* Hide menu text */
            .sidebar-collapsed .aiz-side-nav-text {
                display: none;
            }
        
            /* Center the icons */
            .aiz-side-nav-link {
                display: flex;
                align-items: center;
                padding: 10px 20px;
                transition: all 0.3s;
            }
        
            .aiz-side-nav-icon {
                margin-right: 10px;
                font-size: 20px;
                transition: all 0.3s;
            }
        
            .sidebar-collapsed .aiz-side-nav-link {
                justify-content: center;
            }
        
            .sidebar-collapsed .aiz-side-nav-icon {
                margin-right: 0;
            }
        
            /* Hide arrows in collapse */
            .sidebar-collapsed .aiz-side-nav-arrow {
                display: none;
            }
        
            /* Optionally hide search input inside sidebar when collapsed */
            .sidebar-collapsed .aiz-side-nav-search {
                display: none;
            }

            .aiz-table{
                text-align: center !important;
            }
        </style>
        

    @yield('css')
</head>
