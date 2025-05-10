@extends('backend.layouts.app')

@section('title')
    {{ translate('Create Currency') }}
@endsection

@section('content')
    <x-dynamic-form :action="route('currency.store')" cancelRoute="currency.index" method="POST"  title="Create Currency" :submitLabel="'Save'" :fields="[
        [
            'label' => 'Name',
            'name' => 'name',
            'type' => 'text',
            'required' => true,
        ],
        [
            'label' => 'Symbol',
            'name' => 'symbol',
            'type' => 'text',
            'required' => true,
        ],
        [
            'label' => 'Code',
            'name' => 'code',
            'type' => 'text',
            'required' => true,
        ],
        [
            'label' => 'Icon',
            'name' => 'icon',
            'type' => 'image',
            'required' => true,
        ],
        [
            'label' => 'Exchange Rate (U.S)',
            'name' => 'exchange_rate',
            'type' => 'number',
            'required' => true,
            'options' => [
                'step'=>'0.01',
                'min'=>'0'
            ]
        ],
    ]" />
@endsection


@section('script')
@endsection