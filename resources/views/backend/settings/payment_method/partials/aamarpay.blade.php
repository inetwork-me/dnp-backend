<form class="form-horizontal" action="{{ route('payment_method.update') }}" method="POST">
    @csrf
    <input type="hidden" name="payment_method" value="aamarpay">
    <div class="form-group row">
        <input type="hidden" name="types[]" value="AAMARPAY_STORE_ID">
        <div class="col-md-4">
            <label class="col-from-label">{{ translate('Aamarpay Store Id') }}</label>
        </div>
        <div class="col-md-8">
            <input type="text" class="form-control" name="AAMARPAY_STORE_ID"
                value="{{ env('AAMARPAY_STORE_ID') }}"
                placeholder="{{ translate('Aamarpay Store Id') }}" required>
        </div>
    </div>
    <div class="form-group row">
        <input type="hidden" name="types[]" value="AAMARPAY_SIGNATURE_KEY">
        <div class="col-md-4">
            <label class="col-from-label">{{ translate('Aamarpay signature key') }}</label>
        </div>
        <div class="col-md-8">
            <input type="text" class="form-control" name="AAMARPAY_SIGNATURE_KEY"
                value="{{ env('AAMARPAY_SIGNATURE_KEY') }}"
                placeholder="{{ translate('Aamarpay signature key') }}" required>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-md-4">
            <label class="col-from-label">{{ translate('Aamarpay Sandbox Mode') }}</label>
        </div>
        <div class="col-md-8">
            <label class="aiz-switch aiz-switch-success mb-0">
                <input value="1" name="aamarpay_sandbox" type="checkbox"
                    @if (get_setting('aamarpay_sandbox') == 1) checked @endif>
                <span class="slider round"></span>
            </label>
        </div>
    </div>
    <div class="form-group mb-0 text-right">
        <button type="submit" class="btn btn-sm btn-primary">{{ translate('Save') }}</button>
    </div>
</form>
