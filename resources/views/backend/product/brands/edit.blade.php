@extends('backend.layouts.app')

@section('title')
{{ translate('Update Brand') }}
@endsection

@section('content')

<div class="row">
	<div class="col-xl-12 col-lg-6 col-12">
        <ul class="nav nav-tabs nav-fill language-bar">
            @foreach (get_all_active_language() as $key => $language)
                <li class="nav-item">
                    <a class="nav-link text-reset @if ($language->code == $lang) active @endif py-3" href="{{ route('brands.edit', ['id'=>$brand->id, 'lang'=> $language->code] ) }}">
                        <img src="{{ static_asset('assets/img/flags/'.$language->code.'.png') }}" height="11" class="mr-1">
                        <span>{{ $language->name }}</span>
                    </a>
                </li>
          @endforeach
        </ul>
		<x-form-card title="{{ translate('Update Brand') }}">
			<form class="p-4" action="{{ route('brands.update', $brand->id) }}" method="POST" enctype="multipart/form-data">
                <input name="_method" type="hidden" value="PATCH">
                <input type="hidden" name="lang" value="{{ $lang }}">
                @csrf
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="name">{{translate('Name')}} <i class="las la-language text-danger" title="{{translate('Translatable')}}"></i></label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="{{translate('Name')}}" id="name" name="name" value="{{ $brand->getTranslation('name', $lang) }}" class="form-control" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-3 col-form-label" for="signinSrEmail">{{translate('Logo')}} <small>({{ translate('120x80') }})</small></label>
                    <div class="col-md-9">
                        <div class="input-group" data-toggle="aizuploader" data-type="image">
                            <div class="input-group-prepend">
                                <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                            </div>
                            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                            <input type="hidden" name="logo" value="{{$brand->logo}}" class="selected-files">
                        </div>
                        <div class="file-preview box sm">
                        </div>
                        <small class="text-muted">{{ translate('Minimum dimensions required: 126px width X 100px height.') }}</small>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label">{{translate('Meta Title')}}</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="meta_title" value="{{ $brand->meta_title }}" placeholder="{{translate('Meta Title')}}">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label">{{translate('Meta Description')}}</label>
                    <div class="col-sm-9">
                        <textarea name="meta_description" rows="8" class="form-control">{{ $brand->meta_description }}</textarea>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="name">{{translate('Slug')}}</label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="{{translate('Slug')}}" id="slug" name="slug" value="{{ $brand->slug }}" class="form-control">
                    </div>
                </div>
                <div class="form-group mb-0 text-right">
                    <button type="submit" class="btn btn-primary">{{translate('Save')}}</button>
                </div>
            </form>
		</x-form-card>
	</div>
</div>

@endsection
