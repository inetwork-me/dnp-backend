@extends('backend.layouts.app')

@section('title')
    {{ translate('All States') }}
@endsection

@section('content')
<x-dynamic-form 
    :action="route('states.store')" 
    method="POST" 
    title="New State"
    :submitLabel="'Save'"
    cancelRoute="states.index"
    :fields="[
        ['label' => 'Name', 'name' => 'name', 'type' => 'text', 'required' => true],
        ['label' => 'Country', 'name' => 'country_id', 'type' => 'select', 'required' => true, 'options' => \App\Models\Country::where('status', 1)->get()->map(fn($country) => ['value' => $country->id, 'label' => $country->name])->toArray()],
    ]"
/>


@php
$customRenderers = [
    'publish' => fn($item) => '<label class="aiz-switch aiz-switch-success mb-0">
                                    <input type="checkbox" onchange="update_status(this)" value="' . $item->id . '"' . ($item->status ? ' checked' : '') . '>
                                    <span></span>
                                </label>',
    'country' => fn($item) => $item->country->name,
];
@endphp

<x-list-table
    :tableHeaders="['Name', 'Country','Status']"
    :tableKeys="['name', 'country','publish']"
    :translatableKeys="[]"
    :tableData="$states"
    title="All States"
    addRoute="#"
    showRoute="#"
    editRoute="states.edit"
    deleteRoute="states.destroy"
    :editParams="fn($item) => ['state' => $item->id, 'lang' => env('DEFAULT_LANGUAGE')]"
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
            $.post('{{ route('states.status') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
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
