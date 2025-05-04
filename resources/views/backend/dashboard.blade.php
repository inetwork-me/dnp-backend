@extends('backend.layouts.app')

@section('title')
    {{ translate('Home') }}
@endsection

@section('content')
    <div class="row mt-3">
        @include('backend.layouts.components.statistics')
    </div>
@endsection
