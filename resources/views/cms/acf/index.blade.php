@extends('backend.layouts.app')

@section('title')
{{ translate('Field Groups') }}
@endsection

@section('content')

@php
$customRenderers = [
    'fields_count' => fn($item) => count($item->customFields),
    'key' => fn($item) => '<svg width="20" height="20" viewBox="0 0 17 17" xmlns="http://www.w3.org/2000/svg"><path d="m14.811 6.299.707-.707-1.733-1.733.757-.753-.705-.709-8.146 8.107a3 3 0 0 0-1.703-.535c-1.654 0-3 1.346-3 3s1.346 3 3 3 3-1.346 3-3c0-.661-.222-1.268-.585-1.764l5.264-5.238 1.738 1.738.707-.707-1.737-1.736.701-.698zm-10.824 8.67c-1.103 0-2-.897-2-2s.897-2 2-2 2 .897 2 2-.897 2-2 2" fill="gray"/></svg> '.$item->key,
    'fields_location' => fn($item) => implode(', ', optional($item->locationRules())->pluck('value')->toArray() ?? [])
];

$othersActions = [
    'view_fields' => fn($item) => '<a class="table_btn"
            href="' . route('cms.acf.fields', ['id' => $item->id]) . '"
            title="' . translate('Fields') . '">
            <svg width="25" height="25" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M6.5 1a.5.5 0 0 0 0 1c.627 0 .957.2 1.156.478C7.878 2.79 8 3.288 8 4v7c0 .712-.122 1.21-.344 1.522-.199.278-.53.478-1.156.478a.5.5 0 0 0 0 1c.873 0 1.543-.3 1.97-.897l.03-.044.03.044c.427.597 1.097.897 1.97.897a.5.5 0 0 0 0-1c-.627 0-.957-.2-1.156-.478C9.122 12.21 9 11.712 9 11V4c0-.712.122-1.21.344-1.522C9.543 2.2 9.874 2 10.5 2a.5.5 0 0 0 0-1c-.873 0-1.543.3-1.97.897l-.03.044-.03-.044C8.042 1.3 7.372 1 6.5 1M14 5h-3V4h3a1 1 0 0 1 1 1v5a1 1 0 0 1-1 1h-3v-1h3zM6 4v1H1v5h5v1H1a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1z" fill="#37143e"/></svg>
        </a>'
];
@endphp

<x-list-table
    :tableHeaders="['Title','Description','Key','Location', 'Fields']"
    :tableKeys="['title','description','key','fields_location', 'fields_count']"
    :translatableKeys="[]"
    :tableData="$fieldGroups"
    title="All Field Groups"
    addRoute="cms.acf.create"
    showRoute="#"
    editRoute="cms.acf.edit"
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
    :othersActions="$othersActions"
/>
@endsection

@section('modal')
    @include('backend.layouts.components.modals.delete_modal')
@endsection
