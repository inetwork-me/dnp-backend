@extends('backend.layouts.app')

@section('title')
    {{ translate('New Field Group') }}
@endsection

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        .select2-container--classic.select2-container--focus,
        .select2-container--default.select2-container--focus {
            height: 49px;
        }

        .select2-container--default .select2-selection--single {
            background-color: #fff;
            border: 1px solid #dfdfe6;
            border-radius: 10px;
            height: 49px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 45px;
            position: absolute;
            top: 1px;
            right: 1px;
            width: 20px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #000;
            line-height: 45px;
        }

        .select2-container .select2-selection--single .select2-selection__rendered {
            display: block;
            padding-left: 14px;
            padding-right: 20px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .select2-container--classic .select2-results__options .select2-results__option[aria-selected=true],
        .select2-container--default .select2-results__options .select2-results__option[aria-selected=true] {
            background-color: #37143e;
            color: #fff;
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
            <span>{{ translate('New Field Group') }}</span>
        </div>
        <form class="form-horizontal w-100" action="{{ route('cms.acf.store') }}" method="POST">
            <div class="card-body">
                @csrf
                <!-- Field Group Name -->
                <div class="form-group">
                    <input type="text" name="name" class="form-control" required
                        placeholder="{{ translate('Field Group Name') }}">
                </div>

                <div class="form-group">
                    <textarea name="description" id="description" cols="30" rows="3" class="form-control"></textarea>
                </div>

                <!-- Custom Fields -->
                <hr>
                <h5>{{ translate('Fields') }}</h5>
                <div id="fields-container">
                    <div class="field-group border rounded p-3 mb-3 bg-white">

                        <div class="row mb-2">
                            <div class="col-md-4 mb-2">
                                <select name="fields[0][type]" class="form-control select2-with-icons">
                                    @foreach (custom_fields() as $field)
                                        <option value="{{ $field['value'] }}" data-icon="{{ $field['icon'] }}">
                                            {{ $field['label'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-2">
                                <input type="text" name="fields[0][label]" class="form-control" placeholder="Label"
                                    required>
                            </div>
                            <div class="col-md-4 mb-2">
                                <input type="text" name="fields[0][name]" class="form-control" placeholder="Name"
                                    required>
                            </div>
                        </div>

                        <div class="row align-items-center">
                            <div class="col-md-4 mb-2 choices-textarea d-none">
                                <textarea name="fields[0][choices]" class="form-control"
                                    placeholder="{{ translate('Enter choices separated by commas') . ' key=>value,key=>value' }}"></textarea>
                            </div>
                            <div class="col-md-4 mb-2 default-value-group">
                                <input type="text" name="fields[0][default]" class="form-control default-value-input"
                                    placeholder="Default value">
                                <textarea name="fields[0][default]" class="form-control default-value-textarea d-none"
                                    placeholder="Enter default values separated by commas"></textarea>
                            </div>
                            <div class="col-md-2 mb-2 select-multiple-toggle d-none">
                                <div class="form-check form-switch">
                                    <label class="aiz-switch aiz-switch-success mb-0">
                                        <input type="checkbox" name="fields[0][multiple]" id="multiple-toggle-0"
                                            value="1" id="validation-toggle-0">
                                        <span></span>
                                        {{ translate('Select Multiple') }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-2 mb-2">
                                <div class="form-check form-switch">
                                    <label class="aiz-switch aiz-switch-success mb-0">
                                        <input type="checkbox" name="fields[0][validation]" value="1"
                                            id="validation-toggle-0">
                                        <span></span>
                                        {{ translate('Required') }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-2 mb-2">
                                <button type="button" class="btn btn-danger w-100 remove-field-group">
                                    <svg fill="#fff" width="25" height="25" viewBox="0 0 36 36"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path class="clr-i-solid clr-i-solid-path-1"
                                            d="M6 9v22a2.93 2.93 0 0 0 2.86 3h18.23A2.93 2.93 0 0 0 30 31V9Zm9 20h-2V14h2Zm8 0h-2V14h2Z" />
                                        <path class="clr-i-solid clr-i-solid-path-2"
                                            d="M30.73 5H23V4a2 2 0 0 0-2-2h-6.2A2 2 0 0 0 13 4v1H5a1 1 0 1 0 0 2h25.73a1 1 0 0 0 0-2" />
                                        <path fill="none" d="M0 0h36v36H0z" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                    </div>
                </div>


                <hr>
                <button type="button" class="btn btn-primary btn-sm" id="add-field">{{ translate('Add Field') }}</button>

                <!-- Location Rules -->
                <hr>
                <h5>{{ translate('Location Rules') }}</h5>
                <div id="rules-container">
                    <div class="rule-group mb-3">
                        <div class="form-row">
                            <div class="col mb-3">
                                <select name="rules[0][object]" class="form-control">
                                    <option value="post_type">{{ translate('Post Type') }}</option>
                                    <option value="post">{{ translate('Post') }}</option>
                                    <option value="page">{{ translate('Page') }}</option>
                                    <!-- Add more -->
                                </select>
                            </div>
                            <div class="col mb-3">
                                <select name="rules[0][operator]" class="form-control">
                                    <option value="==">{{ translate('is equal to') }}</option>
                                    <option value="!=">{{ translate('is not equal to') }}</option>
                                </select>
                            </div>
                            <div class="col mb-3">
                                <select name="rules[0][value]" class="form-control">
                                    <!-- Dynamically populate based on object selection (JS required) -->
                                    <option value="example">{{ translate('Example') }}</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-3 remove-rule-and">
                                <button type="button" class="btn btn-danger btn-sm remove-rule">
                                    <svg fill="#fff" width="25" height="25" viewBox="0 0 36 36"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path class="clr-i-solid clr-i-solid-path-1"
                                            d="M6 9v22a2.93 2.93 0 0 0 2.86 3h18.23A2.93 2.93 0 0 0 30 31V9Zm9 20h-2V14h2Zm8 0h-2V14h2Z" />
                                        <path class="clr-i-solid clr-i-solid-path-2"
                                            d="M30.73 5H23V4a2 2 0 0 0-2-2h-6.2A2 2 0 0 0 13 4v1H5a1 1 0 1 0 0 2h25.73a1 1 0 0 0 0-2" />
                                        <path fill="none" d="M0 0h36v36H0z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm"
                    id="add-rule-group">{{ translate('Add rule group') }}</button>

            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">{{ translate('Save') }}</button>
                <a href="{{ route('cms.acf.index') }}" class="btn btn-outline-primary">{{ translate('Cancel') }}</a>
            </div>
        </form>
    @endsection


    @section('script')
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


        <script>
            $(document).ready(function() {
                function formatState(state) {
                    if (!state.id) {
                        return state.text;
                    }
                    let $state = $(
                        '<span><i class="' + $(state.element).data('icon') + ' mr-2"></i>' + state.text + '</span>'
                    );
                    return $state;
                }

                $('.select2-with-icons').select2({
                    templateResult: formatState,
                    templateSelection: formatState
                });

                function updateIndexes() {
                    $('#fields-container .field-group').each(function(index) {
                        $(this).find('select, input, textarea').each(function() {
                            let name = $(this).attr('name');
                            if (name) {
                                let newName = name.replace(/\[\d+\]/, '[' + index + ']');
                                $(this).attr('name', newName);
                            }

                            let id = $(this).attr('id');
                            if (id) {
                                let newId = id.replace(/\-\d+$/, '-' + index);
                                $(this).attr('id', newId);
                            }
                        });

                        $(this).find('input[type=checkbox]').each(function() {
                            let oldId = $(this).attr('id');
                            if (oldId) {
                                let newId = oldId.replace(/\-\d+$/, '-' + index);
                                $(this).attr('id', newId);
                                $(this).siblings('label').attr('for', newId);
                            }
                        });
                    });
                }

                $('#add-field').click(function() {
                    let lastField = $('#fields-container .field-group').last();

                    lastField.find('.select2-with-icons').select2('destroy');

                    let newField = lastField.clone();

                    lastField.find('.select2-with-icons').select2({
                        templateResult: formatState,
                        templateSelection: formatState
                    });

                    $('#fields-container').append(newField);

                    newField.find('input[type="text"], textarea').val('');
                    newField.find('input[type="checkbox"]').prop('checked', false);
                    newField.find('select').val(null).trigger('change');

                    updateIndexes();

                    newField.find('.select2-with-icons').select2({
                        templateResult: formatState,
                        templateSelection: formatState
                    });
                });

                $('#fields-container').on('click', '.remove-field-group', function() {
                    if ($('#fields-container .field-group').length > 1) {
                        $(this).closest('.field-group').remove();
                        updateIndexes();
                    } else {
                        alert('Can not delete last record');
                    }
                });

                $('#fields-container').on('change', 'select[name*="[type]"]', function() {
                    let fieldGroup = $(this).closest('.field-group');
                    let selectedType = $(this).val();


                    if (selectedType === 'select') {
                        fieldGroup.find('.choices-textarea').removeClass('d-none');
                        fieldGroup.find('.select-multiple-toggle').removeClass('d-none');
                    } else {
                        fieldGroup.find('.choices-textarea').addClass('d-none');
                        fieldGroup.find('.select-multiple-toggle').addClass('d-none');
                        fieldGroup.find('textarea[name*="[choices]"]').val('');
                        fieldGroup.find('input[name*="[multiple]"]').prop('checked', false);
                    }


                    if (selectedType === 'select') {
                        fieldGroup.find('.default-value-input').addClass('d-none').val('');
                        fieldGroup.find('.default-value-textarea').removeClass('d-none');
                    } else {
                        fieldGroup.find('.default-value-input').removeClass('d-none');
                        fieldGroup.find('.default-value-textarea').addClass('d-none').val('');
                    }
                });


                $('#fields-container select[name*="[type]"]').trigger('change');

            });
        </script>


        {{-- Add Group Rule --}}
        <script>
            function updateRuleNames() {
                $('#rules-container .rule-group').each(function(groupIndex) {
                    $(this).find('.form-row').each(function() {
                        $(this).find('select').each(function() {
                            const name = $(this).attr('name');
                            const matches = name.match(/rules\[\d+\]\[(\w+)\]/);
                            if (matches && matches[1]) {
                                const field = matches[1];
                                $(this).attr('name', `rules[${groupIndex}][${field}]`);
                            }
                        });
                    });
                });
            }

            $(document).ready(function() {
                $(document).on('click', '.add-and-rule', function() {
                    var ruleGroup = $(this).closest('.rule-group');
                    var lastRow = ruleGroup.find('.form-row').last();
                    var newRow = lastRow.clone();

                    newRow.find('select').val('');

                    ruleGroup.append(newRow);
                    updateRuleNames();
                });

                $(document).on('click', '.remove-rule', function() {
                    var ruleGroup = $(this).closest('.rule-group');
                    var ruleRow = $(this).closest('.form-row');

                    if (ruleGroup.find('.form-row').length === 1) {
                        ruleGroup.remove();
                    } else {
                        ruleRow.remove();
                    }
                    updateRuleNames();
                });

                $('#add-rule-group').on('click', function() {
                    var firstGroup = $('.rule-group').first();
                    var firstRow = firstGroup.find('.form-row').first().clone();
                    firstRow.find('select').val('');
                    var newGroup = $('<div class="rule-group mb-3"></div>').append(firstRow);
                    newGroup.find('h5').remove();

                    $('#rules-container').append(newGroup);
                    updateRuleNames();
                });

                updateRuleNames();
            });
        </script>

        {{-- Field Type --}}
        <script>
            $(document).ready(function() {
                $('.select2-with-icons').select2({
                    templateResult: formatOptionWithIcon,
                    templateSelection: formatOptionWithIcon,
                    width: '100%'
                });

                function formatOptionWithIcon(option) {
                    if (!option.id) return option.text;
                    const iconClass = $(option.element).data('icon');
                    return $('<span class="icon-option"><i class="' + iconClass + '"></i> ' + option.text + '</span>');
                }
            });
        </script>
    @endsection
