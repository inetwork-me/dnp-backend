@extends('backend.layouts.app')

@section('title')
    {{ translate('Products Categories') }}
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 col-lg-6 col-12">
            <div class="card statistcs_card admin_statistcs m-0 mb-3">
                <div class="card-header">
                    <span>{{ translate('Search Categories') }}</span>
                </div>
                <div class="card-body d-block">
                    <form class="" id="sort_categories" action="" method="GET">
                        <input type="text" class="form-control" id="search"
                                    name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset
                                    placeholder="{{ translate('Type name & Enter') }}">
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-xl-12 col-lg-6 col-12">
            <div class="card statistcs_card admin_statistcs m-0 mb-3">
                <div class="card-header">
                    <span>{{ translate('All Categories') }}</span>
                    <div class="gap-10">
                        <a href="{{ route('categories.create') }}" class="btn btn-primary">
                            <span>{{ translate('Add New category') }}</span>
                        </a>
                    </div>
                </div>
                <div class="card-body d-block">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th data-breakpoints="lg">#</th>
                                    <th>{{ translate('Name') }}</th>
                                    <th data-breakpoints="lg">{{ translate('Parent Category') }}</th>
                                    <th data-breakpoints="lg">{{ translate('Order Level') }}</th>
                                    <th data-breakpoints="lg">{{ translate('Level') }}</th>
                                    <th data-breakpoints="lg">{{ translate('Banner') }}</th>
                                    <th data-breakpoints="lg">{{ translate('Icon') }}</th>
                                    <th data-breakpoints="lg">{{ translate('Cover Image') }}</th>
                                    <th data-breakpoints="lg">{{ translate('Featured') }}</th>
                                    @if (get_setting('seller_commission_type') == 'category_based')
                                        <th data-breakpoints="lg">{{ translate('Commission') }}</th>
                                    @endif
                                    <th width="10%" class="text-right">{{ translate('Options') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($categories as $key => $category)
                                    <tr>
                                        <td>{{ $key + 1 + ($categories->currentPage() - 1) * $categories->perPage() }}</td>
                                        <td class="d-flex align-items-center">
                                            {{ $category->getTranslation('name') }}
                                            @if ($category->digital == 1)
                                                <img src="{{ static_asset('assets/img/digital_tag.png') }}"
                                                    alt="{{ translate('Digital') }}" class="ml-2 h-25px"
                                                    style="cursor: pointer;" title="DIgital">
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $parent = \App\Models\Category::where(
                                                    'id',
                                                    $category->parent_id,
                                                )->first();
                                            @endphp
                                            @if ($parent != null)
                                                {{ $parent->getTranslation('name') }}
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td>{{ $category->order_level }}</td>
                                        <td>{{ $category->level }}</td>
                                        <td>
                                            @if ($category->banner != null)
                                                <img src="{{ uploaded_asset($category->banner) }}"
                                                    alt="{{ translate('Banner') }}" class="h-50px">
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td>
                                            @if ($category->icon != null)
                                                <span class="avatar avatar-square avatar-xs">
                                                    <img src="{{ uploaded_asset($category->icon) }}"
                                                        alt="{{ translate('icon') }}">
                                                </span>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td>
                                            @if ($category->cover_image != null)
                                                <img src="{{ uploaded_asset($category->cover_image) }}"
                                                    alt="{{ translate('Cover Image') }}" class="h-50px">
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td>
                                            <label class="aiz-switch aiz-switch-success mb-0">
                                                <input type="checkbox" onchange="update_featured(this)"
                                                    value="{{ $category->id }}" <?php if ($category->featured == 1) {
                                                        echo 'checked';
                                                    } ?>>
                                                <span></span>
                                            </label>
                                        </td>
                                        @if (get_setting('seller_commission_type') == 'category_based')
                                            <td>{{ $category->commision_rate }} %</td>
                                        @endif
                                        <td class="text-right">
                                            @can('edit_product_category')
                                                <a class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                                                    href="{{ route('categories.edit', ['id' => $category->id, 'lang' => env('DEFAULT_LANGUAGE')]) }}"
                                                    title="{{ translate('Edit') }}">
                                                    <i class="las la-edit"></i>
                                                </a>
                                            @endcan
                                            @can('delete_product_category')
                                                <a href="#"
                                                    class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete"
                                                    data-href="{{ route('categories.destroy', $category->id) }}"
                                                    title="{{ translate('Delete') }}">
                                                    <i class="las la-trash"></i>
                                                </a>
                                            @endcan
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
            </div>
        </div>
    </div>
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
