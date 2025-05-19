@props([
    'title' => '',
    'action' => null,
    'method' => 'POST',
    'enctype' => null,
    'cancelRoute' => null,
    'submitLabel' => 'Save',
    'fields' => [],
])
<div class="card statistcs_card add_form m-0 mb-3">
    <div class="card-header">
        <span>{{ translate($title) }}</span>
    </div>
    <form class="form-horizontal w-100" action="{{ $action }}"
        method="{{ in_array(strtoupper($method), ['GET']) ? 'GET' : 'POST' }}"
        @if ($enctype) enctype="{{ $enctype }}" @endif>
        <div class="card-body">
            @csrf
            @if (in_array(strtoupper($method), ['PUT', 'PATCH', 'DELETE']))
                @method($method)
            @endif
            <div class="row">
                @foreach ($fields as $field)
                    @php
                        $fieldName = $field['name'];
                        $fieldValue = old($fieldName) ?? ($field['value'] ?? null);
                        $isRequired = !empty($field['required']) ? 'required' : '';
                        $extraClass = $field['extra_class'] ?? '';
                        $options = $field['options'] ?? [];
                    @endphp
                    @if ($field['type'] === 'hidden')
                        <input type="hidden" name="{{ $fieldName }}" value="{{ $fieldValue }}">
                        @continue
                    @endif
                    <div class="form-group {{ $field['col'] ?? 'col-md-6' }}">
                        @if (in_array($field['type'], ['text', 'number']))
                            <input type="{{ $field['type'] }}" name="{{ $fieldName }}" id="{{ $fieldName }}"
                                class="form-control {{ $extraClass }}"
                                placeholder="{{ translate($field['label']) }}" value="{{ $fieldValue }}"
                                {{ $isRequired }}
                                @foreach ($options as $attr => $val)
                                    @if (is_scalar($val))
                                        {{ $attr }}="{{ e($val) }}"
                                    @endif
                                @endforeach>
                            @if (!empty($field['hint']))
                                <small class="text-muted">{{ translate($field['hint']) }}</small>
                            @endif
                        @elseif ($field['type'] === 'textarea')
                            <textarea name="{{ $fieldName }}" rows="{{ $field['rows'] ?? 5 }}" class="form-control {{ $extraClass }}"
                                placeholder="{{ translate($field['label']) }}" {{ $isRequired }}
                                @foreach ($options as $attr => $val)
            @if (is_scalar($val))
            {{ $attr }}="{{ e($val) }}"
            @endif @endforeach>{{ $fieldValue }}</textarea>
                        @elseif ($field['type'] === 'select')
                            <select
                                class="form-control aiz-selectpicker {{ $field['class'] ?? '' }} @if (!empty($field['multiple'])) select2 @endif"
                                name="{{ $fieldName }}{{ !empty($field['multiple']) ? '[]' : '' }}"
                                id="{{ $fieldName }}" {{ !empty($field['multiple']) ? 'multiple' : '' }}
                                {{ $isRequired }}
                                
                                @foreach ($options as $attr => $val)
            @if (is_scalar($val))
            {{ $attr }}="{{ e($val) }}"
            @endif @endforeach>

                                <option value="">{{ translate('Select ' . $field['label']) }}</option>
                                @foreach ($field['options'] as $option)
                                    @php
                                        $selected = '';
                                        if (!empty($field['multiple']) && is_array($fieldValue)) {
                                            $selected = in_array($option['value'], $fieldValue) ? 'selected' : '';
                                        } elseif ($fieldValue == $option['value']) {
                                            $selected = 'selected';
                                        }
                                    @endphp
                                    <option value="{{ $option['value'] }}" {{ $selected }}>
                                        {{ $option['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        @elseif ($field['type'] === 'image')
                            <div class="input-group" data-toggle="aizuploader" data-type="image">
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-soft-secondary font-weight-medium">
                                        {{ translate('Browse') }}
                                    </div>
                                </div>
                                <div class="form-control file-amount">{{ translate('Add ' . $field['label']) }}</div>
                                <input type="hidden" name="{{ $fieldName }}" class="selected-files"
                                    value="{{ $fieldValue }}">
                            </div>
                            @if ($fieldValue)
                                <div class="file-preview box sm">
                                    <img src="{{ uploaded_asset($fieldValue) }}" style="width:50px;">
                                </div>
                            @endif
                            @if (!empty($field['hint']))
                                <small class="text-muted">{{ translate($field['hint']) }}</small>
                            @endif
                        @elseif ($field['type'] === 'checkboxes')
                            <label class="d-block">{{ translate($field['label']) }}</label>
                            <div class="row">
                                @foreach ($field['options'] as $option)
                                    <div class="form-check col-md-6" style=" display: flex; align-items: center; gap: 8px; ">
                                        <input class=""
                                            type="checkbox"
                                            name="{{ $fieldName }}[]"
                                            id="{{ $fieldName }}_{{ $option['value'] }}"
                                            value="{{ $option['value'] }}"
                                            @if (!empty($option['checked']) || (is_array(old($fieldName)) && in_array($option['value'], old($fieldName)))) checked @endif>
                                        <label class="form-check-label" for="{{ $fieldName }}_{{ $option['value'] }}">
                                            {{ $option['label'] }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>

                        @elseif ($field['type'] === 'file')
                            <input type="file" id="{{ $field['label'] }}" name="{{ $fieldName }}"
                                placeholder="{{ translate($field['label']) }}" class="form-control"
                                {{ $isRequired }}
                                @foreach ($options as $attr => $val)
            @if (is_scalar($val))
            {{ $attr }}="{{ e($val) }}"
            @endif @endforeach>
                            @if (!empty($field['hint']))
                                <small class="text-muted">{{ translate($field['hint']) }}</small>
                            @endif
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">{{ translate($submitLabel) }}</button>
            @if ($cancelRoute && $cancelRoute != '#')
                <a href="{{ route($cancelRoute) }}" class="btn btn-outline-primary">{{ translate('Cancel') }}</a>
            @endif
        </div>
    </form>
</div>
