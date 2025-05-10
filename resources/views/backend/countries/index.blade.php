@extends('backend.layouts.app')
@section('title')
{{ translate('Countries') }}
@endsection
@section('content')

<x-dynamic-form 
    :action="route('countries.store')" 
    method="POST" 
    title="New Country"
    :submitLabel="'Save'"
    cancelRoute="countries.index"
    :fields="[
        ['label' => 'Code', 'name' => 'code', 'type' => 'text', 'required' => true],
        ['label' => 'Name', 'name' => 'name', 'type' => 'text', 'required' => true],
    ]"
/>

@php
$customRenderers = [
    'publish' => fn($item) => '<label class="aiz-switch aiz-switch-success mb-0">
                                    <input type="checkbox" onchange="update_status(this)" value="' . $item->id . '"' . ($item->status ? ' checked' : '') . '>
                                    <span></span>
                                </label>',
];
@endphp

<x-list-table
    :tableHeaders="['Code', 'Name','Status']"
    :tableKeys="['code', 'name','publish']"
    :translatableKeys="[]"
    :tableData="$countries"
    title="All Countries"
    addRoute="#"
    showRoute="#"
    editRoute="countries.edit"
    deleteRoute="countries.destroy"
    :editParams="fn($item) => ['id' => $item->id, 'lang' => env('DEFAULT_LANGUAGE')]"
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

@section('script')
    <script type="text/javascript">

        function update_status(el){
            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('{{ route('countries.status') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '{{ translate('Country status updated successfully') }}');
                }
                else{
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

    </script>
@endsection
