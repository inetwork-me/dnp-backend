<form class="form-horizontal" action="{{ route('payment_method.update') }}" method="POST">
    @csrf
    <input type="hidden" name="payment_method" value="paystack">
    <div class="form-group row">
        <input type="hidden" name="types[]" value="PAYSTACK_PUBLIC_KEY">
        <div class="col-md-4">
            <label class="col-from-label">{{ translate('PUBLIC KEY') }}</label>
        </div>
        <div class="col-md-8">
            <input type="text" class="form-control" name="PAYSTACK_PUBLIC_KEY"
                value="{{ env('PAYSTACK_PUBLIC_KEY') }}"
                placeholder="{{ translate('PUBLIC KEY') }}" required>
        </div>
    </div>
    <div class="form-group row">
        <input type="hidden" name="types[]" value="PAYSTACK_SECRET_KEY">
        <div class="col-md-4">
            <label class="col-from-label">{{ translate('SECRET KEY') }}</label>
        </div>
        <div class="col-md-8">
            <input type="text" class="form-control" name="PAYSTACK_SECRET_KEY"
                value="{{ env('PAYSTACK_SECRET_KEY') }}"
                placeholder="{{ translate('SECRET KEY') }}" required>
        </div>
    </div>
    <div class="form-group row">
        <input type="hidden" name="types[]" value="MERCHANT_EMAIL">
        <div class="col-md-4">
            <label class="col-from-label">{{ translate('MERCHANT EMAIL') }}</label>
        </div>
        <div class="col-md-8">
            <input type="text" class="form-control" name="MERCHANT_EMAIL"
                value="{{ env('MERCHANT_EMAIL') }}"
                placeholder="{{ translate('MERCHANT EMAIL') }}" required>
        </div>
    </div>
    <div class="form-group row">
        <input type="hidden" name="types[]" value="PAYSTACK_CURRENCY_CODE">
        <div class="col-md-4">
            <label class="col-from-label">{{ translate('PAYSTACK CURRENCY CODE') }}</label>
        </div>
        <div class="col-md-8">
            <input type="text" class="form-control" name="PAYSTACK_CURRENCY_CODE"
                value="{{ env('PAYSTACK_CURRENCY_CODE') }}"
                placeholder="{{ translate('PAYSTACK CURRENCY CODE') }}" required>
        </div>
    </div>
    <div class="form-group mb-0 text-right">
        <button type="submit" class="btn btn-sm btn-primary">{{ translate('Save') }}</button>
    </div>
</form>
