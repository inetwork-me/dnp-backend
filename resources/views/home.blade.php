@extends('backend.layouts.app')

@section('title')
    {{ translate('Home') }}
@endsection

@section('content')
    @include('backend.layouts.components.statistics')
@endsection
