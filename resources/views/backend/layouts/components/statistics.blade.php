@foreach ($dashboard_statistics as $state)
<div class="{!! $state['class'] !!}">
    <div class="bg-white rounded-2 h-90px d-flex align-items-center justify-content-between text-primary px-2rem mb-3 state_card dashboard-box">
        <div class="d-flex flex-wrap align-items-center state_card_title" style="gap:20px;">
            {!! $state['icon'] !!}
            <div class="d-flex flex-wrap" style="flex-direction:column">
                <span class="fw-600 text-dark mb-0" style="font-size: 18px">{{ $state['name'] }}</span>
                <h1 class="fs-24 fw-600 mb-0">
                    {{ $state['count'] }}
                </h1>
            </div>
        </div>
        
    </div>
</div>
@endforeach
