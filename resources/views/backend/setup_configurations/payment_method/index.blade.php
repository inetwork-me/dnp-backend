    @extends('backend.layouts.app')

    @section('content')
        <div class="row">
            @foreach ($payment_methods as $payment_method)
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <img class="mr-3" src="{{ static_asset('assets/img/cards/'.$payment_method->name.'.png') }}" height="30">
                            <h5 class="mb-0 h6">{{ ucfirst(translate($payment_method->name)) }}</h5>
                        </div>
                        <label class="aiz-switch aiz-switch-success mb-0 float-right">
                            <input type="checkbox" onchange="updatePaymentSettings(this, {{ $payment_method->id }})" @if ($payment_method->active == 1) checked @endif>
                            <span class="slider round"></span>
                        </label>
                    </div>
                    <div class="card-body">
                        @include('backend.setup_configurations.payment_method.partials.'.$payment_method->name)
                    </div>
                </div>
            </div>
            @endforeach

            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <img class="mr-3" src="{{ static_asset('assets/img/cards/cod.png') }}" height="30">
                            <h5 class="mb-0 h6">{{ translate('Cash Payment') }}</h5>
                        </div>
                        <label class="aiz-switch aiz-switch-success mb-0 float-right">
                            <input type="checkbox" onchange="updateSettings(this, 'cash_payment')" @if (get_setting('cash_payment') == 1) checked @endif>
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
        @php
            // $demo_mode = env('DEMO_MODE') == 'On' ? true : false;
        @endphp
    @endsection

    @section('script')
        <script type="text/javascript">
            function updatePaymentSettings(el, id) {

                if('{{env('DEMO_MODE')}}' == 'On'){
                    AIZ.plugins.notify('info', '{{ translate('Data can not change in demo mode.') }}');
                    return;
                }

                if ($(el).is(':checked')) {
                    var value = 1;
                } else {
                    var value = 0;
                }

                $.post('{{ route('payment.activation') }}', {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    value: value
                }, function(data) {
                    if (data == 1) {
                        AIZ.plugins.notify('success', '{{ translate('Payment Settings updated successfully') }}');
                    } else {
                        AIZ.plugins.notify('danger', 'Something went wrong');
                    }
                });
            }

            function updateSettings(el, type) {

                if('{{env('DEMO_MODE')}}' == 'On'){
                    AIZ.plugins.notify('info', '{{ translate('Data can not change in demo mode.') }}');
                    return;
                }

                if ($(el).is(':checked')) {
                    var value = 1;
                } else {
                    var value = 0;
                }

                $.post('{{ route('business_settings.update.activation') }}', {
                    _token: '{{ csrf_token() }}',
                    type: type,
                    value: value
                }, function(data) {
                    if (data == 1) {
                        AIZ.plugins.notify('success', '{{ translate('Settings updated successfully') }}');
                    } else {
                        AIZ.plugins.notify('danger', 'Something went wrong');
                    }
                });
            }
        </script>
    @endsection
