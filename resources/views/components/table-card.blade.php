<div class="card statistcs_card m-0 mb-3">
    <div class="card-header">
        <span>{{ $title }}</span>
        @isset($link)
            <a href="{{ $link }}" class="btn btn-primary">{{ translate('Add New') }}</a>
        @endisset
    </div>
    <div class="card-body">
        <div class="table-container">
            <div class="table-responsive">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
