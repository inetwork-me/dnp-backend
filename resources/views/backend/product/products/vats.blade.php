@extends('backend.layouts.app')

@section('title')
    {{ translate('Product Vat and Tax') }}
@endsection

@section('content')

<x-dynamic-form 
    :action="route('vats.update.products')" 
    method="POST" 
    title="{{ __('VAT and TAX Settings') }}"
    :submitLabel="__('Save')"
    :fields="[
        [
            'label' => __('VAT & TAX'), 
            'name' => 'vat', 
            'type' => 'text', 
            'value' => $vat, 
            'required' => true
        ],
        [
            'label' => __('Tax Type'), 
            'name' => 'tax_type', 
            'type' => 'select', 
            'options' => [
                ['value' => 'amount', 'label' => __('Flat')],
                ['value' => 'percent', 'label' => __('Percent')]
            ], 
            'value' => $tax_type
        ]
    ]"
/>

@endsection
