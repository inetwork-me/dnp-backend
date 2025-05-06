<form class="form-horizontal" action="{{ route('payment_method.update') }}" method="POST">
    @csrf
    <input type="hidden" name="payment_method" value="myfatoorah">
    <div class="form-group row">
        <input type="hidden" name="types[]" value="MYFATOORAH_TOKEN">
        <div class="col-md-4">
            <label class="col-from-label">{{ translate('MYFATOORAH TOKEN') }}</label>
        </div>
        <div class="col-md-8">
            <input type="text" class="form-control" name="MYFATOORAH_TOKEN"
                value="{{ env('MYFATOORAH_TOKEN') }}"
                placeholder="{{ translate('MYFATOORAH TOKEN') }}" required>
        </div>
    </div>
    <div class="form-group row">
        <input type="hidden" name="types[]" value="MYFATOORAH_COUNTRY_ISO">
        <div class="col-md-4">
            <label class="col-from-label">{{ translate('MYFATOORAH COUNTRY ISO') }}</label>
        </div>
        <div class="col-md-8">
            <input type="text" class="form-control" name="MYFATOORAH_COUNTRY_ISO"
                value="{{ env('MYFATOORAH_COUNTRY_ISO') }}"
                placeholder="{{ translate('MYFATOORAH COUNTRY ISO') }}" required>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-md-4">
            <label class="col-from-label">{{ translate('Sandbox Mode') }}</label>
        </div>
        <div class="col-md-8">
            <label class="aiz-switch aiz-switch-success mb-0">
                <input value="1" name="myfatoorah" type="checkbox"
                    @if (get_setting('myfatoorah') == 1) checked @endif>
                <span class="slider round"></span>
            </label>
        </div>
    </div>
    <div class="form-group mb-0 text-right">
        <button type="submit" class="btn btn-sm btn-primary">{{ translate('Save') }}</button>
    </div>
</form>