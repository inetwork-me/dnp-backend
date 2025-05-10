@extends('backend.layouts.app')

@section('title')
{{ translate('Cities') }}
@endsection

@section('content')
<x-dynamic-form 
    :action="route('cities.store')" 
    method="POST" 
    title="New City"
    :submitLabel="'Save'"
    cancelRoute="cities.index"
    :fields="[
        ['label' => 'Name', 'name' => 'name', 'type' => 'text', 'required' => true],
        ['label' => 'State', 'name' => 'state_id', 'type' => 'select', 'required' => true, 'options' => $states->map(fn($state) => ['value' => $state->id, 'label' => $state->name])->toArray()],
        ['label' => 'Cost', 'name' => 'cost', 'type' => 'number', 'required' => true, 'step' => '0.01', 'min' => 0]
    ]"
/>


@php
$customRenderers = [
    'publish' => fn($item) => '<label class="aiz-switch aiz-switch-success mb-0">
                                    <input type="checkbox" onchange="update_status(this)" value="' . $item->id . '"' . ($item->status ? ' checked' : '') . '>
                                    <span></span>
                                </label>',
    'state' => fn($item) => $item->state->name,
    'area' => fn($item) => single_price($item->cost),
];
@endphp

<x-list-table
    :tableHeaders="['Name', 'State','Area Wise Shipping Cost','Status']"
    :tableKeys="['name', 'state','area','publish']"
    :translatableKeys="[]"
    :tableData="$cities"
    title="All Cities"
    addRoute="#"
    showRoute="#"
    editRoute="cities.edit"
    deleteRoute="cities.destroy"
    :editParams="fn($item) => ['id' => $item->id, 'lang' => env('DEFAULT_LANGUAGE')]"
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
    @include('backend.layouts.components.modals.delete_modal')
@endsection


@section('script')
    <script type="text/javascript">
        function sort_cities(el){
            $('#sort_cities').submit();
        }

        function update_status(el){
            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('{{ route('cities.status') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '{{ translate('Country status updated successfully') }}');
                }
                else{
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

    </script>
@endsection
