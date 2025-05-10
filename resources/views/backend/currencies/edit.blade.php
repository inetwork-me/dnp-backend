@extends('backend.layouts.app')

@section('title')
    {{ translate('Create Currency') }}
@endsection

@section('content')
    <x-dynamic-form :action="route('currency.update',$currency->id)" cancelRoute="currency.index" method="PUT"  title="Update Currency" :submitLabel="'Update'" :fields="[
        [
            'label' => 'Name',
            'name' => 'name',
            'type' => 'text',
            'required' => true,
            'value' => $currency->name,
        ],
        [
            'label' => 'Symbol',
            'name' => 'symbol',
            'type' => 'text',
            'required' => true,
            'value' => $currency->symbol,
        ],
        [
            'label' => 'Code',
            'name' => 'code',
            'type' => 'text',
            'required' => true,
            'value' => $currency->code,
        ],
        [
            'label' => 'Exchange Rate (U.S)',
            'name' => 'exchange_rate',
            'type' => 'number',
            'required' => true,
            'value' => $currency->exchange_rate,
            'options' => [
                'step'=>'0.01',
                'min'=>'0'
            ]
        ],
        [
            'label' => 'Icon',
            'name' => 'icon',
            'type' => 'image',
            'required' => true,
            'has_image' => $currency->icon,
        ],
    ]" />
@endsection


@section('script')
@endsection