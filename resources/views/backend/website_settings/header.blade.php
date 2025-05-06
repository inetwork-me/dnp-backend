@extends('backend.layouts.app')

@section('title')
    {{ translate('Website Header') }}
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header row gutters-5">
                    <div class="col text-center text-md-left">
                        <h5 class="mb-md-0 h6">{{ translate('Website Header') }}</h5>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('business_settings.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <!-- Header Logo -->
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{ translate('Header Logo') }}</label>
                            <div class="col-md-8">
                                <div class=" input-group " data-toggle="aizuploader" data-type="image">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text bg-soft-secondary font-weight-medium">
                                            {{ translate('Browse') }}</div>
                                    </div>
                                    <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                    <input type="hidden" name="types[]" value="header_logo">
                                    <input type="hidden" name="header_logo" class="selected-files"
                                        value="{{ get_setting('header_logo') }}">
                                </div>
                                <div class="file-preview"></div>
                            </div>
                        </div>
                        <!-- Show Language Switcher -->
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{ translate('Show Language Switcher?') }}</label>
                            <div class="col-md-8">
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input type="hidden" name="types[]" value="show_language_switcher">
                                    <input type="checkbox" name="show_language_switcher"
                                        @if (get_setting('show_language_switcher') == 'on') checked @endif>
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <!-- Show Currency Switcher -->
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{ translate('Show Currency Switcher?') }}</label>
                            <div class="col-md-8">
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input type="hidden" name="types[]" value="show_currency_switcher">
                                    <input type="checkbox" name="show_currency_switcher"
                                        @if (get_setting('show_currency_switcher') == 'on') checked @endif>
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <div class="border-top pt-3">
                            <!-- Help line number -->
                            <div class="form-group row">
                                <label class="col-md-3 col-from-label">{{ translate('Hotline number') }}</label>
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <input type="hidden" name="types[]" value="helpline_number">
                                        <input type="text" class="form-control"
                                            placeholder="{{ translate('Hotline number') }}" name="helpline_number"
                                            value="{{ get_setting('helpline_number') }}">
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
