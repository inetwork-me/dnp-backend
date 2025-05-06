<form class="form-horizontal" action="{{ route('payment_method.update') }}" method="POST">
    @csrf
    <input type="hidden" name="payment_method" value="voguepay">
    <div class="form-group row">
        <input type="hidden" name="types[]" value="VOGUE_MERCHANT_ID">
        <div class="col-md-4">
            <label class="col-from-label">{{ translate('MERCHANT ID') }}</label>
        </div>
        <div class="col-md-8">
            <input type="text" class="form-control" name="VOGUE_MERCHANT_ID"
                value="{{ env('VOGUE_MERCHANT_ID') }}"
                placeholder="{{ translate('MERCHANT ID') }}" required>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-md-4">
            <label class="col-from-label">{{ translate('Sandbox Mode') }}</label>
        </div>
        <div class="col-md-8">
            <label class="aiz-switch aiz-switch-success mb-0">
                <input value="1" name="voguepay_sandbox" type="checkbox"
                    @if (get_setting('voguepay_sandbox') == 1) checked @endif>
                <span class="slider round"></span>
            </label>
        </div>
    </div>
    <div class="form-group mb-0 text-right">
        <button type="submit" class="btn btn-sm btn-primary">{{ translate('Save') }}</button>
    </div>
</form>
