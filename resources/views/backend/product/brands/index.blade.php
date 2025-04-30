@extends('backend.layouts.app')

@section('title')
{{ translate('Brands') }}
@endsection

@section('content')

<div class="row">
	<div class="col-lg-12">
		<div class="card">
		    <div class="card-header row gutters-5">
				<div class="col text-center text-md-left">
					<h5 class="mb-md-0 h6">{{ translate('Brands') }}</h5>
				</div>
				<div class="col-md-4">
					<form class="" id="sort_brands" action="" method="GET">
						<div class="input-group input-group-sm">
					  		<input type="text" class="form-control" id="search" name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type name & Enter') }}">
						</div>
					</form>
				</div>
		    </div>
		    <div class="card-body">
		        <table class="table aiz-table mb-0">
		            <thead>
		                <tr>
		                    <th>#</th>
		                    <th>{{translate('Name')}}</th>
		                    <th>{{translate('Logo')}}</th>
		                    <th class="text-right">{{translate('Options')}}</th>
		                </tr>
		            </thead>
		            <tbody>
		                @foreach($brands as $key => $brand)
		                    <tr>
		                        <td>{{ ($key+1) + ($brands->currentPage() - 1)*$brands->perPage() }}</td>
		                        <td>{{ $brand->getTranslation('name') }}</td>
								<td>
		                            <img src="{{ uploaded_asset($brand->logo) }}" alt="{{translate('Brand')}}" class="h-50px">
		                        </td>
		                        <td class="text-right">
									@can('edit_brand')
										<a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('brands.edit', ['id'=>$brand->id, 'lang'=>env('DEFAULT_LANGUAGE')] )}}" title="{{ translate('Edit') }}">
											<i class="las la-edit"></i>
										</a>
									@endcan
									@can('delete_brand')
										<a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('brands.destroy', $brand->id)}}" title="{{ translate('Delete') }}">
											<i class="las la-trash"></i>
										</a>
									@endcan
		                        </td>
		                    </tr>
		                @endforeach
		            </tbody>
		        </table>
		        <div class="aiz-pagination">
                	{{ $brands->appends(request()->input())->links() }}
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
    function sort_brands(el){
        $('#sort_brands').submit();
    }
</script>
@endsection
