@extends('backend.layouts.app')

@section('title')
{{translate('New Recipe Category')}}
@endsection

@section('content')


<x-dynamic-form 
    title="New Recipe Category" 
    action="{{ route('recipe-category.store') }}" 
    method="POST" 
    submitLabel="Save"
    :fields="[
        [
            'name' => 'category_name',
            'type' => 'text',
            'label' => 'Name',
            'required' => true,
            'col' => 'col-md-12',
            'options' => [
                'id' => 'category_name',
                'placeholder' => translate('Name')
            ]
        ]
    ]"
/>
@endsection
