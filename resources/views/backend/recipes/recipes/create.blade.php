@extends('backend.layouts.app')

@section('content')

<x-dynamic-form 
    :action="route('recipe.store')" 
    method="POST" 
    :submitLabel="'Save'" 
    title="Save Recipe"
    cancelRoute="recipe.index"
    :fields="[
        ['label' => 'Recipe Title', 'name' => 'title', 'type' => 'text', 'required' => true, 'options' => ['onkeyup' => 'makeSlug(this.value)']],
        ['label' => 'Category', 'name' => 'category_id', 'type' => 'select', 'required' => true, 'options' => $recipe_categories->map(fn($category) => ['value' => $category->id, 'label' => $category->category_name])->toArray()],
        ['label' => 'Slug', 'name' => 'slug', 'type' => 'text', 'required' => true],
        ['label' => 'Banner', 'name' => 'banner', 'type' => 'image', 'note' => '(1300x650)'],
        ['label' => 'Calories', 'name' => 'calories', 'type' => 'text', 'required' => true, 'col' => 'col-md-12'],
        ['label' => 'Time To Make', 'name' => 'time_make', 'type' => 'text', 'required' => true, 'col' => 'col-md-12'],
        ['label' => 'Description', 'name' => 'description', 'type' => 'textarea', 'extra_class' => 'aiz-text-editor', 'col' => 'col-md-12'],
        ['label' => 'Meta Title', 'name' => 'meta_title', 'type' => 'text'],
        ['label' => 'Meta Image', 'name' => 'meta_img', 'type' => 'image', 'note' => '(200x200)+'],
        ['label' => 'Meta Description', 'name' => 'meta_description', 'type' => 'textarea', 'attributes' => ['rows' => 5]],
        ['label' => 'Meta Keywords', 'name' => 'meta_keywords', 'type' => 'text'],
    ]"
/>

@endsection

@section('script')
<script>
    function makeSlug(val) {
        let str = val;
        let output = str.replace(/\s+/g, '-').toLowerCase();
        $('#slug').val(output);
    }
</script>
@endsection
