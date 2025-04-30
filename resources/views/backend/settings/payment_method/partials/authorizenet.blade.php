<form class="form-horizontal" action="{{ route('payment_method.update') }}" method="POST">
    @csrf
    <input type="hidden" name="payment_method" value="authorizenet">
    <div class="form-group row">
        <input type="hidden" name="types[]" value="MERCHANT_LOGIN_ID">
        <div class="col-lg-4">
            <label class="col-from-label">{{ translate('MERCHANT_LOGIN_ID') }}</label>
        </div>
        <div class="col-lg-8">
            <input type="text" class="form-control" name="MERCHANT_LOGIN_ID"
                value="{{ env('MERCHANT_LOGIN_ID') }}"
                placeholder="{{ translate('MERCHANT LOGIN ID') }}" required>
        </div>
    </div>
    <div class="form-group row">
        <input type="hidden" name="types[]" value="MERCHANT_TRANSACTION_KEY">
        <div class="col-lg-4">
            <label class="col-from-label">{{ translate('MERCHANT_TRANSACTION_KEY') }}</label>
        </div>
        <div class="col-lg-8">
            <input type="text" class="form-control" name="MERCHANT_TRANSACTION_KEY"
                value="{{ env('MERCHANT_TRANSACTION_KEY') }}"
                placeholder="{{ translate('MERCHANT TRANSACTION KEY') }}" required>
        </div>
    </div>

    <div class="form-group row">
        <div class="col-md-4">
            <label class="col-from-label">{{ translate('Authorize Net Sandbox Mode') }}</label>
        </div>
        <div class="col-md-8">
            <label class="aiz-switch aiz-switch-success mb-0">
                <input value="1" name="authorizenet_sandbox" type="checkbox"
                    @if (get_setting('authorizenet_sandbox') == 1) checked @endif>
                <span class="slider round"></span>
            </label>
        </div>
    </div>

    <div class="form-group mb-0 text-right">
        <button type="submit" class="btn btn-sm btn-primary">{{ translate('Save') }}</button>
    </div>
</form>
