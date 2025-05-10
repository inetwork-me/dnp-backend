@extends('backend.layouts.app')

@section('title')
{{ translate('Currency Configurations') }}
@endsection

@section('content')

<div class="row">
    <div class="col-6">
        <x-dynamic-form 
            :action="route('business_settings.update')" 
            method="POST" 
            title="Set Currency Formats"
            :submitLabel="'Save'"
            :fields="[
                [
                    'label' => 'System Default Currency', 
                    'name' => 'system_default_currency', 
                    'type' => 'select', 
                    'required' => true,
                    'value' => get_setting('system_default_currency'), 
                    'options' => $active_currencies->map(fn($currency) => [
                        'value' => $currency->id, 
                        'label' => $currency->name
                    ])->toArray(),
                    'col' => 'col-12'
                ],
                [
                    'type' => 'hidden',
                    'name' => 'types[]',
                    'value' => 'system_default_currency'
                ]
            ]"
        />
    </div>
    
    <div class="col-6">
        <x-dynamic-form
            title="Set Currency Format"
            :action="route('business_settings.update')"
            :fields="[
                [
                    'type' => 'select',
                    'name' => 'symbol_format',
                    'label' => 'Symbol Format',
                    'value' => get_setting('symbol_format'),
                    'options' => [
                        ['value' => 1, 'label' => '[Symbol][Amount]'],
                        ['value' => 2, 'label' => '[Amount][Symbol]'],
                        ['value' => 3, 'label' => '[Symbol] [Amount]'],
                        ['value' => 4, 'label' => '[Amount] [Symbol]'],
                    ],
                    'col' => 'col-12',
                ],
                [
                    'type' => 'select',
                    'name' => 'decimal_separator',
                    'label' => 'Decimal Separator',
                    'value' => get_setting('decimal_separator'),
                    'options' => [
                        ['value' => 1, 'label' => '1,23,456.70'],
                        ['value' => 2, 'label' => '1.23.456,70'],
                    ],
                    'col' => 'col-12',
                ],
                [
                    'type' => 'select',
                    'name' => 'no_of_decimals',
                    'label' => 'No of decimals',
                    'value' => get_setting('no_of_decimals'),
                    'options' => [
                        ['value' => 0, 'label' => '12345'],
                        ['value' => 1, 'label' => '1234.5'],
                        ['value' => 2, 'label' => '123.45'],
                        ['value' => 3, 'label' => '12.345'],
                    ],
                    'col' => 'col-12',
                ],
            ]"
        />

    </div>
</div>

@php
$customRenderers = [
    'publish' => fn($item) => '<label class="aiz-switch aiz-switch-success mb-0">
                                    <input type="checkbox" onchange="update_currency_status(this)" value="' . $item->id . '"' . ($item->status ? ' checked' : '') . '>
                                    <span></span>
                                </label>',
];
@endphp

<x-list-table
    :tableHeaders="['Name','Symbol','Code','Exchange Rate','Status']"
    :tableKeys="['name', 'symbol','code','exchange_rate','publish']"
    :translatableKeys="[]"
    :tableData="$currencies"
    title="All Currencies"
    addRoute="currency.create"
    showRoute="#"
    editRoute="currency.edit"
    deleteRoute="currency.destroy"
    :editParams="fn($item) => ['id' => $item->id]"
    :deleteParams="fn($item) => [$item->id]"
    :showParams="fn($item) => [$item->id]"
	:permissions="[
        'show' => '',
        'edit' => '',
        'delete' => ''
    ]"
	:customRenderers="$customRenderers"
/>

@endsection

@section('modal')

    <!-- Delete Modal -->
    @include('backend.layouts.components.modals.delete_modal')

    <div class="modal fade" id="add_currency_modal">
        <div class="modal-dialog">
            <div class="modal-content" id="modal-content">

            </div>
        </div>
    </div>

    <div class="modal fade" id="currency_modal_edit">
        <div class="modal-dialog">
            <div class="modal-content" id="modal-content">

            </div>
        </div>
    </div>

@endsection

@section('script')
    <script type="text/javascript">

        function sort_currencies(el){
            $('#sort_currencies').submit();
        }

        function update_currency_status(el){
            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }

            $.post('{{ route('currency.update_status') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '{{ translate('Currency Status updated successfully') }}');
                }
                else{
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }
    </script>
@endsection
