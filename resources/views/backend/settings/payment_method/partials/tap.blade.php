<form class="form-horizontal" action="{{ route('payment_method.update') }}" method="POST">
    @csrf
    <input type="hidden" name="payment_method" value="tap">
    <div class="form-group row">
        <input type="hidden" name="types[]" value="TAP_SECRET_KEY">
        <div class="col-md-4">
            <label class="col-from-label">{{ translate('Tap Secret Key') }}</label>
        </div>
        <div class="col-md-8">
            <input type="text" class="form-control" name="TAP_SECRET_KEY"
                value="{{ env('TAP_SECRET_KEY') }}" placeholder="{{ translate('TAP SECRET KEY') }}"
                required>
        </div>
    </div>
    {{-- <div class="form-group row">
        <input type="hidden" name="types[]" value="TAP_MERCHANT_ID">
        <div class="col-md-4">
            <label class="col-from-label">{{ translate('Tap Merchant ID') }}</label>
        </div>
        <div class="col-md-8">
            <input type="text" class="form-control" name="TAP_MERCHANT_ID"
                value="{{ env('TAP_MERCHANT_ID') }}" placeholder="{{ translate('TAP MERCHANT ID') }}"
                required>
        </div>
    </div> --}}
    <div class="form-group mb-0 text-right">
        <button type="submit" class="btn btn-sm btn-primary">{{ translate('Save') }}</button>
    </div>
</form>
