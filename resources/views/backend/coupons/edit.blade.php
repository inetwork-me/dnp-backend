@extends('backend.layouts.app')

@section('title')
{{ translate('Update Coupon') }}
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12 col-lg-6 col-12">
        <x-form-card title="{{ translate('Update Coupon') }}">
            <form action="{{ route('coupons.update', $coupon->id) }}" method="POST">
                @csrf
                @method('PATCH')

                {{-- Code --}}
                <div class="form-group mb-3">
                    <label for="code">{{ translate('Code') }}</label>
                    <input type="text" name="code" id="code" class="form-control" value="{{ old('code', $coupon->code) }}" required>
                </div>

                {{-- Type --}}
                <div class="form-group mb-3">
                    <label for="type">{{ translate('Type') }}</label>
                    <select name="type" id="type" class="form-control" required>
                        <option value="percent" {{ old('type', $coupon->type)==='percent' ? 'selected' : '' }}>
                            {{ translate('Percent') }}
                        </option>
                        <option value="fixed" {{ old('type', $coupon->type)==='fixed' ? 'selected' : '' }}>
                            {{ translate('Fixed Amount') }}
                        </option>
                    </select>
                </div>

                {{-- Value --}}
                <div class="form-group mb-3">
                    <label for="value">{{ translate('Value') }}</label>
                    <input type="number" step="0.01" name="value" id="value" class="form-control" value="{{ old('value', $coupon->value) }}" required>
                </div>

                {{-- Uses per Customer --}}
                <div class="form-group mb-3">
                    <label for="usage_limit_per_customer">{{ translate('Uses per Customer') }}</label>
                    <input type="number" name="usage_limit_per_customer" id="usage_limit_per_customer" class="form-control" value="{{ old('usage_limit_per_customer', $coupon->usage_limit_per_customer) }}" min="1" required>
                </div>

                {{-- Global Cap --}}
                <div class="form-group mb-3">
                    <label for="usage_limit_global">{{ translate('Global Cap (optional)') }}</label>
                    <input type="number" name="usage_limit_global" id="usage_limit_global" class="form-control" value="{{ old('usage_limit_global', $coupon->usage_limit_global) }}" min="1">
                </div>

                {{-- Starts At --}}
                <div class="form-group mb-3">
                    <label for="starts_at">{{ translate('Starts At') }}</label>
                    <input type="datetime-local" name="starts_at" id="starts_at" class="form-control" value="{{ old('starts_at', optional($coupon->starts_at)->format('Y-m-d\TH:i')) }}">
                </div>

                {{-- Ends At --}}
                <div class="form-group mb-3">
                    <label for="ends_at">{{ translate('Ends At') }}</label>
                    <input type="datetime-local" name="ends_at" id="ends_at" class="form-control" value="{{ old('ends_at', optional($coupon->ends_at)->format('Y-m-d\TH:i')) }}">
                </div>

                {{-- Active --}}
                <div class="form-group mb-3 form-check">
                    <input type="checkbox" name="active" id="active" class="form-check-input" value="1" {{ old('active', $coupon->active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="active">
                        {{ translate('Active') }}
                    </label>
                </div>

                <div class="form-group text-right mb-0">
                    <button type="submit" class="btn btn-primary">
                        {{ translate('Save Changes') }}
                    </button>
                </div>
            </form>
        </x-form-card>
    </div>
</div>
@endsection