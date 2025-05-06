<form class="form-horizontal" action="{{ route('payment_method.update') }}" method="POST">
    @csrf
    <input type="hidden" name="payment_method" value="bkash">
    <div class="form-group row">
        <input type="hidden" name="types[]" value="BKASH_CHECKOUT_APP_KEY">
        <div class="col-md-4">
            <label class="col-from-label">{{ translate('BKASH CHECKOUT APP KEY') }}</label>
        </div>
        <div class="col-md-8">
            <input type="text" class="form-control" name="BKASH_CHECKOUT_APP_KEY"
                value="{{ env('BKASH_CHECKOUT_APP_KEY') }}"
                placeholder="{{ translate('BKASH CHECKOUT APP KEY') }}" required>
        </div>
    </div>
    <div class="form-group row">
        <input type="hidden" name="types[]" value="BKASH_CHECKOUT_APP_SECRET">
        <div class="col-md-4">
            <label class="col-from-label">{{ translate('BKASH CHECKOUT APP SECRET') }}</label>
        </div>
        <div class="col-md-8">
            <input type="text" class="form-control" name="BKASH_CHECKOUT_APP_SECRET"
                value="{{ env('BKASH_CHECKOUT_APP_SECRET') }}"
                placeholder="{{ translate('BKASH CHECKOUT APP SECRET') }}" required>
        </div>
    </div>
    <div class="form-group row">
        <input type="hidden" name="types[]" value="BKASH_CHECKOUT_USER_NAME">
        <div class="col-md-4">
            <label class="col-from-label">{{ translate('BKASH CHECKOUT USER NAME') }}</label>
        </div>
        <div class="col-md-8">
            <input type="text" class="form-control" name="BKASH_CHECKOUT_USER_NAME"
                value="{{ env('BKASH_CHECKOUT_USER_NAME') }}"
                placeholder="{{ translate('BKASH CHECKOUT USER NAME') }}" required>
        </div>
    </div>
    <div class="form-group row">
        <input type="hidden" name="types[]" value="BKASH_CHECKOUT_PASSWORD">
        <div class="col-md-4">
            <label class="col-from-label">{{ translate('BKASH CHECKOUT PASSWORD') }}</label>
        </div>
        <div class="col-md-8">
            <input type="text" class="form-control" name="BKASH_CHECKOUT_PASSWORD"
                value="{{ env('BKASH_CHECKOUT_PASSWORD') }}"
                placeholder="{{ translate('BKASH CHECKOUT PASSWORD') }}" required>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-md-4">
            <label class="col-from-label">{{ translate('Bkash Sandbox Mode') }}</label>
        </div>
        <div class="col-md-8">
            <label class="aiz-switch aiz-switch-success mb-0">
                <input value="1" name="bkash_sandbox" type="checkbox"
                    @if (get_setting('bkash_sandbox') == 1) checked @endif>
                <span class="slider round"></span>
            </label>
        </div>
    </div>
    <div class="form-group mb-0 text-right">
        <button type="submit" class="btn btn-sm btn-primary">{{ translate('Save') }}</button>
    </div>
</form>
