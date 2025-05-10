@extends('backend.layouts.app')

@section('title')
{{ translate('Edit City') }}
@endsection

@section('content')

<ul class="nav nav-tabs nav-fill language-bar">
    @foreach (get_all_active_language() as $key => $language)
        <li class="nav-item">
            <a class="nav-link text-reset @if ($language->code == $lang) active @endif py-3" href="{{ route('cities.edit', ['id'=>$city->id, 'lang'=> $language->code] ) }}">
                <img src="{{ static_asset('assets/img/flags/'.$language->code.'.png') }}" height="11" class="mr-1">
                <span>{{ $language->name }}</span>
            </a>
        </li>
  @endforeach
</ul>

<x-dynamic-form 
    :action="route('cities.update',$city->id)" 
    method="PUT" 
    title="Update City"
    :submitLabel="'Update'"
    cancelRoute="cities.index"
    :fields="[
        ['label' => 'Name', 'name' => 'name', 'type' => 'text', 'required' => true,'value'=> $city->name],
        ['type' => 'hidden', 'name' => 'lang', 'value' => $lang],
        [
            'label' => 'State',
            'name' => 'state_id',
            'type' => 'select',
            'required' => true,
            'value' => $city->state_id,
            'options' => $states->map(fn($state) => ['value' => $state->id, 'label' => $state->name])->toArray()
        ],
        ['label' => 'Cost', 'name' => 'cost', 'type' => 'number', 'required' => true, 'step' => '0.01', 'min' => 0,'value'=> $city->cost]
    ]"
/>
@endsection
