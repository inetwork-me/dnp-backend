@extends('backend.layouts.app')

@section('content')

@php
    CoreComponentRepository::instantiateShopRepository();
    CoreComponentRepository::initializeCache();
@endphp

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-auto">
            <h1 class="h3">{{translate('Update Commission')}}</h1>
        </div>
    </div>
</div>
<br>

<div class="card">
    <form class="" id="sort_products" action="{{ route('commission.update.products') }}" method="POST">
        @csrf
        <div class="card-header row gutters-5">
            <div class="col-12">
                <h5 class="mb-md-0 h6">{{ translate('Update Commission') }}</h5>
            </div>
        </div>
        
        <div class="card-body row">
            <div class="col-12 mt-3">
                <label for="">{{ trans('Commission') }}</label>
                <input type="text" value="{{ $commission }}" id="" class="form-control" name="commission">
            </div>
            <div class="col-12 mt-3">
                <select class="form-control aiz-selectpicker" name="commission_type">
                    <option value="amount" @if($commission_type == 'amount') selected @endif>{{translate('Flat')}}</option>
                    <option value="percent" @if($commission_type == 'percent') selected @endif>{{translate('Percent')}}</option>
                </select>
            </div>
            <div class="col-12 mt-3">
                <input type="submit" value="{{ trans('Save') }}" class="btn btn-primary">
            </div>
        </div>

    </form>
</div>

@endsection
