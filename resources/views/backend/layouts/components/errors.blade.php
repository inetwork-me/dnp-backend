@if ($errors->any())
    <div class="card statistcs_card add_form m-0 mb-3 card-danger">
        <div class="card-header">
            <span>{{ translate('Fix These Erros') }}</span>
        </div>
        <div class="card-body">
            <ul class="list-unstyled mb-0">
                @foreach ($errors->all() as $error)
                    <h6 class="text-danger">{{ $error }}</h6>
                @endforeach
            </ul>
        </div>
    </div>
@endif
