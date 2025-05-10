@extends('backend.layouts.app')

@section('title')
{{ translate('Update Zone') }}
@endsection

@section('content')
<x-dynamic-form 
    :action="route('zones.update', $zone->id)" 
    method="PUT" 
    title="Update Zone"
    :submitLabel="'Update'"
    cancelRoute="zones.index"
    :fields="[
        ['label' => 'Name', 'name' => 'name', 'type' => 'text', 'required' => true, 'value' => $zone->name],
        [
            'label' => 'States', 
            'name' => 'country_id', 
            'type' => 'select', 
            'required' => true, 
            'multiple' => true,
            'value' => \App\Models\Country::where('zone_id', $zone->id)->pluck('id')->toArray(),
            'options' => \App\Models\Country::where('status', 1)->get()->map(fn($country) => [
                'value' => $country->id, 
                'label' => $country->name
            ])->toArray()
        ],
    ]"
/>

@endsection
