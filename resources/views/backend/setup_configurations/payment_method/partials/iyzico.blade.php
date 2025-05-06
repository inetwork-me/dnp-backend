<form class="form-horizontal" action="{{ route('payment_method.update') }}" method="POST">
    @csrf
    <input type="hidden" name="payment_method" value="iyzico">
    <div class="form-group row">
        <input type="hidden" name="types[]" value="IYZICO_API_KEY">
        <div class="col-lg-4">
            <label class="col-from-label">{{ translate('IYZICO_API_KEY') }}</label>
        </div>
        <div class="col-lg-8">
            <input type="text" class="form-control" name="IYZICO_API_KEY"
                value="{{ env('IYZICO_API_KEY') }}"
                placeholder="{{ translate('IYZICO API KEY') }}" required>
        </div>
    </div>
    <div class="form-group row">
        <input type="hidden" name="types[]" value="IYZICO_SECRET_KEY">
        <div class="col-lg-4">
            <label class="col-from-label">{{ translate('IYZICO_SECRET_KEY') }}</label>
        </div>
        <div class="col-lg-8">
            <input type="text" class="form-control" name="IYZICO_SECRET_KEY"
                value="{{ env('IYZICO_SECRET_KEY') }}"
                placeholder="{{ translate('IYZICO SECRET KEY') }}" required>
        </div>
    </div>
    <div class="form-group row">
        <input type="hidden" name="types[]" value="IYZICO_CURRENCY_CODE">
        <div class="col-md-4">
            <label class="col-from-label">{{ translate('IYZICO CURRENCY CODE') }}</label>
        </div>
        <div class="col-md-8">
            <input type="text" class="form-control" name="IYZICO_CURRENCY_CODE"
                value="{{ env('IYZICO_CURRENCY_CODE') }}"
                placeholder="{{ translate('IYZICO CURRENCY CODE') }}" required>
        </div>
    </div>

    <div class="form-group row">
        <div class="col-md-4">
            <label class="col-from-label">{{ translate('IYZICO Sandbox Mode') }}</label>
        </div>
        <div class="col-md-8">
            <label class="aiz-switch aiz-switch-success mb-0">
                <input value="1" name="iyzico_sandbox" type="checkbox"
                    @if (get_setting('iyzico_sandbox') == 1) checked @endif>
                <span class="slider round"></span>
            </label>
        </div>
    </div>

    <div class="form-group mb-0 text-right">
        <button type="submit" class="btn btn-sm btn-primary">{{ translate('Save') }}</button>
    </div>
</form>
