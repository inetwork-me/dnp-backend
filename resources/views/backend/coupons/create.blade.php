@extends('backend.layouts.app')

@section('title')
{{ translate('New Coupon') }}
@endsection

@section('content')
<div class="row">
	<div class="col-xl-12 col-lg-6 col-12">
		<x-form-card title="{{ translate('Create New Coupon') }}">
			<form action="{{ route('coupons.store') }}" method="POST">
				@csrf

				<div class="form-group mb-3">
					<label for="code">{{ translate('Code') }}</label>
					<input type="text" name="code" id="code" class="form-control" placeholder="{{ translate('e.g. WELCOME10') }}" value="{{ old('code') }}" required>
				</div>

				<div class="form-group mb-3">
					<label for="type">{{ translate('Type') }}</label>
					<select name="type" id="type" class="form-control" required>
						<option value="percent" {{ old('type')==='percent' ? 'selected' : '' }}>
							{{ translate('Percent') }}
						</option>
						<option value="fixed" {{ old('type')==='fixed' ? 'selected' : '' }}>
							{{ translate('Fixed Amount') }}
						</option>
					</select>
				</div>

				<div class="form-group mb-3">
					<label for="value">{{ translate('Value') }}</label>
					<input type="number" step="0.01" name="value" id="value" class="form-control" placeholder="{{ translate('Discount value') }}" value="{{ old('value') }}" required>
				</div>

				<div class="form-group mb-3">
					<label for="usage_limit_per_customer">{{ translate('Uses per Customer') }}</label>
					<input type="number" name="usage_limit_per_customer" id="usage_limit_per_customer" class="form-control" placeholder="1" value="{{ old('usage_limit_per_customer', 1) }}" min="1" required>
				</div>

				<div class="form-group mb-3">
					<label for="usage_limit_global">{{ translate('Global Cap (optional)') }}</label>
					<input type="number" name="usage_limit_global" id="usage_limit_global" class="form-control" placeholder="{{ translate('e.g. 100') }}" value="{{ old('usage_limit_global') }}" min="1">
				</div>

				<div class="form-group mb-3">
					<label for="starts_at">{{ translate('Starts At') }}</label>
					<input type="datetime-local" name="starts_at" id="starts_at" class="form-control" value="{{ old('starts_at') }}">
				</div>

				<div class="form-group mb-3">
					<label for="ends_at">{{ translate('Ends At') }}</label>
					<input type="datetime-local" name="ends_at" id="ends_at" class="form-control" value="{{ old('ends_at') }}">
				</div>

				<div class="form-group mb-3 form-check">
					<input type="checkbox" name="active" id="active" value="1" class="form-check-input" {{ old('active', 1) ? 'checked' : '' }}>
					<label class="form-check-label" for="active">
						{{ translate('Active') }}
					</label>
				</div>

				<div class="form-group mb-3 text-right">
					<button type="submit" class="btn btn-primary">
						{{ translate('Save Coupon') }}
					</button>
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
	// any coupon-specific JS can go here
</script>
@endsection