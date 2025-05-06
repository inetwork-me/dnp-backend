@extends('backend.layouts.app')

@section('title')
{{ translate('Brands') }}
@endsection

@section('content')

@php
$customRenderers = [];
@endphp

<x-list-table
    :tableHeaders="['Name', 'Logo']"
    :tableKeys="['name', 'logo']"
    :translatableKeys="['name']"
    :tableData="$brands"
    title="All Brands"
    addRoute="brands.create"
    showRoute="#"
    editRoute="brands.edit"
    deleteRoute="brands.destroy"
    :editParams="fn($item) => ['id' => $item->id, 'lang' => env('DEFAULT_LANGUAGE')]"
    :deleteParams="fn($item) => [$item->id]"
    :showParams="fn($item) => [$item->id]"
	:permissions="[
        'show' => '',
        'edit' => 'edit_brand',
        'delete' => 'delete_brand'
    ]"
	:customRenderers="$customRenderers"
/>

@endsection

@section('modal')
    @include('backend.layouts.components.modals.delete_modal')
@endsection

@section('script')
<script type="text/javascript">
    function sort_brands(el){
        $('#sort_brands').submit();
    }
</script>
@endsection
