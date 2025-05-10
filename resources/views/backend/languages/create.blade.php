@extends('backend.layouts.app')

@section('title')
    {{ translate('New Language') }}
@endsection

@section('content')

@php
    $availableFlags = collect(\File::files(base_path('public/assets/img/flags')))
        ->filter(fn($path) => !in_array(pathinfo($path)['filename'], \App\Models\Language::pluck('code')->toArray()))
        ->map(fn($path) => [
            'value' => pathinfo($path)['filename'],
            'label' => strtoupper(pathinfo($path)['filename']),
            'content' => "<div class='d-flex align-items-center'><img src='" . static_asset('assets/img/flags/' . pathinfo($path)['filename'] . '.png') . "' class='mr-2' width='20'><span>" . strtoupper(pathinfo($path)['filename']) . "</span></div>",
        ])
        ->toArray();
@endphp

<x-dynamic-form 
    :action="route('languages.store')" 
    method="POST" 
    title="New Language"
    :submitLabel="'Save'"
    cancelRoute="languages.index"
    :fields="[
        ['label' => 'Name', 'name' => 'name', 'type' => 'text', 'required' => true],
        ['label' => 'Code', 'name' => 'code', 'type' => 'select', 'required' => true, 'options' => $availableFlags],
        ['label' => 'Frontend Code', 'name' => 'app_lang_code', 'type' => 'text', 'required' => true]
    ]"
/>
@endsection
