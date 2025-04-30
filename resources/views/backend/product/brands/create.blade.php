@extends('backend.layouts.app')

@section('title')
{{ translate('New Brand') }}
@endsection

@section('content')
<div class="row">
	<div class="col-xl-12 col-lg-6 col-12">
		<x-form-card title="{{ translate('Create New Brand') }}">
			<form action="{{ route('brands.store') }}" method="POST">
				@csrf
				<div class="form-group mb-3">
					<label for="name">{{translate('Name')}}</label>
					<input type="text" placeholder="{{translate('Name')}}" name="name" class="form-control" required>
				</div>
				<div class="form-group mb-3">
					<label for="name">{{translate('Logo')}} <small>({{ translate('120x80') }})</small></label>
					<div class="input-group" data-toggle="aizuploader" data-type="image">
						<div class="input-group-prepend">
								<div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
						</div>
						<div class="form-control file-amount">{{ translate('Choose File') }}</div>
						<input type="hidden" name="logo" class="selected-files">
					</div>
					<div class="file-preview box sm">
					</div>
					<small class="text-muted">{{ translate('Minimum dimensions required: 126px width X 100px height.') }}</small>
				</div>
				<div class="form-group mb-3">
					<label for="name">{{translate('Meta Title')}}</label>
					<input type="text" class="form-control" name="meta_title" placeholder="{{translate('Meta Title')}}">
				</div>
				<div class="form-group mb-3">
					<label for="name">{{translate('Meta Description')}}</label>
					<textarea name="meta_description" rows="5" class="form-control"></textarea>
				</div>
				<div class="form-group mb-3 text-right">
					<button type="submit" class="btn btn-primary">{{translate('Save')}}</button>
				</div>
			</form>
		</x-form-card>
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
