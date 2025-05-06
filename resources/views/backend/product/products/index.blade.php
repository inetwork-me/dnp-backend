@extends('backend.layouts.app')

@section('title')
    {{ translate('All products') }}
@endsection

@section('content')

@php
$customRenderers = [
    'added_by' => fn($item) => ($type == 'Seller' || $type == 'All') ? optional($item->user)->name : translate('Admin'),
    'thumbnail_img' => fn($item) => $item->icon ? '<img src="' . uploaded_asset($item->thumbnail_img) . '" class="size-50px img-fit">' : '—',
    'info' => fn($item) => '
        <strong>'.translate("Num of Sale").':</strong>'.$item->num_of_sale.'
        '.translate("times").'</br>
        <strong>'.translate("Base Price").':</strong>
        '.single_price($item->unit_price).'</br>
        <strong>'.translate("Rating").':</strong> '.$item->rating.' </br>
    ',
    'stock' => fn($item) => 
        $item->digital == 1
            ? '<span class="badge badge-inline badge-info">' . translate('Digital Product') . '</span>'
            : (function() use ($item) {
                $qty = 0;
                if ($item->variant_product) {
                    foreach ($item->stocks as $stock) {
                        $qty += $stock->qty;
                        echo $stock->variant . ' - ' . $stock->qty . '<br>';
                    }
                } else {
                    $qty = optional($item->stocks->first())->qty;
                    echo $qty;
                }
                return $qty <= $item->low_stock_quantity
                    ? '<span class="badge badge-inline badge-danger">' . translate('Low') . '</span>'
                    : '';
            })(),
    'today_deal' => fn($item) => '<label class="aiz-switch aiz-switch-success mb-0">
                                        <input onchange="update_todays_deal(this)" value="'.$item->id.'"
                                            type="checkbox" '.($item->todays_deal == 1 ? 'checked' : '').'>
                                        <span class="slider round"></span>
                                    </label>',

    'published' => fn($item) => '<label class="aiz-switch aiz-switch-success mb-0">
                                    <input type="checkbox" onchange="update_published(this)" value="' . $item->id . '"' . ($item->published ? ' checked' : '') . '>
                                    <span></span>
                                </label>',

    'approved' => fn($item) => (get_setting('product_approve_by_admin') == 1 && $type == 'Seller') 
        ? '<label class="aiz-switch aiz-switch-success mb-0">
                <input onchange="update_approved(this)" value="' . $item->id . '"
                    type="checkbox" ' . ($item->approved == 1 ? 'checked' : '') . '>
                <span class="slider round"></span>
           </label>'
        : '<span class="badge badge-inline badge-success">' . translate('Approved') . '</span>',
    'featured' => fn($item) => '<label class="aiz-switch aiz-switch-success mb-0">
                                    <input onchange="update_featured(this)" value="' . $item->id . '"
                                        type="checkbox" ' . ($item->featured == 1 ? 'checked' : '') . '>
                                    <span class="slider round"></span>
                                </label>',
    
];

$othersActions = [
    'product_duplicate' => fn($item) => auth()->user()->can('product_duplicate') ? 
        '<a class="table_btn"
            href="' . route('products.duplicate', ['id' => $item->id, 'type' => $type]) . '"
            title="' . translate('Duplicate') . '">
            <svg width="25" height="25" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M1 9.5A1.5 1.5 0 0 0 2.5 11H4v-1H2.5a.5.5 0 0 1-.5-.5v-7a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 .5.5V4H5.5A1.5 1.5 0 0 0 4 5.5v7A1.5 1.5 0 0 0 5.5 14h7a1.5 1.5 0 0 0 1.5-1.5v-7A1.5 1.5 0 0 0 12.5 4H11V2.5A1.5 1.5 0 0 0 9.5 1h-7A1.5 1.5 0 0 0 1 2.5zm4-4a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-.5.5h-7a.5.5 0 0 1-.5-.5z" fill="#37143e"/></svg>
        </a>' : ''
];


$editRoute = fn($item) => $type == 'Seller' ? 'products.seller.edit' : 'products.admin.edit';
@endphp

<x-list-table
    :tableHeaders="['Name', 'Added By', 'Info', 'Total Stock', 'Todays Deal','Publish Status','Approve Status','Featured']"
    :tableKeys="['name', 'added_by', 'info', 'stock', 'today_deal','published','approved','featured']"
    :translatableKeys="['name']"
    :tableData="$products"
    title="All Products"
    addRoute="products.create"
    showRoute="#"
    :editRoute="$editRoute"
    deleteRoute="products.destroy"
    :editParams="fn($item) => ['id' => $item->id, 'lang' => env('DEFAULT_LANGUAGE')]"
    :deleteParams="fn($item) => [$item->id]"
    :showParams="fn($item) => [$item->id]"
    :customRenderers="$customRenderers"
    :othersActions="$othersActions"
    :permissions="[
        'show' => '',
        'edit' => 'edit_product_category',
        'delete' => 'delete_product_category'
    ]"
/>

@endsection

@section('modal')
    <!-- Delete modal -->
    @include('backend.layouts.components.modals.delete_modal')
    <!-- Bulk Delete modal -->
    @include('backend.layouts.components.modals.bulk_delete_modal')
@endsection


@section('script')
    <script type="text/javascript">
        $(document).on("change", ".check-all", function() {
            if (this.checked) {
                // Iterate each checkbox
                $('.check-one:checkbox').each(function() {
                    this.checked = true;
                });
            } else {
                $('.check-one:checkbox').each(function() {
                    this.checked = false;
                });
            }

        });

        $(document).ready(function() {
            //$('#container').removeClass('mainnav-lg').addClass('mainnav-sm');
        });

        function update_todays_deal(el) {
            if (el.checked) {
                var status = 1;
            } else {
                var status = 0;
            }
            $.post('{{ route('products.todays_deal') }}', {
                _token: '{{ csrf_token() }}',
                id: el.value,
                status: status
            }, function(data) {
                if (data == 1) {
                    AIZ.plugins.notify('success', '{{ translate('Todays Deal updated successfully') }}');
                } else {
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        function update_published(el) {
            if (el.checked) {
                var status = 1;
            } else {
                var status = 0;
            }
            $.post('{{ route('products.published') }}', {
                _token: '{{ csrf_token() }}',
                id: el.value,
                status: status
            }, function(data) {
                if (data == 1) {
                    AIZ.plugins.notify('success', '{{ translate('Published products updated successfully') }}');
                } else {
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        function update_approved(el) {
            if (el.checked) {
                var approved = 1;
            } else {
                var approved = 0;
            }
            $.post('{{ route('products.approved') }}', {
                _token: '{{ csrf_token() }}',
                id: el.value,
                approved: approved
            }, function(data) {
                if (data == 1) {
                    AIZ.plugins.notify('success', '{{ translate('Product approval update successfully') }}');
                } else {
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        function update_featured(el) {
            if (el.checked) {
                var status = 1;
            } else {
                var status = 0;
            }
            $.post('{{ route('products.featured') }}', {
                _token: '{{ csrf_token() }}',
                id: el.value,
                status: status
            }, function(data) {
                if (data == 1) {
                    AIZ.plugins.notify('success', '{{ translate('Featured products updated successfully') }}');
                } else {
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        function sort_products(el) {
            $('#sort_products').submit();
        }

        function bulk_delete() {
            var data = new FormData($('#sort_products')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('bulk-product-delete') }}",
                type: 'POST',
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response == 1) {
                        location.reload();
                    }
                }
            });
        }
    </script>
@endsection
