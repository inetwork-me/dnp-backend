@extends('backend.layouts.app')

@section('title')
    {{ translate('Home') }}
@endsection

@section('content')
    <div class="row mt-3">
        <div class="col-md-6">
            @include('backend.layouts.components.statistics')
        </div>

        <div class="col-md-6">
        </div>

    </div>
@endsection
