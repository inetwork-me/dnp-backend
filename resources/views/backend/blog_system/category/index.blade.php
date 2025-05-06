@extends('backend.layouts.app')

@section('title')
{{ translate('All Blogs & News Categories') }}
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
    title="All Blogs & News Categories"
    addRoute="blog-category.create"
    showRoute="#"
    editRoute="blog-category.edit"
    deleteRoute="blog-category.destroy"
    :editParams="fn($item) => [$item->id]"
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

