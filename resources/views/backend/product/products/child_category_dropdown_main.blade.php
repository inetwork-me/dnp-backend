<option value="{{ $child_category->id }}"
    @if($selected_main == $child_category->id) selected @endif>
    {{ str_repeat('--', $child_category->level) }} {{ $child_category->getTranslation('name') }}
</option>

@if ($child_category->childrenCategories->count())
    @foreach ($child_category->childrenCategories as $subCategory)
        @include('backend.product.products.child_category_dropdown_main', [
            'child_category' => $subCategory,
            'selected_main' => $selected_main
        ])
    @endforeach
@endif
