@extends('backend.layouts.app')

@section('title')
    {{ translate('Product Vat & Tax') }}
@endsection

@section('content')
<div class="row">
    <div class="col-xl-12 col-lg-6 col-12">
        <x-form-card title="{{ translate('Update VAT & TAX') }}">
            <form class="" id="sort_products" action="{{ route('vats.update.products') }}" method="POST">
                @csrf
                
                <div class="card-body row">
                    <div class="col-12 mt-3">
                        <label for="">{{ trans('VAT & TAX') }}</label>
                        <input type="text" value="{{ $vat }}" id="" class="form-control" name="vat">
                    </div>
                    <div class="col-12 mt-3">
                        <select class="form-control aiz-selectpicker" name="tax_type">
                            <option value="amount" @if($tax_type == 'amount') selected @endif>{{translate('Flat')}}</option>
                            <option value="percent" @if($tax_type == 'percent') selected @endif>{{translate('Percent')}}</option>
                        </select>
                    </div>
                    <div class="col-12 mt-3">
                        <input type="submit" value="{{ trans('Save') }}" class="btn btn-primary">
                    </div>
                </div>
        
            </form>
        </x-form-card>
    </div>
</div>
@endsection
