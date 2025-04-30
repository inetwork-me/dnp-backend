@extends('backend.layouts.app')

@section('title')
    {{ translate('New Category') }}
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 col-lg-6 col-12">
            <x-form-card title="{{ translate('Category Information') }}">
                <form class="form-horizontal" action="{{ route('categories.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{ translate('Name') }}</label>
                        <div class="col-md-9">
                            <input type="text" placeholder="{{ translate('Name') }}" id="name" name="name"
                                class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{ translate('Type') }}</label>
                        <div class="col-md-9">
                            <select name="digital" onchange="categoriesByType(this.value)" required
                                class="form-control  mb-2 mb-md-0">
                                <option value="0">{{ translate('Physical') }}</option>
                                <option value="1">{{ translate('Digital') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{ translate('Parent Category') }}</label>
                        <div class="col-md-9">
                            <select class="select2 form-control " name="parent_id" data-toggle="select2"
                                data-placeholder="Choose ..." data-live-search="true">
                                @include('backend.product.categories.categories_option', [
                                    'categories' => $categories,
                                ])
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">
                            {{ translate('Ordering Number') }}
                        </label>
                        <div class="col-md-9">
                            <input type="number" name="order_level" class="form-control" id="order_level"
                                placeholder="{{ translate('Order Level') }}">
                            <small>{{ translate('Higher number has high priority') }}</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label" for="signinSrEmail">{{ translate('Banner') }}</label>
                        <div class="col-md-9">
                            <div class="input-group" data-toggle="aizuploader" data-type="image">
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-soft-secondary font-weight-medium">
                                        {{ translate('Browse') }}</div>
                                </div>
                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                <input type="hidden" name="banner" class="selected-files">
                            </div>
                            <div class="file-preview box sm">
                            </div>
                            <small
                                class="text-muted">{{ translate('Minimum dimensions required: 150px width X 150px height.') }}</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label" for="signinSrEmail">{{ translate('Icon') }}</label>
                        <div class="col-md-9">
                            <div class="input-group" data-toggle="aizuploader" data-type="image">
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-soft-secondary font-weight-medium">
                                        {{ translate('Browse') }}</div>
                                </div>
                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                <input type="hidden" name="icon" class="selected-files">
                            </div>
                            <div class="file-preview box sm">
                            </div>
                            <small
                                class="text-muted">{{ translate('Minimum dimensions required: 16px width X 16px height.') }}</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label" for="signinSrEmail">{{ translate('Cover Image') }}</label>
                        <div class="col-md-9">
                            <div class="input-group" data-toggle="aizuploader" data-type="image">
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-soft-secondary font-weight-medium">
                                        {{ translate('Browse') }}</div>
                                </div>
                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                <input type="hidden" name="cover_image" class="selected-files">
                            </div>
                            <div class="file-preview box sm">
                            </div>
                            <small
                                class="text-muted">{{ translate('Minimum dimensions required: 260px width X 260px height.') }}</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{ translate('Meta Title') }}</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="meta_title"
                                placeholder="{{ translate('Meta Title') }}">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{ translate('Meta Description') }}</label>
                        <div class="col-md-9">
                            <textarea name="meta_description" rows="5" class="form-control"></textarea>
                        </div>
                    </div>
                    @if (get_setting('category_wise_commission') == 1)
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">{{ translate('Commission Rate') }}</label>
                            <div class="col-md-9 input-group">
                                <input type="number" lang="en" min="0" step="0.01"
                                    placeholder="{{ translate('Commission Rate') }}" id="commision_rate"
                                    name="commision_rate" class="form-control">
                                <div class="input-group-append">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{ translate('Filtering Attributes') }}</label>
                        <div class="col-md-9">
                            <select class="select2 form-control " name="filtering_attributes[]" data-toggle="select2"
                                data-placeholder="Choose ..."data-live-search="true" multiple>
                                @foreach (\App\Models\Attribute::all() as $attribute)
                                    <option value="{{ $attribute->id }}">{{ $attribute->getTranslation('name') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group mb-0 text-right">
                        <button type="submit" class="btn btn-primary">{{ translate('Save') }}</button>
                    </div>
                </form>
            </x-form-card>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        function categoriesByType(val) {
            $('select[name="parent_id"]').html('');
            AIZ.plugins.bootstrapSelect('refresh');
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: '{{ route('categories.categories-by-type') }}',
                data: {
                    digital: val
                },
                success: function(data) {
                    $('select[name="parent_id"]').html(data);
                    AIZ.plugins.bootstrapSelect('refresh');
                }
            });
        }
    </script>
@endsection
