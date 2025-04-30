<option value="{{ $child_category->id }}" 
    @if(in_array($child_category->id, $selected_ids)) selected @endif>
    {{ str_repeat('--', $child_category->level) }} {{ $child_category->getTranslation('name') }}
</option>

@if ($child_category->childrenCategories->count())
    @foreach ($child_category->childrenCategories as $subCategory)
        @include('backend.product.products.child_category_dropdown', [
            'child_category' => $subCategory,
            'selected_ids' => $selected_ids
        ])
    @endforeach
@endif
