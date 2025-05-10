@extends('backend.layouts.app')
@section('title')
{{ translate('Countries') }}
@endsection
@section('content')

<x-dynamic-form 
    :action="route('countries.update',$country->id)" 
    method="PUT" 
    title="New Country"
    :submitLabel="'Update'"
    cancelRoute="countries.index"
    :fields="[
        ['label' => 'Code', 'name' => 'code', 'type' => 'text', 'required' => true , 'value'=>$country->code],
        ['label' => 'Name', 'name' => 'name', 'type' => 'text', 'required' => true , 'value'=>$country->name],
    ]"
/>

@endsection

@section('script')
    
@endsection
