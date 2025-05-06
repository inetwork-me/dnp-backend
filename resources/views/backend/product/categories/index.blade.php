@extends('backend.layouts.app')

@section('title')
    {{ translate('Products Categories') }}
@endsection

@section('content')
@php
$customRenderers = [
    'parent_category' => fn($item) => \App\Models\Category::where('id', $item->parent_id)->first()?->getTranslation('name') ?? '—',
    'icon' => fn($item) => $item->icon ? '<img src="' . uploaded_asset($item->icon) . '" class="h-30px">' : '—',
    'featured' => fn($item) => '<label class="aiz-switch aiz-switch-success mb-0">
                                    <input type="checkbox" onchange="update_featured(this)" value="' . $item->id . '"' . ($item->featured ? ' checked' : '') . '>
                                    <span></span>
                                </label>',
];
@endphp

<x-list-table
    :tableHeaders="['Name', 'Parent Category', 'Order', 'Icon', 'Featured']"
    :tableKeys="['name', 'parent_category', 'order_level', 'icon', 'featured']"
    :translatableKeys="['name']"
    :tableData="$categories"
    title="All Categories"
    addRoute="categories.create"
    showRoute="#"
    editRoute="categories.edit"
    deleteRoute="categories.destroy"
    :editParams="fn($item) => ['id' => $item->id, 'lang' => env('DEFAULT_LANGUAGE')]"
    :deleteParams="fn($item) => [$item->id]"
    :showParams="fn($item) => [$item->id]"
    :customRenderers="$customRenderers"
    :permissions="[
        'show' => '',
        'edit' => 'edit_product_category',
        'delete' => 'delete_product_category'
    ]"
/>
@endsection


@section('modal')
    @include('backend.layouts.components.modals.delete_modal')
@endsection


@section('script')
    <script type="text/javascript">
        function update_featured(el) {

            if ('{{ env('DEMO_MODE') }}' == 'On') {
                showToastNotification('info', '{{ translate('Data can not change in demo mode.') }}');
                return;
            }

            if (el.checked) {
                var status = 1;
            } else {
                var status = 0;
            }
            $.post('{{ route('categories.featured') }}', {
                _token: '{{ csrf_token() }}',
                id: el.value,
                status: status
            }, function(data) {
                if (data == 1) {
                    showToastNotification('success',
                        '{{ translate('Featured categories updated successfully') }}');
                } else {
                    showToastNotification('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }
    </script>
@endsection
