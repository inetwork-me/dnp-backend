<form class="form-horizontal" action="{{ route('payment_method.update') }}" method="POST">
    @csrf
    <input type="hidden" name="payment_method" value="instamojo">
    <div class="form-group row">
        <input type="hidden" name="types[]" value="IM_API_KEY">
        <div class="col-md-4">
            <label class="col-from-label">{{ translate('API KEY') }}</label>
        </div>
        <div class="col-md-8">
            <input type="text" class="form-control" name="IM_API_KEY"
                value="{{ env('IM_API_KEY') }}" placeholder="{{ translate('IM API KEY') }}"
                required>
        </div>
    </div>
    <div class="form-group row">
        <input type="hidden" name="types[]" value="IM_AUTH_TOKEN">
        <div class="col-md-4">
            <label class="col-from-label">{{ translate('AUTH TOKEN') }}</label>
        </div>
        <div class="col-md-8">
            <input type="text" class="form-control" name="IM_AUTH_TOKEN"
                value="{{ env('IM_AUTH_TOKEN') }}"
                placeholder="{{ translate('IM AUTH TOKEN') }}" required>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-md-4">
            <label class="col-from-label">{{ translate('Instamojo Sandbox Mode') }}</label>
        </div>
        <div class="col-md-8">
            <label class="aiz-switch aiz-switch-success mb-0">
                <input value="1" name="instamojo_sandbox" type="checkbox"
                    @if (get_setting('instamojo_sandbox') == 1) checked @endif>
                <span class="slider round"></span>
            </label>
        </div>
    </div>
    <div class="form-group mb-0 text-right">
        <button type="submit" class="btn btn-sm btn-primary">{{ translate('Save') }}</button>
    </div>
</form>
