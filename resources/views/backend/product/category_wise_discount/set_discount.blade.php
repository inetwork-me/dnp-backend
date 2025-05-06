@extends('backend.layouts.app')

@section('content')

@php
    //CoreComponentRepository::instantiateShopRepository();
    // CoreComponentRepository::initializeCache();
@endphp

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{translate('Set Category Wise Product Discount')}}</h1>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header d-block d-md-flex">
        <h5 class="mb-0 h6">{{ translate('Categories') }}</h5>
        <form class="" id="sort_categories" action="" method="GET">
            <div class="box-inline pad-rgt pull-left">
                <div class="" style="min-width: 200px;">
                    <input type="text" class="form-control" id="search" name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type name & Enter') }}">
                </div>
            </div>
        </form>
    </div>
    <div class="card-body">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th data-breakpoints="lg">#</th>
                    <th data-breakpoints="lg">{{translate('Icon')}}</th>
                    <th>{{translate('Name')}}</th>
                    <th data-breakpoints="lg">{{ translate('Parent Category') }}</th>
                    <th data-breakpoints="lg" width="15%">{{ translate('Discount') }}</th>
                    <th data-breakpoints="lg" width="20%">{{ translate('Discount Date Range') }}</th>
                    <th data-breakpoints="lg" class="text-center" width="10%">{{ translate('Seller Products?') }}</th>
                    <th data-breakpoints="lg" class="text-right">{{ translate('Action') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $key => $category)
                    <tr>
                        <td>{{ ($key+1) + ($categories->currentPage() - 1)*$categories->perPage() }}</td>
                        <td>
                            @if($category->icon != null)
                                <span class="avatar avatar-square avatar-xs">
                                    <img src="{{ uploaded_asset($category->icon) }}" alt="{{translate('icon')}}">
                                </span>
                            @else
                                —
                            @endif
                        </td>
                        <td class="align-items-center d-flex fw-800">
                            {{ $category->getTranslation('name') }}
                            @if($category->digital == 1)
                                <img src="{{ static_asset('assets/img/digital_tag.png') }}" alt="{{translate('Digital')}}" class="ml-2 h-25px" style="cursor: pointer;" title="DIgital">
                            @endif
                         </td>
                        <td class="fw-600">
                            @php
                                $parent = \App\Models\Category::where('id', $category->parent_id)->first();
                            @endphp
                            @if ($parent != null)
                                {{ $parent->getTranslation('name') }}
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            <div class="input-group">
                                <input type="number" class="form-control" id="discount_{{ $category->id }}" step="0.01" value="0" min="0" placeholder="{{translate('Discount')}}"
                                    style="border-radius: 8px 0 0 8px;">
                                <div class="input-group-prepend">
                                    <span class="input-group-text border-left-0" id="inputGroupPrepend" style="border-radius: 0 8px 8px 0;">%</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <input type="text" class="form-control aiz-date-range rounded-2" id="date_range_{{ $category->id }}" placeholder="{{translate('Select Date')}}" data-time-picker="true" data-format="DD-MM-Y HH:mm:ss" data-separator=" to " autocomplete="off">
                        </td>
                        <td class="text-center">
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input id="seller_product_discount_{{ $category->id }}" type="checkbox" >
                                <span class="slider round"></span>
                            </label>
                        </td>
                        <td class="text-right">
                            <div class="form-group mb-0 text-right">
                                <button type="button" onclick="trigger_alert({{ $category->id }})" class="btn btn-primary btn-sm rounded-2 w-120px">{{translate('Set')}}</button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="aiz-pagination">
            {{ $categories->appends(request()->input())->links() }}
        </div>
    </div>
</div>
@endsection

@section('modal')
    <!-- confirm Modal -->
    <div id="confirm-modal" class="modal fade">
        <div class="modal-dialog modal-md modal-dialog-centered" style="max-width: 540px;">
            <div class="modal-content p-2rem">
                <div class="modal-body text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="72" height="64" viewBox="0 0 72 64">
                        <g id="Octicons" transform="translate(-0.14 -1.02)">
                          <g id="alert" transform="translate(0.14 1.02)">
                            <path id="Shape" d="M40.159,3.309a4.623,4.623,0,0,0-7.981,0L.759,58.153a4.54,4.54,0,0,0,0,4.578A4.718,4.718,0,0,0,4.75,65.02H67.587a4.476,4.476,0,0,0,3.945-2.289,4.773,4.773,0,0,0,.046-4.578Zm.6,52.555H31.582V46.708h9.173Zm0-13.734H31.582V23.818h9.173Z" transform="translate(-0.14 -1.02)" fill="#ffc700" fill-rule="evenodd"/>
                          </g>
                        </g>
                    </svg>
                    <p class="mt-3 mb-3 fs-16 fw-700">{{translate('Are you sure you want to set this discount?')}}</p>
                    <div>
                        <button type="button" class="btn btn-light rounded-2 mt-2 fs-13 fw-700 w-150px" data-dismiss="modal">{{ translate('Cancel') }}</button>
                        <a href="javascript:void(0)" id="trigger_btn" data-value="" class="btn btn-warning rounded-2 mt-2 fs-13 fw-700 w-250px" onclick="setDiscount()">{{translate('Confirm')}}</a>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- /.modal -->
@endsection

@section('script')
    <script type="text/javascript">

        $(document).ready(function() {
            setTimeout(() => {
                AIZ.plugins.dateRange();
            }, "2000");
        });

        function trigger_alert(CategoryId){
            $('#trigger_btn').attr('data-value', CategoryId);
            $('#confirm-modal').modal('show');
        }

        function setDiscount(){

            if('{{env('DEMO_MODE')}}' == 'On'){
                showToastNotification('info', '{{ translate('Data can not change in demo mode.') }}');
                $('#confirm-modal').modal('hide');
                return;
            }

            $('#confirm-modal').modal('hide');
            var CategoryId = $('#trigger_btn').attr('data-value');
            var discount =  $("#discount_" + CategoryId).val();
            var dateRange =  $("#date_range_" + CategoryId).val();
            var sellerProductDiscount =  $("#seller_product_discount_" + CategoryId).prop('checked') ? 1 : 0;

            if(discount < 0) {
                showToastNotification('danger', '{{ translate('Discount can not be less than 0') }}');
            }
            else{
                $.post('{{ route('set_product_discount') }}', {
                    _token:'{{ csrf_token() }}',
                    category_id:CategoryId,
                    discount:discount,
                    date_range:dateRange,
                    seller_product_discount:sellerProductDiscount
                }, function(data) {
                    if(data == 1){
                        showToastNotification('success', '{{ translate('Category Wise Product Discount Set Successfully') }}');
                    }
                    location.reload();
                }).fail(function() {
                    showToastNotification('danger', '{{ translate('Something went wrong') }}');
                });
            }
        }
    </script>
@endsection


