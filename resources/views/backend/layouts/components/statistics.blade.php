
@foreach ($dashboard_statistics as $state)
    <div class="col-3">
        <x-statistics-card
            title="{{ $state['name'] }}"
            icon="{!! $state['icon'] !!}"
            count="{{ $state['count'] }}"
            :change="null"
        />
    </div>
@endforeach

