@extends('backend.layouts.app')

@section('title')
{{ translate('Edit State') }}
@endsection

@section('content')

<x-dynamic-form 
    :action="route('states.update',$state->id)" 
    method="PUT" 
    title="Update State"
    :submitLabel="'Update'"
    cancelRoute="states.index"
    :fields="[
        ['label' => 'Name', 'name' => 'name', 'type' => 'text', 'required' => true ,'value'=>$state->name],
        ['label' => 'Country', 'name' => 'country_id', 'type' => 'select', 'required' => true,'value'=>$state->country_id, 'options' => \App\Models\Country::where('status', 1)->get()->map(fn($country) => ['value' => $country->id, 'label' => $country->name])->toArray()],
    ]"
/>
@endsection
