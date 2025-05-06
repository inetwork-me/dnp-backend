@extends('backend.layouts.app')

@section('title')
{{ translate('All Dashboard Users') }}
@endsection

@section('content')

@php
$customRenderers = [
    'name' => fn($item) => $item->user->name,
    'email' => fn($item) => $item->user->email,
    'phone' => fn($item) => $item->user->phone,
    'role' => fn($item) => $item->role ? $item->role->getTranslation('name') : '—',
];
@endphp

<x-list-table
    :tableHeaders="['Name', 'Email','Phone','Role']"
    :tableKeys="['name', 'email','phone','role']"
    :translatableKeys="[]"
    :tableData="$staffs"
    title="All Dashboard Users"
    addRoute="staffs.create"
    showRoute="#"
    editRoute="staffs.edit"
    deleteRoute="staffs.destroy"
    :editParams="fn($item) => ['staff' => encrypt($item->id)]"
    :deleteParams="fn($item) => [$item->id]"
    :showParams="fn($item) => [$item->id]"
	:permissions="[
        'show' => '',
        'edit' => 'edit_staff',
        'delete' => 'delete_staff'
    ]"
    :customRenderers="$customRenderers"
/>

@endsection

@section('modal')
@include('backend.layouts.components.modals.delete_modal')
@endsection
