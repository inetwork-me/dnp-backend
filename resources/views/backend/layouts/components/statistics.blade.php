<div class="row">
    
@foreach ($dashboard_statistics as $state)
    <div class="col-md-6">
        <x-statistics-card
            title="{{ $state['name'] }}"
            icon="{!! $state['icon'] !!}"
            count="{{ $state['count'] }}"
            :change="null"
        />
    </div>
@endforeach


</div>