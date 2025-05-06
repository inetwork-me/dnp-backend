@extends('backend.layouts.app')

@section('content')
    <div class="aiz-titlebar text-left mt-2 mb-3">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="h3">{{ translate('Page Sections') }}</h1>
            </div>
        </div>
    </div>
    <div class="card">
        <ul class="nav nav-tabs nav-fill language-bar">
            @foreach (get_all_active_language() as $key => $language)
                <li class="nav-item">
                    <a class="nav-link text-reset @if ($language->code == $lang) active @endif py-3"
                        href="{{ route('custom-pages.sections', ['id' => $page->id, 'lang' => $language->code]) }}">
                        <img src="{{ static_asset('assets/img/flags/' . $language->code . '.png') }}" height="11"
                            class="mr-1">
                        <span>{{ $language->name }}</span>
                    </a>
                </li>
            @endforeach
        </ul>

        <div class="p-3">
            @foreach ($page_sections as $index => $section)
                <form action="{{ route('custom-pages.sectionUpdate', $section->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="lang" value="{{ $lang }}">
                    <div class="item-content">
                        <div class="form-group row">
                            <label class="col-sm-2 col-from-label" for="title">
                                {{ translate('Section Title') }} <span class="text-danger">*</span>
                                <i class="las la-language text-danger" title="{{ translate('Translatable') }}"></i>
                            </label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" placeholder="{{ translate('Title') }}"
                                    name="title" value="{{ $section->title }}" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-from-label" for="content">
                                {{ translate('Section Description') }} <span class="text-danger">*</span>
                                <i class="las la-language text-danger" title="{{ translate('Translatable') }}"></i>
                            </label>
                            <div class="col-sm-10">
                                <textarea class="aiz-text-editor" placeholder="{{ translate('Description') }}"
                                    name="content" required>{{ $section->content }}</textarea>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-from-label">{{ translate('Section Image') }}</label>
                            <div class="col-sm-10">
                                <div class="input-group" data-toggle="aizuploader" data-type="image">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text bg-soft-secondary font-weight-medium">
                                            {{ translate('Browse') }}
                                        </div>
                                    </div>
                                    <div class="form-control file-amount">
                                        {{ translate('Choose File') }}
                                    </div>
                                    <input type="hidden" name="image"
                                        class="selected-files" value="{{ $section->image }}">
                                </div>
                                <div class="file-preview">
                                    @if ($section->image)
                                        <img src="{{ uploaded_asset($section->image) }}" alt="Current Image"
                                            class="img-fluid mt-2">
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-10 offset-sm-2">
                                <a href="{{ route('custom-pages.sectionDelete', $section->id) }}"
                                    onclick="return confirm('Are you sure?')" class="btn btn-danger">
                                    {{ translate('Remove Section') }}
                                </a>
                                <button type="submit" class="btn btn-warning">
                                    {{ translate('Update Section') }}
                                </button>
                            </div>
                        </div>
                        <hr>
                    </div>
                </form>
            @endforeach
        </div>
    </div>
@endsection

@section('script')
@endsection
