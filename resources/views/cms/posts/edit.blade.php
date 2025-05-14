@extends('backend.layouts.app')

@section('title')
    {{ translate('Edit Post Type') }}
@endsection

@section('content')
    <ul class="nav nav-tabs nav-fill language-bar">
        @foreach (get_all_active_language() as $key => $language)
            <li class="nav-item">
                <a class="nav-link text-reset @if ($language->code == $lang) active @endif py-3"
                    href="{{ route('cms.cpt.edit', [$postType->id, 'lang' => $language->code]) }}">
                    <img src="{{ static_asset('assets/img/flags/' . $language->code . '.png') }}" height="11" class="mr-1">
                    <span>{{ $language->name }}</span>
                </a>
            </li>
        @endforeach
    </ul>

    <x-dynamic-form :action="route('cms.cpt.update', $postType)" method="PUT" title="Edit Post Type" :submitLabel="'Update'"
        cancelRoute="cms.cpt.index" :fields="[
            ['label' => 'Slug', 'name' => 'slug', 'type' => 'text', 'required' => true, 'value' => $postType->slug],
            [
                'label' => 'Plural Label',
                'name' => 'plural_label',
                'type' => 'text',
                'required' => true,
                'value' =>  $postType->getTranslation('plural_label', $lang),
            ],
            [
                'label' => 'Singular Label',
                'name' => 'singular_label',
                'type' => 'text',
                'required' => true,
                'value' => $postType->getTranslation('singular_label', $lang),
            ],
            [
                'label' => 'Menu Name',
                'name' => 'menu_name',
                'type' => 'text',
                'required' => false,
                'value' => $postType->getTranslation('menu_name', $lang),
            ],
            [
                'label' => 'Menu Icon',
                'name' => 'icon',
                'type' => 'image',
                'hint' => 'Recommend Icon Size (28x28)',
                'value' => $postType->icon,
                'required' => false,
                'col' => 'col-md-12',
            ],
            [
                'label' => 'Description',
                'name' => 'description',
                'type' => 'textarea',
                'required' => false,
                'value' => $postType->getTranslation('description', $lang),
                'col' => 'col-md-12',
            ],
            [
                'label' => 'Supports',
                'name' => 'supports',
                'type' => 'checkboxes',
                'required' => false,
                'options' => [
                    ['value' => 'title', 'label' => translate('Title'), 'checked' => in_array('title', $postType->supports)],
                    ['value' => 'editor', 'label' => translate('Editor'), 'checked' => in_array('editor', $postType->supports)],
                    [
                        'value' => 'featured_image',
                        'label' => translate('Featured Image'),
                        'checked' => in_array('featured_image', $postType->supports),
                    ],
                    ['value' => 'excerpt', 'label' => translate('Excerpt'), 'checked' => in_array('excerpt', $postType->supports)],
                    [
                        'value' => 'trackbacks',
                        'label' => translate('Trackbacks'),
                        'checked' => in_array('trackbacks', $postType->supports),
                    ],
                    [
                        'value' => 'custom_fields',
                        'label' => translate('Custom Fields'),
                        'checked' => in_array('custom_fields', $postType->supports),
                    ],
                    [
                        'value' => 'comments',
                        'label' => translate('Comments'),
                        'checked' => in_array('comments', $postType->supports),
                    ],
                    [
                        'value' => 'revisions',
                        'label' => translate('Revisions'),
                        'checked' => in_array('revisions', $postType->supports),
                    ],
                    ['value' => 'author', 'label' => 'Author', 'checked' => in_array('author', $postType->supports)],
                    [
                        'value' => 'page_attributes',
                        'label' => translate('Page Attributes'),
                        'checked' => in_array('page_attributes', $postType->supports),
                    ],
                    [
                        'value' => 'post_formats',
                        'label' => translate('Post Formats'),
                        'checked' => in_array('post_formats', $postType->supports),
                    ],
                    ['value' => 'none', 'label' => 'None', 'checked' => in_array('none', $postType->supports)],
                ],
                'col' => 'col-md-12',
            ],
        ]" />
@endsection
