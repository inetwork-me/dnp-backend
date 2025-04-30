<form class="form-horizontal" action="{{ route('payment_method.update') }}" method="POST">
    @csrf
    <input type="hidden" name="payment_method" value="nagad">
    <div class="form-group row">
        <input type="hidden" name="types[]" value="NAGAD_MODE">
        <div class="col-md-4">
            <label class="col-from-label">{{ translate('NAGAD MODE') }}</label>
        </div>
        <div class="col-md-8">
            <input type="text" class="form-control" name="NAGAD_MODE"
                value="{{ env('NAGAD_MODE') }}" placeholder="{{ translate('NAGAD MODE') }}"
                required>
        </div>
    </div>
    <div class="form-group row">
        <input type="hidden" name="types[]" value="NAGAD_MERCHANT_ID">
        <div class="col-md-4">
            <label class="col-from-label">{{ translate('NAGAD MERCHANT ID') }}</label>
        </div>
        <div class="col-md-8">
            <input type="text" class="form-control" name="NAGAD_MERCHANT_ID"
                value="{{ env('NAGAD_MERCHANT_ID') }}"
                placeholder="{{ translate('NAGAD MERCHANT ID') }}" required>
        </div>
    </div>
    <div class="form-group row">
        <input type="hidden" name="types[]" value="NAGAD_MERCHANT_NUMBER">
        <div class="col-md-4">
            <label class="col-from-label">{{ translate('NAGAD MERCHANT NUMBER') }}</label>
        </div>
        <div class="col-md-8">
            <input type="text" class="form-control" name="NAGAD_MERCHANT_NUMBER"
                value="{{ env('NAGAD_MERCHANT_NUMBER') }}"
                placeholder="{{ translate('NAGAD MERCHANT NUMBER') }}" required>
        </div>
    </div>
    <div class="form-group row">
        <input type="hidden" name="types[]" value="NAGAD_PG_PUBLIC_KEY">
        <div class="col-md-4">
            <label class="col-from-label">{{ translate('NAGAD PG PUBLIC KEY') }}</label>
        </div>
        <div class="col-md-8">
            <input type="text" class="form-control" name="NAGAD_PG_PUBLIC_KEY"
                value="{{ env('NAGAD_PG_PUBLIC_KEY') }}"
                placeholder="{{ translate('NAGAD PG PUBLIC KEY') }}" required>
        </div>
    </div>
    <div class="form-group row">
        <input type="hidden" name="types[]" value="NAGAD_MERCHANT_PRIVATE_KEY">
        <div class="col-md-4">
            <label class="col-from-label">{{ translate('NAGAD MERCHANT PRIVATE KEY') }}</label>
        </div>
        <div class="col-md-8">
            <input type="text" class="form-control" name="NAGAD_MERCHANT_PRIVATE_KEY"
                value="{{ env('NAGAD_MERCHANT_PRIVATE_KEY') }}"
                placeholder="{{ translate('NAGAD MERCHANT PRIVATE KEY') }}" required>
        </div>
    </div>
    <div class="form-group mb-0 text-right">
        <button type="submit" class="btn btn-sm btn-primary">{{ translate('Save') }}</button>
    </div>
</form>
