@extends('backend.layouts.app')

@section('title')
{{ translate('Post Types') }}
@endsection

@section('content')

@php
$customRenderers = [
    'publish' => fn($item) => '<label class="aiz-switch aiz-switch-success mb-0">
                                    <input type="checkbox" onchange="update_status(this)" value="' . $item->id . '"' . ($item->status ? ' checked' : '') . '>
                                    <span></span>
                                </label>',
    'icon' => fn($item) => '<img src="'.uploaded_asset($item->icon).'" />'
];
@endphp

<x-list-table
    :tableHeaders="['Name', 'Slug','Icon']"
    :tableKeys="['menu_name', 'slug' ,'icon']"
    :translatableKeys="[]"
    :tableData="$postTypes"
    title="All Post Types"
    addRoute="cms.cpt.create"
    showRoute="#"
    editRoute="cms.cpt.edit"
    deleteRoute="cms.cpt.destroy"
    :editParams="fn($item) => ['id' => $item->id, 'lang' => env('DEFAULT_LANGUAGE')]"
    :deleteParams="fn($item) => ['id' => $item->id]"
    :showParams="fn($item) => ['id' => $item->id]"
    :permissions="[
        'show' => 'show_post_types',
        'edit' => 'edit_post_types',
        'delete' => 'delete_post_types'
    ]"
    :customRenderers="$customRenderers"
/>
@endsection

@section('modal')
    @include('backend.layouts.components.modals.delete_modal')
@endsection
