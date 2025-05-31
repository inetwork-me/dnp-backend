@extends('backend.layouts.app')

@section('title')
{{ translate('Coupons') }}
@endsection

@section('content')

@php
$customRenderers = [
'code' => fn($item) => strtoupper($item->code),

'type' => fn($item) => ucfirst($item->type),

// number_format + “$” prefix instead of undefined currency()
'value' => fn($item) => $item->type === 'percent'
? $item->value.'%'
: '$'.number_format($item->value, 2),

'usage_limit_per_customer' => fn($item) => $item->usage_limit_per_customer,
'usage_limit_global' => fn($item) => $item->usage_limit_global ?: '—',

'starts_at' => fn($item) => optional($item->starts_at)->format('Y-m-d H:i') ?: '—',
'ends_at' => fn($item) => optional($item->ends_at)->format('Y-m-d H:i') ?: '—',

'active' => function($item) {
return $item->active
? '<span class="badge badge-success">'.translate('Active').'</span>'
: '<span class="badge badge-danger">'.translate('Inactive').'</span>';
},
];
@endphp

<x-list-table :tableHeaders="[
        'Code',
        'Type',
        'Value',
        translate('Uses/Customer'),
        translate('Global Cap'),
        translate('Starts At'),
        translate('Ends At'),
        translate('Status'),
    ]" :tableKeys="[
        'code',
        'type',
        'value',
        'usage_limit_per_customer',
        'usage_limit_global',
        'starts_at',
        'ends_at',
        'active',
    ]" :translatableKeys="[]" :tableData="$coupons" title="{{ translate('All Coupons') }}" addRoute="coupons.create" showRoute="#" editRoute="coupons.update" deleteRoute="coupons.destroy" :editParams="fn($item) => ['id' => $item->id]" :deleteParams="fn($item) => [$item->id]" :showParams="fn($item) => [$item->id]" :permissions="[
        'show'   => '',
        'edit'   => 'edit_coupon',
        'delete' => 'delete_coupon',
    ]" :customRenderers="$customRenderers" />

@endsection

@section('modal')
@include('backend.layouts.components.modals.delete_modal')
@endsection

@section('script')
<script>
    function sort_coupons(el) {
        $('#sort_coupons').submit();
    }
</script>
@endsection