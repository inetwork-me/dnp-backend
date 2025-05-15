@extends('backend.layouts.app')

@section('title')
    {{ translate('Edit Field') }}
@endsection

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--single {
            background-color: #fff;
            border: 1px solid #dfdfe6;
            border-radius: 10px;
            height: 49px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 45px;
            top: 1px;
            right: 1px;
            width: 20px;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #000;
            line-height: 45px;
        }
        label.aiz-switch.aiz-switch-success.mb-0 {
            display: flex;
            gap: 10px;
        }
    </style>
@endsection

@section('content')
<div class="card statistcs_card add_form m-0 mb-3">
    <div class="card-header">
        <span>{{ translate('Edit Field') }}</span>
    </div>
    <form class="form-horizontal w-100" action="{{ route('cms.acf.field.update', $field->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div id="fields-container">
                <div class="field-group border rounded p-3 mb-3 bg-white">
                    <div class="row mb-2">
                        <div class="col-md-4 mb-2">
                            <select name="type" class="form-control select2-with-icons">
                                @foreach (custom_fields() as $f)
                                    <option value="{{ $f['value'] }}" data-icon="{{ $f['icon'] }}"
                                        @if ($f['value'] == $field->type) selected @endif>
                                        {{ $f['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-2">
                            <input type="text" name="label" class="form-control" placeholder="Label" required value="{{ $field->label }}">
                        </div>
                        <div class="col-md-4 mb-2">
                            <input type="text" name="name" class="form-control" placeholder="Name" required value="{{ $field->name }}">
                        </div>
                    </div>

                    <div class="row align-items-center">
                        <div class="col-md-4 mb-2 choices-textarea d-none">
                            <textarea name="choices" class="form-control" placeholder="{{ translate('Enter choices separated by commas') }}">{{ $field->choices }}</textarea>
                        </div>
                        <div class="col-md-4 mb-2 default-value-group">
                            <input type="text" name="default_input" class="form-control default-value-input" placeholder="Default value" value="{{ $field->default_values }}">
                            <textarea name="default_textarea" class="form-control default-value-textarea d-none" placeholder="Enter default values separated by commas">{{ $field->default_values }}</textarea>
                        </div>
                        <div class="col-md-2 mb-2 select-multiple-toggle d-none">
                            <div class="form-check form-switch">
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input type="checkbox" name="multiple" value="1" @if ($field->select_multiple) checked @endif>
                                    <span></span> {{ translate('Select Multiple') }}
                                </label>
                            </div>
                        </div>
                        <div class="col-md-2 mb-2">
                            <div class="form-check form-switch">
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input type="checkbox" name="validation" value="1" @if ($field->is_required) checked @endif>
                                    <span></span> {{ translate('Required') }}
                                </label>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">{{ translate('Update') }}</button>
            <a href="{{ route('cms.acf.index') }}" class="btn btn-outline-primary">{{ translate('Cancel') }}</a>
        </div>
    </form>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        function formatState(state) {
            if (!state.id) return state.text;
            return $('<span><i class="' + $(state.element).data('icon') + ' mr-2"></i>' + state.text + '</span>');
        }

        $('.select2-with-icons').select2({
            templateResult: formatState,
            templateSelection: formatState,
            width: '100%'
        });

        $('select[name="type"]').on('change', function() {
            let fieldGroup = $(this).closest('.field-group');
            let selectedType = $(this).val();

            if (selectedType === 'select') {
                fieldGroup.find('.choices-textarea').removeClass('d-none');
                fieldGroup.find('.select-multiple-toggle').removeClass('d-none');
                fieldGroup.find('.default-value-input').addClass('d-none').val('');
                fieldGroup.find('.default-value-textarea').removeClass('d-none');
            } else {
                fieldGroup.find('.choices-textarea').addClass('d-none').find('textarea').val('');
                fieldGroup.find('.select-multiple-toggle').addClass('d-none').find('input[type="checkbox"]').prop('checked', false);
                fieldGroup.find('.default-value-input').removeClass('d-none');
                fieldGroup.find('.default-value-textarea').addClass('d-none').val('');
            }
        });

        $('select[name="type"]').trigger('change');
    });
</script>
@endsection
