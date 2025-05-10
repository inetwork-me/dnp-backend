@extends('backend.layouts.app')

@section('title')
    {{ translate('New Category') }}
@endsection

@section('content')
<x-dynamic-form 
    :action="route('categories.store')" 
    method="POST" 
    title="New Category"
    enctype="multipart/form-data"
    :submitLabel="'Save'"
    cancelRoute="categories.index"
    :fields="[
        ['label' => 'Name', 'name' => 'name', 'type' => 'text', 'required' => true],
        ['label' => 'Type', 'name' => 'digital', 'type' => 'select', 'options' => [
            ['value' => 0, 'label' => 'Physical'],
            ['value' => 1, 'label' => 'Digital']
        ]],
        ['label' => 'Parent Category', 'name' => 'parent_id', 'type' => 'select', 'options' => $categoryOptions],
        ['label' => 'Ordering Number', 'name' => 'order_level', 'type' => 'number', 'hint' => 'Higher number has high priority'],
        ['label' => 'Banner', 'name' => 'banner', 'type' => 'image', 'hint' => 'Minimum: 150x150px'],
        ['label' => 'Icon', 'name' => 'icon', 'type' => 'image', 'hint' => 'Minimum: 16x16px'],
        ['label' => 'Cover Image', 'name' => 'cover_image', 'type' => 'image', 'hint' => 'Minimum: 260x260px'],
        ['label' => 'Meta Title', 'name' => 'meta_title', 'type' => 'text'],
        ['label' => 'Meta Description', 'name' => 'meta_description', 'type' => 'textarea'],
        ['label' => 'Commission Rate', 'name' => 'commision_rate', 'type' => 'number'],
        ['label' => 'Filtering Attributes', 'name' => 'filtering_attributes', 'type' => 'select', 'multiple' => true, 'options' => $attributeOptions],
    ]"
/>

@endsection

@section('script')
    <script type="text/javascript">
        function categoriesByType(val) {
            $('select[name="parent_id"]').html('');
            AIZ.plugins.bootstrapSelect('refresh');
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: '{{ route('categories.categories-by-type') }}',
                data: {
                    digital: val
                },
                success: function(data) {
                    $('select[name="parent_id"]').html(data);
                    AIZ.plugins.bootstrapSelect('refresh');
                }
            });
        }
    </script>
@endsection
