<form class="form-horizontal" action="{{ route('payment_method.update') }}" method="POST">
    @csrf
    <input type="hidden" name="payment_method" value="stripe">
    <div class="form-group row">
        <input type="hidden" name="types[]" value="STRIPE_KEY">
        <div class="col-md-4">
            <label class="col-from-label">{{ translate('Stripe Key') }}</label>
        </div>
        <div class="col-md-8">
            <input type="text" class="form-control" name="STRIPE_KEY"
                value="{{ env('STRIPE_KEY') }}" placeholder="{{ translate('STRIPE KEY') }}"
                required>
        </div>
    </div>
    <div class="form-group row">
        <input type="hidden" name="types[]" value="STRIPE_SECRET">
        <div class="col-md-4">
            <label class="col-from-label">{{ translate('Stripe Secret') }}</label>
        </div>
        <div class="col-md-8">
            <input type="text" class="form-control" name="STRIPE_SECRET"
                value="{{ env('STRIPE_SECRET') }}" placeholder="{{ translate('STRIPE SECRET') }}"
                required>
        </div>
    </div>
    <div class="form-group mb-0 text-right">
        <button type="submit" class="btn btn-sm btn-primary">{{ translate('Save') }}</button>
    </div>
</form>
