<form class="form-horizontal" action="{{ route('payment_method.update') }}" method="POST">
    @csrf
    <input type="hidden" name="payment_method" value="mercadopago">
    <div class="form-group row">
        <input type="hidden" name="types[]" value="MERCADOPAGO_KEY">
        <div class="col-md-4">
            <label class="col-from-label">{{ translate('Mercadopago Key') }}</label>
        </div>
        <div class="col-md-8">
            <input type="text" class="form-control" name="MERCADOPAGO_KEY"
                value="{{ env('MERCADOPAGO_KEY') }}"
                placeholder="{{ translate('Mercadopago Key') }}" required>
        </div>
    </div>
    <div class="form-group row">
        <input type="hidden" name="types[]" value="MERCADOPAGO_ACCESS">
        <div class="col-md-4">
            <label class="col-from-label">{{ translate('Mercadopago Access') }}</label>
        </div>
        <div class="col-md-8">
            <input type="text" class="form-control" name="MERCADOPAGO_ACCESS"
                value="{{ env('MERCADOPAGO_ACCESS') }}"
                placeholder="{{ translate('Mercadopago Access') }}" required>
        </div>
    </div>
    <div class="form-group row">
        <input type="hidden" name="types[]" value="MERCADOPAGO_CURRENCY">
        <div class="col-lg-4">
            <label class="col-from-label">{{ translate('MERCADOPAGO CURRENCY') }}</label>
        </div>
        <div class="col-lg-8">
            <input type="text" class="form-control" name="MERCADOPAGO_CURRENCY"
                value="{{ env('MERCADOPAGO_CURRENCY') }}"
                placeholder="{{ translate('MERCADOPAGO CURRENCY') }}" required>
            <br>
            <div class="alert alert-primary" role="alert">
                Currency must be <b>es-AR</b> or <b>es-CL</b> or <b>es-CO</b> or <b>es-MX</b> or
                <b>es-VE</b> or <b>es-UY</b> or <b>es-PE</b> or <b>pt-BR</b><br>
                If kept empty, <b>en-US</b> will be used automatically
            </div>
        </div>
    </div>

    <div class="form-group mb-0 text-right">
        <button type="submit" class="btn btn-sm btn-primary">{{ translate('Save') }}</button>
    </div>
</form>
