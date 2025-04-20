<option value="0">{{ translate('No Parent') }}</option>
@foreach ($categories as $p_category)
    <option value="{{ $p_category->id }}">{{ $p_category->getTranslation('name') }}</option>
    @foreach ($p_category->childrenCategories as $childCategory)
        @include('categories.child_category', ['child_category' => $childCategory])
    @endforeach
@endforeach
