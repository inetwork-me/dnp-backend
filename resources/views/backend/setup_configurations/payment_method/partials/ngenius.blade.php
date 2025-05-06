<form class="form-horizontal" action="{{ route('payment_method.update') }}" method="POST">
    @csrf
    <input type="hidden" name="payment_method" value="ngenius">
    <div class="form-group row">
        <input type="hidden" name="types[]" value="NGENIUS_OUTLET_ID">
        <div class="col-lg-4">
            <label class="col-from-label">{{ translate('NGENIUS OUTLET ID') }}</label>
        </div>
        <div class="col-lg-8">
            <input type="text" class="form-control" name="NGENIUS_OUTLET_ID"
                value="{{ env('NGENIUS_OUTLET_ID') }}"
                placeholder="{{ translate('NGENIUS OUTLET ID') }}" required>
        </div>
    </div>
    <div class="form-group row">
        <input type="hidden" name="types[]" value="NGENIUS_API_KEY">
        <div class="col-lg-4">
            <label class="col-from-label">{{ translate('NGENIUS API KEY') }}</label>
        </div>
        <div class="col-lg-8">
            <input type="text" class="form-control" name="NGENIUS_API_KEY"
                value="{{ env('NGENIUS_API_KEY') }}"
                placeholder="{{ translate('NGENIUS API KEY') }}" required>
        </div>
    </div>
    <div class="form-group row">
        <input type="hidden" name="types[]" value="NGENIUS_CURRENCY">
        <div class="col-lg-4">
            <label class="col-from-label">{{ translate('NGENIUS CURRENCY') }}</label>
        </div>
        <div class="col-lg-8">
            <input type="text" class="form-control" name="NGENIUS_CURRENCY"
                value="{{ env('NGENIUS_CURRENCY') }}"
                placeholder="{{ translate('NGENIUS CURRENCY') }}" required>
            <br>
            <div class="alert alert-primary" role="alert">
                Currency must be <b>AED</b> or <b>USD</b> or <b>EUR</b><br>
                If kept empty, <b>AED</b> will be used automatically
            </div>
        </div>
    </div>
    <div class="form-group mb-0 text-right">
        <button type="submit" class="btn btn-sm btn-primary">{{ translate('Save') }}</button>
    </div>
</form>
