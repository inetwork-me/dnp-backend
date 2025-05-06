<form class="form-horizontal" action="{{ route('payment_method.update') }}" method="POST">
    @csrf
    <input type="hidden" name="payment_method" value="paymob">
    <div class="form-group row">
        <input type="hidden" name="types[]" value="PAYMOB_API_KEY">
        <div class="col-lg-4">
            <label class="col-from-label">{{ translate('Paymob API Key') }}</label>
        </div>
        <div class="col-lg-8">
            <input type="text" class="form-control" name="PAYMOB_API_KEY"
                value="{{ env('PAYMOB_API_KEY') }}"
                placeholder="{{ translate('Paymob API Key') }}" required>
        </div>
    </div>
    <div class="form-group row">
        <input type="hidden" name="types[]" value="PAYMOB_IFRAME_ID">
        <div class="col-md-4">
            <label class="col-from-label">{{ translate('Paymob Iframe ID') }}</label>
        </div>
        <div class="col-lg-8">
            <input type="text" class="form-control" name="PAYMOB_IFRAME_ID"
                value="{{ env('PAYMOB_IFRAME_ID') }}"
                placeholder="{{ translate('Paymob Iframe ID') }}" required>
        </div>
    </div>
    <div class="form-group row">
        <input type="hidden" name="types[]" value="PAYMOB_INTEGRATION_ID">
        <div class="col-md-4">
            <label class="col-from-label">{{ translate('Paymob Integration ID') }}</label>
        </div>
        <div class="col-lg-8">
            <input type="text" class="form-control" name="PAYMOB_INTEGRATION_ID"
                value="{{ env('PAYMOB_INTEGRATION_ID') }}"
                placeholder="{{ translate('Paymob Integration ID') }}" required>
        </div>
    </div>
    <div class="form-group row">
        <input type="hidden" name="types[]" value="PAYMOB_HMAC">
        <div class="col-md-4">
            <label class="col-from-label">{{ translate('Paymob HMAC') }}</label>
        </div>
        <div class="col-lg-8">
            <input type="text" class="form-control" name="PAYMOB_HMAC"
                value="{{ env('PAYMOB_HMAC') }}"
                placeholder="{{ translate('Paymob HMAC') }}" required>
        </div>
    </div>
    <div class="form-group mb-0 text-right">
        <button type="submit" class="btn btn-sm btn-primary">{{ translate('Save') }}</button>
    </div>
</form>
