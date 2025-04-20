@php
    $value = null;
    for ($i=0; $i < $child_category->level; $i++){
        $value .= '--';
    }
    $child_categories = $child_category->categories->whereNotIn('id', App\Utility\CategoryUtility::children_ids($category->id, true))->where('id', '!=' , $category->id);
@endphp
<option value="{{ $child_category->id }}">{{ $value." ".$child_category->getTranslation('name') }}</option>
@if (count($child_categories)>0)
    @foreach ($child_categories as $childCategory)
        @include('categories.child_category', ['child_category' => $childCategory])
    @endforeach
@endif
