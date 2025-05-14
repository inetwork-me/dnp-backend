@props([
    'title' => null,
    'icon' => null,
    'count' => null,
    'change' => null,
])

<div class="card admin_statistcs m-0 mb-3">
    <div class="card-body">
        {!! $icon !!}
        <div class="statistcs_data">
            <span class="statistcs_data_title">{{ translate($title) }}</span>
            <span class="statistcs_data_number">{{ $count }}</span>
        </div>
    </div>
    <div class="card-footer">
        <div class="card_footer_statistics">
            <div>
                <img src="{{ static_asset('assets') }}/img/svg/up_arrow.png" alt="">
                <span>3.5%</span>
            </div>
            <span>Last Week</span>
        </div>
    </div>
</div>

{{-- <div class="card statistcs_card admin_statistcs m-0 mb-3">
    <div class="card-body">
        {!! $icon !!}
        <div class="statistcs_data">
            <span class="statistcs_data_title">{{ translate($title) }}</span>
            <span class="statistcs_data_number">{{ $count }}</span>
        </div>
    </div>

    @if ($change)
        <div class="card-footer">
            <div>
                <div>
                    <img src="{{ static_asset('assets') }}/img/svg/{{ $change['direction'] == 'up' ? 'up_arrow.png' : 'down_arrow.png' }}"
                        alt="{{ $change['direction'] }}">
                    <span>{{ $change['percent'] }}%</span>
                </div>
                <span>{{ translate($change['label'] ?? 'Last Week') }}</span>
            </div>
        </div>
    @endif
</div> --}}
