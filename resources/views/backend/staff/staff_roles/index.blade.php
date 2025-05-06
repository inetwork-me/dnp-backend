@extends('backend.layouts.app')

@section('title')
{{ translate('All Roles') }}
@endsection

@section('content')

@php
$customRenderers = [];
@endphp

<x-list-table
    :tableHeaders="['Name']"
    :tableKeys="['name']"
    :translatableKeys="[]"
    :tableData="$roles"
    title="All Roles"
    addRoute="roles.create"
    showRoute="#"
    editRoute="roles.edit"
    deleteRoute="roles.destroy"
    :editParams="fn($item) => ['id' => $item->id, 'lang' => env('DEFAULT_LANGUAGE')]"
    :deleteParams="fn($item) => [$item->id]"
    :showParams="fn($item) => [$item->id]"
	:permissions="[
        'show' => '',
        'edit' => 'edit_staff_role',
        'delete' => 'delete_staff_role'
    ]"
    :customRenderers="$customRenderers"
/>

@endsection

@section('modal')
@include('backend.layouts.components.modals.delete_modal')
@endsection
