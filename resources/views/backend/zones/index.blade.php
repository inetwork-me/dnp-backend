@extends('backend.layouts.app')

@section('title')
{{ translate('Zones') }}
@endsection

@section('content')

<x-dynamic-form 
    :action="route('zones.store')" 
    method="POST" 
    title="New Zone"
    :submitLabel="'Save'"
    cancelRoute="zones.index"
    :fields="[
        ['label' => 'Name', 'name' => 'name', 'type' => 'text', 'required' => true],
        [
            'label' => 'States', 
            'name' => 'country_id', 
            'type' => 'select', 
            'required' => true, 
            'multiple' => true,
            'options' => \App\Models\Country::where('status', 1)->get()->map(fn($country) => [
                'value' => $country->id, 
                'label' => $country->name
            ])->toArray()
        ],
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
    :tableHeaders="['Name', 'Status']"
    :tableKeys="['name', 'publish']"
    :translatableKeys="[]"
    :tableData="$zones"
    title="All Zones"
    addRoute="#"
    showRoute="#"
    editRoute="zones.edit"
    deleteRoute="zones.destroy"
    :editParams="fn($item) => ['zone' => $item->id, 'lang' => env('DEFAULT_LANGUAGE')]"
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
