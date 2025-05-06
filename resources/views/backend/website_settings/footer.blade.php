@extends('backend.layouts.app')

@section('title')
{{ translate('Website Footer') }}
@endsection

@section('content')
<div class="row">
   <div class="col-lg-12">
      <div class="card">
         <div class="card-header row gutters-5">
            <div class="col text-center text-md-left">
               <h5 class="mb-md-0 h6">{{ translate('Website Footer') }}</h5>
            </div>
         </div>
         <div class="card-body">
            <!-- Language -->
            <ul class="nav nav-tabs nav-fill language-bar">
               @foreach (get_all_active_language() as $key => $language)
               <li class="nav-item">
                  <a class="nav-link text-reset @if ($language->code == $lang) active @endif py-3"
                     href="{{ route('website.footer', ['lang' => $language->code]) }}">
                  <img src="{{ static_asset('assets/img/flags/' . $language->code . '.png') }}"
                     height="11" class="mr-1">
                  <span>{{ $language->name }}</span>
                  </a>
               </li>
               @endforeach
            </ul>
            <!-- Footer Widget -->
            <div class="card shadow-none bg-light">
				<div class="card-header">
				   <h6 class="mb-0">{{ translate('About Widget') }}</h6>
				</div>
				<div class="card-body">
				   <form action="{{ route('business_settings.update') }}" method="POST"
					  enctype="multipart/form-data">
					  @csrf
					  <!-- Footer Logo -->
					  <div class="form-group">
						 <label class="form-label"
							for="signinSrEmail">{{ translate('Footer Logo') }}</label>
						 <div class="input-group " data-toggle="aizuploader" data-type="image">
							<div class="input-group-prepend">
							   <div
								  class="input-group-text bg-soft-secondary font-weight-medium">
								  {{ translate('Browse') }}
							   </div>
							</div>
							<div class="form-control file-amount">{{ translate('Choose File') }}
							</div>
							<input type="hidden" name="types[]" value="footer_logo">
							<input type="hidden" name="footer_logo" class="selected-files"
							   value="{{ get_setting('footer_logo') }}">
						 </div>
						 <div class="file-preview"></div>
					  </div>
					  <!-- About description -->
					  <div class="form-group">
						 <label>{{ translate('About description') }}
						 ({{ translate('Translatable') }})</label>
						 <input type="hidden" name="types[][{{ $lang }}]"
							value="about_us_description">
						 <textarea class="aiz-text-editor form-control" name="about_us_description"
							data-buttons='[["font", ["bold", "underline", "italic"]],["para", ["ul", "ol"]],["view", ["undo","redo"]]]'
							placeholder="Type.." data-min-height="150">
						 {!! get_setting('about_us_description', null, $lang) !!}
						 </textarea>
					  </div>
					  <div class="mt-4 text-right">
						 <button type="submit"
							class="btn btn-success w-230px btn-md rounded-2 fs-14 fw-700 shadow-success">{{ translate('Update') }}</button>
					  </div>
				   </form>
				</div>
			 </div>
            <!-- Contact Info Widget -->
            <div class="card shadow-none bg-light">
				<div class="card-header">
				   <h6 class="mb-0">{{ translate('Contact Info Widget') }}</h6>
				</div>
				<div class="card-body">
				   <form action="{{ route('business_settings.update') }}" method="POST"
					  enctype="multipart/form-data">
					  @csrf
					  <!-- Contact address -->
					  <div class="form-group">
						 <label>{{ translate('Contact address') }}
						 ({{ translate('Translatable') }})</label>
						 <input type="hidden" name="types[][{{ $lang }}]"
							value="contact_address">
						 <input type="text" class="form-control"
							placeholder="{{ translate('Address') }}" name="contact_address"
							value="{{ get_setting('contact_address', null, $lang) }}">
					  </div>
					  <!-- Contact phone -->
					  <div class="form-group">
						 <label>{{ translate('Contact phone') }}</label>
						 <input type="hidden" name="types[]" value="contact_phone">
						 <input type="text" class="form-control"
							placeholder="{{ translate('Phone') }}" name="contact_phone"
							value="{{ get_setting('contact_phone') }}">
					  </div>
					  <!-- Contact email -->
					  <div class="form-group">
						 <label>{{ translate('Contact email') }}</label>
						 <input type="hidden" name="types[]" value="contact_email">
						 <input type="text" class="form-control"
							placeholder="{{ translate('Email') }}" name="contact_email"
							value="{{ get_setting('contact_email') }}">
					  </div>
					  <!-- Update Button -->
					  <div class="mt-4 text-right">
						 <button type="submit"
							class="btn btn-success w-230px btn-md rounded-2 fs-14 fw-700 shadow-success">{{ translate('Update') }}</button>
					  </div>
				   </form>
				</div>
			 </div>
            <form action="{{ route('business_settings.update') }}" method="POST"
               enctype="multipart/form-data">
               @csrf
               <div class="card shadow-none bg-light">
                  <div class="card-header">
                     <h6 class="mb-0">{{ translate('Copyright Widget ') }}</h6>
                  </div>
                  <div class="card-body">
                     <div class="form-group">
                        <label>{{ translate('Copyright Text') }}
                        ({{ translate('Translatable') }})</label>
                        <input type="hidden" name="types[][{{ $lang }}]"
                           value="frontend_copyright_text">
                        <textarea class="aiz-text-editor form-control" name="frontend_copyright_text"
                           data-buttons='[["font", ["bold", "underline", "italic"]],["insert", ["link"]],["view", ["undo","redo"]]]'
                           placeholder="Type.." data-min-height="150">
                        {!! get_setting('frontend_copyright_text', null, $lang) !!}
                        </textarea>
                     </div>
                  </div>
               </div>
               <!-- Payment Methods Widget -->
               <div class="card shadow-none bg-light">
                  <div class="card-header">
                     <h6 class="mb-0">{{ translate('Payment Methods Widget ') }}</h6>
                  </div>
                  <div class="card-body">
                     <div class="form-group">
                        <label>{{ translate('Payment Methods') }}</label>
                        <div class="input-group" data-toggle="aizuploader" data-type="image"
                           data-multiple="true">
                           <div class="input-group-prepend">
                              <div class="input-group-text bg-soft-secondary font-weight-medium">
                                 {{ translate('Browse') }}
                              </div>
                           </div>
                           <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                           <input type="hidden" name="types[]" value="payment_method_images">
                           <input type="hidden" name="payment_method_images" class="selected-files"
                              value="{{ get_setting('payment_method_images') }}">
                        </div>
                        <div class="file-preview box sm">
                        </div>
                     </div>
                  </div>
               </div>
               <!-- Update Button -->
               <div class="mt-4 text-right">
                  <button type="submit"
                     class="btn btn-success w-230px btn-md rounded-2 fs-14 fw-700 shadow-success">{{ translate('Update') }}</button>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>
@endsection