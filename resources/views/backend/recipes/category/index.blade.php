@extends('backend.layouts.app')

@section('title')
{{ translate('All Recipe Categories') }}
@endsection

@section('content')

@php
$customRenderers = [];
@endphp

<x-list-table
    :tableHeaders="['Title']"
    :tableKeys="['category_name']"
    :translatableKeys="[]"
    :tableData="$categories"
    title="All Recipe Categories"
    addRoute="recipe-category.create"
    showRoute="#"
    editRoute="recipe-category.edit"
    deleteRoute="recipe-category.destroy"
    :editParams="fn($item) => ['recipe_category' => $item->id, 'lang' => env('DEFAULT_LANGUAGE')]"
    :deleteParams="fn($item) => [$item->id]"
    :showParams="fn($item) => [$item->id]"
	:permissions="[
        'show' => '',
        'edit' => 'edit_blog_category',
        'delete' => 'delete_blog_category'
    ]"
    :customRenderers="$customRenderers"
/>

@endsection


@section('modal')
@include('backend.layouts.components.modals.delete_modal')
@endsection

