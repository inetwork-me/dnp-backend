@extends('backend.layouts.app')

@section('title')
{{ translate('Dashboard Settings') }}
@endsection

@section('content')

<div class="row">
	<div class="col-lg-12">
	   <div class="card">
		  <div class="card-header row gutters-5">
			 <div class="col text-center text-md-left">
				<h5 class="mb-md-0 h6">{{ translate('Dashboard Settings') }}</h5>
			 </div>
		  </div>
		  <div class="card-body">
			<form class="form-horizontal" action="{{ route('business_settings.update') }}" method="POST" enctype="multipart/form-data">
				@csrf
				<!-- System Name -->
				<div class="form-group row">
					<label class="col-sm-3 col-from-label">{{translate('Dashboard Name')}}</label>
					<div class="col-sm-9">
						<input type="hidden" name="types[]" value="site_name">
						<input type="text" name="site_name" class="form-control" placeholder="{{ translate('Dashboard Name') }}" value="{{ get_setting('site_name') }}">
					</div>
				</div>
				<!-- Frontend Website Name -->
				<div class="form-group row">
					<label class="col-md-3 col-from-label">{{translate('Website Name')}}</label>
					<div class="col-md-8">
						<input type="hidden" name="types[]" value="website_name">
						<input type="text" name="website_name" class="form-control" placeholder="{{ translate('Website Name') }}" value="{{ get_setting('website_name') }}">
					</div>
				</div>
				
				<!-- Site Icon -->
				<div class="form-group row">
					<label class="col-md-3 col-from-label">{{ translate('Site Icon') }}</label>
					<div class="col-md-8">
						<div class="input-group " data-toggle="aizuploader" data-type="image">
							<div class="input-group-prepend">
								<div class="input-group-text bg-soft-secondary">{{ translate('Browse') }}</div>
							</div>
							<div class="form-control file-amount">{{ translate('Choose File') }}</div>
							<input type="hidden" name="types[]" value="site_icon">
							<input type="hidden" name="site_icon" value="{{ get_setting('site_icon') }}" class="selected-files">
						</div>
						<div class="file-preview box"></div>
						<small class="text-muted">{{ translate('Website favicon. 32x32 .png') }}</small>
					</div>
				</div>
				<!-- System Logo - White -->
				<div class="form-group row">
					<label class="col-sm-3 col-from-label">{{translate('System Logo - White')}}</label>
					<div class="col-sm-9">
						<div class="input-group" data-toggle="aizuploader" data-type="image">
							<div class="input-group-prepend">
								<div class="input-group-text bg-soft-secondary">{{ translate('Browse') }}</div>
							</div>
							<div class="form-control file-amount">{{ translate('Choose Files') }}</div>
							<input type="hidden" name="types[]" value="system_logo_white">
							<input type="hidden" name="system_logo_white" value="{{ get_setting('system_logo_white') }}" class="selected-files">
						</div>
						<div class="file-preview box sm"></div>
						<small>{{ translate('Will be used in admin panel side menu') }}</small>
					</div>
				</div>
				<!-- System Logo - Black -->
				<div class="form-group row">
					<label class="col-sm-3 col-from-label">{{translate('System Logo - Black')}}</label>
					<div class="col-sm-9">
						<div class="input-group" data-toggle="aizuploader" data-type="image">
							<div class="input-group-prepend">
								<div class="input-group-text bg-soft-secondary">{{ translate('Browse') }}</div>
							</div>
							<div class="form-control file-amount">{{ translate('Choose Files') }}</div>
							<input type="hidden" name="types[]" value="system_logo_black">
							<input type="hidden" name="system_logo_black" value="{{ get_setting('system_logo_black') }}" class="selected-files">
						</div>
						<div class="file-preview box sm"></div>
						<small>{{ translate('Will be used in Admin login page, Seller login page & Delivery Boy login page') }}</small>
					</div>
				</div>
				<!-- System Timezone -->
				<div class="form-group row">
					<label class="col-sm-3 col-from-label">{{translate('System Timezone')}}</label>
					<div class="col-sm-9">
						<input type="hidden" name="types[]" value="timezone">
						<select name="timezone" class="form-control aiz-selectpicker" data-live-search="true">
							@foreach (timezones() as $key => $value)
								<option value="{{ $value }}" @if (app_timezone() == $value)
									selected
								@endif>{{ $key }}</option>
							@endforeach
						</select>
					</div>
				</div>
				<!-- Update Button -->
				<div class="mt-4 text-right">
					<button type="submit" class="btn btn-success w-230px btn-md rounded-2 fs-14 fw-700 shadow-success">{{ translate('Update') }}</button>
				</div>
			</form>
		  </div>
		</div>
	</div>
</div>

@endsection
