<form class="form-horizontal" action="{{ route('payment_method.update') }}" method="POST">
    @csrf
    <input type="hidden" name="payment_method" value="sslcommerz">
    <div class="form-group row">
        <input type="hidden" name="types[]" value="SSLCZ_STORE_ID">
        <div class="col-md-4">
            <label class="col-from-label">{{ translate('Sslcz Store Id') }}</label>
        </div>
        <div class="col-md-8">
            <input type="text" class="form-control" name="SSLCZ_STORE_ID"
                value="{{ env('SSLCZ_STORE_ID') }}"
                placeholder="{{ translate('Sslcz Store Id') }}" required>
        </div>
    </div>
    <div class="form-group row">
        <input type="hidden" name="types[]" value="SSLCZ_STORE_PASSWD">
        <div class="col-md-4">
            <label class="col-from-label">{{ translate('Sslcz store password') }}</label>
        </div>
        <div class="col-md-8">
            <input type="text" class="form-control" name="SSLCZ_STORE_PASSWD"
                value="{{ env('SSLCZ_STORE_PASSWD') }}"
                placeholder="{{ translate('Sslcz store password') }}" required>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-md-4">
            <label class="col-from-label">{{ translate('Sslcommerz Sandbox Mode') }}</label>
        </div>
        <div class="col-md-8">
            <label class="aiz-switch aiz-switch-success mb-0">
                <input value="1" name="sslcommerz_sandbox" type="checkbox"
                    @if (get_setting('sslcommerz_sandbox') == 1) checked @endif>
                <span class="slider round"></span>
            </label>
        </div>
    </div>
    <div class="form-group mb-0 text-right">
        <button type="submit" class="btn btn-sm btn-primary">{{ translate('Save') }}</button>
    </div>
</form>
