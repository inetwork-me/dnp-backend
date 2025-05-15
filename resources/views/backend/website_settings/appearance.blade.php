@extends('backend.layouts.app')

@section('title')
    {{ translate('Dashboard Settings') }}
@endsection

@section('css')
    <style>
        div#v-pills-tab {
            padding: 0 20px 10px 20px;
        }

        .nav-pills button.nav-link {
            background: unset;
            border: 0;
            font-size: 16px;
            font-weight: 400;
            color: #111827 !important;
            padding: 10px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .nav-pills button.nav-link.active {
            background: #712a7f;
            border: 0;
            font-size: 16px;
            font-weight: 400;
            color: white !important;
            padding: 10px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .settings_card .card-header {
            border-bottom: 1px solid #e9e9e9 !important;
            padding: .75rem 1.25rem !important;
            margin-bottom: 10px !important
        }

        .setting_side_nav {
            border-right: 1px solid #e9e9e9;
        }

        div#v-pills-tabContent {
            padding: 0 15px 0 0;
        }
    </style>
@endsection

@section('content')
    <div class="card settings_card table-card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center w-100">
                <h5 class="card-title mb-0">{{ translate('Dashboard Settings') }}</h5>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-2 setting_side_nav">
                    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <button class="nav-link active" id="v-pills-general-settings-tab" data-toggle="pill"
                            data-target="#v-pills-general-settings" type="button" role="tab"
                            aria-controls="v-pills-general-settings" aria-selected="true">
                            <i class="fa-solid fa-gear"></i>
                            <span>{{ translate('General Settings') }}</span>
                            <i class="fa-solid fa-chevron-right"></i>
                        </button>
                        <button class="nav-link" id="v-pills-profile-tab" data-toggle="pill" data-target="#v-pills-profile"
                            type="button" role="tab" aria-controls="v-pills-profile"
                            aria-selected="false">Profile</button>
                        <button class="nav-link" id="v-pills-messages-tab" data-toggle="pill"
                            data-target="#v-pills-messages" type="button" role="tab" aria-controls="v-pills-messages"
                            aria-selected="false">Messages</button>
                        <button class="nav-link" id="v-pills-settings-tab" data-toggle="pill"
                            data-target="#v-pills-settings" type="button" role="tab" aria-controls="v-pills-settings"
                            aria-selected="false">Settings</button>
                    </div>
                </div>
                <div class="col-10">
                    <div class="tab-content" id="v-pills-tabContent">
                        <div class="tab-pane fade show active" id="v-pills-general-settings" role="tabpanel"
                            aria-labelledby="v-pills-general-settings-tab">

                            <form class="form-horizontal" action="{{ route('business_settings.update') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <!-- System Name -->
                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <input type="hidden" name="types[]" value="site_name">
                                        <input type="text" name="site_name" class="form-control"
                                            placeholder="{{ translate('Dashboard Name') }}"
                                            value="{{ get_setting('site_name') }}">
                                    </div>
                                </div>
                                <!-- Frontend Website Name -->
                                <div class="form-group row">
                                    <label class="col-md-3 col-from-label">{{ translate('Frontend Website Name') }}</label>
                                    <div class="col-md-8">
                                        <input type="hidden" name="types[]" value="website_name">
                                        <input type="text" name="website_name" class="form-control"
                                            placeholder="{{ translate('Frontend Website Name') }}"
                                            value="{{ get_setting('website_name') }}">
                                    </div>
                                </div>
								
                                <!-- Site Icon -->
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <div class="input-group " data-toggle="aizuploader" data-type="image">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text bg-soft-secondary">{{ translate('Site Icon') }}
                                                </div>
                                            </div>
                                            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                            <input type="hidden" name="types[]" value="site_icon">
                                            <input type="hidden" name="site_icon"
                                                value="{{ get_setting('site_icon') }}" class="selected-files">
                                        </div>
                                        <div class="file-preview box"></div>
                                        <small class="text-muted">{{ translate('Website favicon. 32x32 .png') }}</small>
                                    </div>
                                </div>
                                <!-- System Logo - White -->
                                <div class="form-group row">
                                    <div class="col-sm-12">
                                        <div class="input-group" data-toggle="aizuploader" data-type="image">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text bg-soft-secondary">{{ translate('Browse') }}
                                                </div>
                                            </div>
                                            <div class="form-control file-amount">{{ translate('Choose Files') }}</div>
                                            <input type="hidden" name="types[]" value="system_logo_white">
                                            <input type="hidden" name="system_logo_white"
                                                value="{{ get_setting('system_logo_white') }}" class="selected-files">
                                        </div>
                                        <div class="file-preview box sm"></div>
                                        <small>{{ translate('Will be used in admin panel side menu') }}</small>
                                    </div>
                                </div>
                                <!-- System Logo - Black -->
                                <div class="form-group row">
                                    <div class="col-sm-9">
                                        <div class="input-group" data-toggle="aizuploader" data-type="image">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text bg-soft-secondary">{{ translate('Browse') }}
                                                </div>
                                            </div>
                                            <div class="form-control file-amount">{{ translate('System Logo - Black') }}</div>
                                            <input type="hidden" name="types[]" value="system_logo_black">
                                            <input type="hidden" name="system_logo_black"
                                                value="{{ get_setting('system_logo_black') }}" class="selected-files">
                                        </div>
                                        <div class="file-preview box sm"></div>
                                        <small>{{ translate('Will be used in Admin login page, Seller login page & Delivery Boy login page') }}</small>
                                    </div>
                                </div>
                                <!-- System Timezone -->
                                <div class="form-group row">
                                    <div class="col-sm-9">
                                        <input type="hidden" name="types[]" value="timezone">
                                        <select name="timezone" class="form-control aiz-selectpicker"
                                            data-live-search="true">
											<option value="">{{ translate('Select System Timezone') }}</option>
                                            @foreach (timezones() as $key => $value)
                                                <option value="{{ $value }}"
                                                    @if (app_timezone() == $value) selected @endif>{{ $key }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <!-- Update Button -->
                                <div class="mt-4 text-right">
                                    <button type="submit"
                                        class="btn btn-success w-230px btn-md rounded-2 fs-14 fw-700 shadow-success">{{ translate('Update') }}</button>
                                </div>
                            </form>

                        </div>
                        <div class="tab-pane fade" id="v-pills-profile" role="tabpanel"
                            aria-labelledby="v-pills-profile-tab">
                        </div>
                        <div class="tab-pane fade" id="v-pills-messages" role="tabpanel"
                            aria-labelledby="v-pills-messages-tab">...</div>
                        <div class="tab-pane fade" id="v-pills-settings" role="tabpanel"
                            aria-labelledby="v-pills-settings-tab">...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
