<div class="row mt-3">
    <div class="col-xl-12 col-lg-12 col-12">
        <form class="d-flex justify-content-between" id="sort_products" action="" method="GET">
            @can('product_delete')
                <div class="dropdown mb-2 mb-md-0">
                    <button class="btn border dropdown-toggle" type="button" data-toggle="dropdown">
                        {{ translate('Bulk Action') }}
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item confirm-alert" href="javascript:void(0)" data-target="#bulk-delete-modal">
                            {{ translate('Delete selection') }}</a>
                    </div>
                </div>
            @endcan

            <div class="d-flex justify-content-between gap-10">
                @if ($type == 'Seller')
                    <div class="w-100 ml-auto">
                        <select class="form-control  mb-2 mb-md-0" id="user_id" name="user_id"
                            onchange="sort_products()">
                            <option value="">{{ translate('All Sellers') }}</option>
                            @foreach (App\Models\User::where('user_type', '=', 'seller')->get() as $key => $seller)
                                <option value="{{ $seller->id }}" @if ($seller->id == $seller_id) selected @endif>
                                    {{ $seller->shop->name }} ({{ $seller->name }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif
                @if ($type == 'All' && get_setting('vendor_system_activation') == 1)
                    <div class="w-100 ml-auto">
                        <select class="form-control  mb-2 mb-md-0" id="user_id" name="user_id"
                            onchange="sort_products()">
                            <option value="">{{ translate('All Sellers') }}</option>
                            @foreach (App\Models\User::where('user_type', '=', 'admin')->orWhere('user_type', '=', 'seller')->get() as $key => $seller)
                                <option value="{{ $seller->id }}" @if ($seller->id == $seller_id) selected @endif>
                                    {{ $seller->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="w-100 ml-auto">
                    <select class="form-control  mb-2 mb-md-0" name="type" id="type" onchange="sort_products()">
                        <option value="">{{ translate('Sort By') }}</option>
                        <option value="rating,desc"
                            @isset($col_name, $query) @if ($col_name == 'rating' && $query == 'desc') selected @endif @endisset>
                            {{ translate('Rating (High > Low)') }}</option>
                        <option value="rating,asc"
                            @isset($col_name, $query) @if ($col_name == 'rating' && $query == 'asc') selected @endif @endisset>
                            {{ translate('Rating (Low > High)') }}</option>
                        <option
                            value="num_of_sale,desc"@isset($col_name, $query) @if ($col_name == 'num_of_sale' && $query == 'desc') selected @endif @endisset>
                            {{ translate('Num of Sale (High > Low)') }}</option>
                        <option
                            value="num_of_sale,asc"@isset($col_name, $query) @if ($col_name == 'num_of_sale' && $query == 'asc') selected @endif @endisset>
                            {{ translate('Num of Sale (Low > High)') }}</option>
                        <option
                            value="unit_price,desc"@isset($col_name, $query) @if ($col_name == 'unit_price' && $query == 'desc') selected @endif @endisset>
                            {{ translate('Base Price (High > Low)') }}</option>
                        <option
                            value="unit_price,asc"@isset($col_name, $query) @if ($col_name == 'unit_price' && $query == 'asc') selected @endif @endisset>
                            {{ translate('Base Price (Low > High)') }}</option>
                    </select>
                </div>
                <div class="w-100">
                    <div class="form-group mb-0">
                        <input type="text" class="form-control form-control-sm" id="search"
                            name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset
                            placeholder="{{ translate('Type & Enter') }}">
                    </div>
                </div>
            </div>


        </form>
    </div>
</div>