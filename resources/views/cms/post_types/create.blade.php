@extends('backend.layouts.app')

@section('title')
{{ translate('New Post Type') }}
@endsection

@section('content')
    <x-dynamic-form
        :action="route('cms.cpt.store')"
        method="POST"
        title="New Post Type"
        :submitLabel="'Save'"
        cancelRoute="cms.cpt.index"
        :fields="[
            ['label' => 'Slug', 'name' => 'slug', 'type' => 'text', 'required' => true],
            ['label' => 'Plural Label', 'name' => 'plural_label', 'type' => 'text', 'required' => true],
            ['label' => 'Singular Label', 'name' => 'singular_label', 'type' => 'text', 'required' => true],
            ['label' => 'Menu Name', 'name' => 'menu_name', 'type' => 'text', 'required' => false],
            ['label' => 'Menu Icon', 'name' => 'icon', 'type' => 'image', 'hint' => 'Recommend Icon Size (28x28)', 'required' => false,'col'=>'col-md-12'],
            ['label' => 'Description', 'name' => 'description', 'type' => 'textarea', 'required' => false,'col'=>'col-md-12'],
            [
                'label' => 'Supports',
                'name' => 'supports',
                'type' => 'checkboxes',
                'required' => false,
                'options' => [
                    ['value' => 'title', 'label' => 'Title'],
                    ['value' => 'editor', 'label' => 'Editor'],
                    ['value' => 'featured_image', 'label' => 'Featured Image'],
                    ['value' => 'excerpt', 'label' => 'Excerpt'],
                    ['value' => 'trackbacks', 'label' => 'Trackbacks'],
                    ['value' => 'custom_fields', 'label' => 'Custom Fields'],
                    ['value' => 'comments', 'label' => 'Comments'],
                    ['value' => 'revisions', 'label' => 'Revisions'],
                    ['value' => 'author', 'label' => 'Author'],
                    ['value' => 'page_attributes', 'label' => 'Page Attributes'],
                    ['value' => 'post_formats', 'label' => 'Post Formats'],
                    ['value' => 'none', 'label' => 'None'],
                ],
                'col'=>'col-md-12'
            ],
        ]"
    />
@endsection
