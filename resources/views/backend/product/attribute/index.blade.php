@extends('backend.layouts.app')

@section('title')
{{ translate('All Attributes') }}
@endsection

@section('content')

{{-- <x-list-table
    :tableHeaders="['Name', 'Values']"
    :tableKeys="['name', 'attribute_values']"
    :translatableKeys="['name']"
    :tableData="$attributes"
    title="All Attributes"
    addRoute="#"
    showRoute="#"
    editRoute="brands.edit"
    deleteRoute="brands.destroy"
    :editParams="fn($item) => ['id' => $item->id, 'lang' => env('DEFAULT_LANGUAGE')]"
    :deleteParams="fn($item) => [$item->id]"
    :showParams="fn($item) => [$item->id]"
    :customRenderers="[
    'attribute_values' => fn($item) => $item->attribute_values->map(fn($val) =>
        "<span class='badge badge-inline badge-md bg-primary'>{$val->value}</span>"
    )->implode(' ')
]"

/> --}}

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{ translate('Add New Attribute') }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('attributes.store') }}" method="POST">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="name">{{ translate('Name') }}</label>
                        <input type="text" placeholder="{{ translate('Name') }}" id="name" name="name"
                            class="form-control" required>
                    </div>
                    <div class="form-group mb-3 text-right">
                        <button type="submit" class="btn btn-primary">{{ translate('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<hr>

@php
$customRenderers = [
    'attribute_values' => fn($item) => collect($item->attribute_values)
        ->map(fn($v) => '<span class="badge badge-inline badge-md bg-primary">' . e($v->value) . '</span>')
        ->implode(' ')
];
@endphp

<x-list-table
    :tableHeaders="['Name', 'Values']"
    :tableKeys="['name', 'attribute_values']"
    :translatableKeys="['name']"
    :tableData="$attributes"
    title="All Attributes"
    addRoute="attributes.create"
    showRoute="attributes.show"
    editRoute="attributes.edit"
    deleteRoute="attributes.destroy"
    :editParams="fn($item) => ['id' => $item->id, 'lang' => env('DEFAULT_LANGUAGE')]"
    :deleteParams="fn($item) => [$item->id]"
    :showParams="fn($item) => [$item->id]"
    :customRenderers="$customRenderers"
    :permissions="[
        'show' => 'view_product_attribute_values',
        'edit' => 'edit_product_attribute',
        'delete' => 'delete_product_attribute'
    ]"
/>


    
@endsection

@section('modal')
    @include('backend.layouts.components.modals.delete_modal')
@endsection
