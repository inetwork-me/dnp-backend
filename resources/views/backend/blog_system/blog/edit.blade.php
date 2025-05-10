@extends('backend.layouts.app')

@section('content')

@php
    $categoryOptions = $blog_categories->map(function ($category) {
        return [
            'value' => $category->id,
            'label' => $category->category_name,
        ];
    })->toArray();
@endphp

<ul class="nav nav-tabs nav-fill language-bar">
    @foreach (get_all_active_language() as $key => $language)
        <li class="nav-item">
            <a class="nav-link text-reset @if ($language->code == $lang) active @endif py-3" href="{{ route('blog.edit', ['id'=>$blog->id, 'lang'=> $language->code] ) }}">
                <img src="{{ static_asset('assets/img/flags/'.$language->code.'.png') }}" height="11" class="mr-1">
                <span>{{ $language->name }}</span>
            </a>
        </li>
  @endforeach
</ul>

<x-dynamic-form 
    :action="route('blog.update', $blog->id)" 
    method="PATCH" 
    :submitLabel="'Save'" 
    title="Edit Blog"
    cancelRoute="blog.index"
    :fields="[
        ['type' => 'hidden', 'name' => 'lang', 'value' => $lang],
        ['label' => 'Blog Title', 'name' => 'title', 'type' => 'text', 'required' => true, 'value' => $blog->getTranslation('title', $lang),'options' => [ 'onkeyup' => 'makeSlug(this.value)' ]],
        ['label' => 'Category', 'name' => 'category_id', 'type' => 'select', 'required' => true, 'options' => $categoryOptions, 'value' => optional($blog->category)->id],
        ['label' => 'Slug', 'name' => 'slug', 'type' => 'text', 'required' => true, 'value' => $blog->getTranslation('slug', $lang)],
        ['label' => 'Banner', 'name' => 'banner', 'type' => 'image', 'value' => $blog->banner, 'note' => '(1300x650)'],
        ['label' => 'Short Description', 'name' => 'short_description', 'type' => 'textarea', 'required' => true, 'value' => $blog->getTranslation('short_description', $lang), 'col' => 'col-md-12'],
        ['label' => 'Description', 'name' => 'description', 'type' => 'textarea', 'value' => $blog->getTranslation('description', $lang), 'extra_class' => 'aiz-text-editor', 'col' => 'col-md-12'],
        ['label' => 'Meta Title', 'name' => 'meta_title', 'type' => 'text', 'value' => $blog->getTranslation('meta_title', $lang)],
        ['label' => 'Meta Image', 'name' => 'meta_img', 'type' => 'image', 'value' => $blog->meta_img, 'note' => '(200x200)+'],
        ['label' => 'Meta Description', 'name' => 'meta_description', 'type' => 'textarea', 'value' => $blog->getTranslation('meta_description', $lang), 'attributes' => ['rows' => 5]],
        ['label' => 'Meta Keywords', 'name' => 'meta_keywords', 'type' => 'text', 'value' => $blog->getTranslation('meta_keywords', $lang)],
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
