@extends('backend.layouts.app')

@section('title')
{{ translate('Languages Configurations') }}
@endsection

@section('content')

<div class="row">
    <div class="col-6">
        <x-dynamic-form 
            :action="route('env_key_update.update')" 
            method="POST" 
            title="Default Language Setting" 
            :submitLabel="'Save'"
            :fields="[
                [
                    'type' => 'hidden',
                    'name' => 'types[]',
                    'value' => 'DEFAULT_LANGUAGE',
                ],
                [
                    'label' => 'Default Language',
                    'name' => 'DEFAULT_LANGUAGE',
                    'type' => 'select',
                    'required' => true,
                    'class' => 'aiz-selectpicker',
                    'options' => \App\Models\Language::where('status', 1)->get()->map(function ($language) {
                        return [
                            'value' => $language->code,
                            'label' => $language->name,
                        ];
                    })->toArray(),
                    'value' => env('DEFAULT_LANGUAGE'),
                    'col' => 'col-md-12'
                ],
            ]"
        />
    </div>
    
    <div class="col-6">
        <x-dynamic-form 
            :action="route('app-translations.import')" 
            method="POST" 
            title="Import App Translations" 
            :submitLabel="'Import'"
            :fields="[
                [
                    'label' => 'English Translation File',
                    'name' => 'DEFAULT_LANGUAGE',
                    'type' => 'file',
                    'required' => true,
                    'class' => 'form-control',
                    'col' => 'col-md-12',
                    'hint' => 'Choose Translation File with extention .arb'
                ],
            ]"
        />
    </div>
</div>

@php
$customRenderers = [
    'publish' => fn($item) => '<label class="aiz-switch aiz-switch-success mb-0">
                                    <input type="checkbox" onchange="update_status(this)" value="' . $item->id . '"' . ($item->status ? ' checked' : '') . '>
                                    <span></span>
                                </label>',
    'is_rtl' => fn($item) => '<label class="aiz-switch aiz-switch-success mb-0">
                                    <input type="checkbox" onchange="update_rtl_status(this)" value="' . $item->id . '"' . ($item->rtl ? ' checked' : '') . '>
                                    <span></span>
                                </label>',
];
$othersActions = [
    'delete_lang' => fn($item) => $item->code != 'en' ? 
        '<a class="table_btn"
            href="' .route('languages.destroy', $item->id) . '"
            title="' . translate('Delete') . '"
            onclick="return confirm(\'Are You Sure\')"
            >
            <svg fill="red" width="25" height="25" viewBox="-3 -2 24 24"
                xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMinYMin"
                class="jam jam-trash">
                <path
                    d="M6 2V1a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v1h4a2 2 0 0 1 2 2v1a2 2 0 0 1-2 2h-.133l-.68 10.2a3 3 0 0 1-2.993 2.8H5.826a3 3 0 0 1-2.993-2.796L2.137 7H2a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2zm10 2H2v1h14zM4.141 7l.687 10.068a1 1 0 0 0 .998.932h6.368a1 1 0 0 0 .998-.934L13.862 7zM7 8a1 1 0 0 1 1 1v7a1 1 0 0 1-2 0V9a1 1 0 0 1 1-1m4 0a1 1 0 0 1 1 1v7a1 1 0 0 1-2 0V9a1 1 0 0 1 1-1" />
            </svg>
        </a>' : '',
    'frontend_trans' => fn($item) => 
        '<a class="table_btn"
            href="' .route('app-translations.show', $item->id) . '"
            title="' . translate('Frontend Translation') . '">
            <svg width="25" height="25" viewBox="0 0 35 35" data-name="Layer 2" xmlns="http://www.w3.org/2000/svg"><path d="M33.5 22.4a1.25 1.25 0 0 1-1.16-.78l-5.73-13.9L20 21.68a1.25 1.25 0 1 1-2.26-1.07l7.82-16.52a1.25 1.25 0 0 1 2.28.06l6.82 16.52A1.26 1.26 0 0 1 34 22.3a1.2 1.2 0 0 1-.5.1"/><path d="M30.19 16h-7.62a1.25 1.25 0 0 1 0-2.5h7.62a1.25 1.25 0 0 1 0 2.5M3.39 31.62A1.22 1.22 0 0 1 2.34 31a1.25 1.25 0 0 1 .38-1.72c8.34-5.28 9.14-13 9.17-13.32a1.25 1.25 0 0 1 2.49.22c0 .38-.89 9.24-10.32 15.21a1.26 1.26 0 0 1-.67.23"/><path d="M14.62 29.81a1 1 0 0 1-.3 0c-6.13-1.56-10.93-8.58-11.13-8.88a1.25 1.25 0 1 1 2.07-1.4c0 .06 4.47 6.53 9.66 7.82a1.25 1.25 0 0 1-.3 2.46m2.07-13.54H1.5a1.25 1.25 0 0 1 0-2.5h15.19a1.25 1.25 0 0 1 0 2.5"/><path d="M8.68 16.27a1.24 1.24 0 0 1-1.21-1l-.84-3.46a1.25 1.25 0 1 1 2.43-.58l.84 3.46a1.26 1.26 0 0 1-.9 1.55 1.4 1.4 0 0 1-.32.03"/></svg>
        </a>',
    'download_trans' => fn($item) => 
        '<a class="table_btn"
            href="' .route('app-translations.export', $item->id) . '"
            title="' . translate('Download Translation') . '">
            <svg width="25" height="25" viewBox="-5 -5 24 24" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMinYMin" class="jam jam-download"><path d="m8 6.641 1.121-1.12a1 1 0 0 1 1.415 1.413L7.707 9.763a.997.997 0 0 1-1.414 0L3.464 6.934A1 1 0 1 1 4.88 5.52L6 6.641V1a1 1 0 1 1 2 0zM1 12h12a1 1 0 0 1 0 2H1a1 1 0 0 1 0-2"/></svg>
        </a>',
];
@endphp

<x-list-table
    :tableHeaders="['Name','Code','Frontend Language','Is RTL','Status']"
    :tableKeys="['name', 'code','app_lang_code','is_rtl','publish']"
    :translatableKeys="[]"
    :tableData="$languages"
    title="All Languages"
    :othersActions="$othersActions"
    addRoute="languages.create"
    showRoute="languages.show"
    editRoute="languages.edit"
    deleteRoute="#"
    :editParams="fn($item) => ['language' => $item->id]"
    :deleteParams="fn($item) => [$item->id]"
    :showParams="fn($item) => [$item->id]"
	:permissions="[
        'show' => '',
        'edit' => '',
        'delete' => ''
    ]"
	:customRenderers="$customRenderers"
/>

@endsection

@section('modal')
    @include('backend.layouts.components.modals.delete_modal')
@endsection


@section('script')
    <script type="text/javascript">
        function update_rtl_status(el){
            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('{{ route('languages.update_rtl_status') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    location.reload();
                }
                else{
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }
        function update_status(el){
            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('{{ route('languages.update-status') }}', {
                    _token : '{{ csrf_token() }}', 
                    id : el.value, 
                    status : status
                }, function(data) {
                if(data == 1) {
                    location.reload();
                }
                else { 
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }
    </script>
@endsection
