@extends('backend.layouts.app')

@section('title')
{{ translate('Fields') }}
@endsection

@section('content')

@php
$customRenderers = [
    'type' => fn($item) => '<i style="color: #a22cb9;border: 1px solid #c3c1c1;padding: 8px;font-size: 20px;border-radius: 50%;width: 40px;height: 40px;text-align: center;line-height: 1.6rem;" class="'.get_custom_field_by_value($item->type)['icon'].'"></i>&nbsp;'.translate(get_custom_field_by_value($item->type)['label']),
];
@endphp

<x-list-table
    :tableHeaders="['Title','Name','Type']"
    :tableKeys="['label','name','type']"
    :translatableKeys="[]"
    :tableData="$fieldGroup->customFields()->paginate(10)"
    title="Fields"
    addRoute="cms.acf.create"
    showRoute="#"
    editRoute="cms.acf.field.edit"
    deleteRoute="cms.acf.destroy"
    :editParams="fn($item) => ['id' => $item->id]"
    :deleteParams="fn($item) => ['id' => $item->id]"
    :showParams="fn($item) => ['id' => $item->id]"
    :permissions="[
        'show' => 'show_field_groups',
        'edit' => 'edit_field_groups',
        'delete' => 'delete_field_groups'
    ]"
    :customRenderers="$customRenderers"
/>
@endsection

@section('modal')
    @include('backend.layouts.components.modals.delete_modal')
@endsection
