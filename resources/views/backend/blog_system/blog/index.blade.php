@extends('backend.layouts.app')

@section('content')

@section('title')
{{ translate('All Blogs & News') }}
@endsection

@php
$customRenderers = [
    'category' => fn($item) => $item->category ? $item->category->category_name : '—',
    'publish_status' => fn($item) => '<label class="aiz-switch aiz-switch-success mb-0">
                                    <input type="checkbox" 
                                        @can("publish_blog") onchange="change_status(this)" @endcan
                                        value="' . $item->id . '" 
                                        ' . ($item->status == 1 ? 'checked' : '') . '
                                        @cannot("publish_blog") disabled @endcan
                                    >
                                    <span></span>
                                </label>',
];
@endphp

<x-list-table
    :tableHeaders="['Title', 'Category','Short Description','Status']"
    :tableKeys="['title', 'category','short_description','publish_status']"
    :translatableKeys="[]"
    :tableData="$blogs"
    title="All Blogs & News"
    addRoute="blog.create"
    showRoute="#"
    editRoute="blog.edit"
    deleteRoute="blog.destroy"
    :editParams="fn($item) => [$item->id, 'lang' => env('DEFAULT_LANGUAGE')]"
    :deleteParams="fn($item) => [$item->id]"
    :showParams="fn($item) => [$item->id]"
	:permissions="[
        'show' => '',
        'edit' => 'edit_blog',
        'delete' => 'delete_blog'
    ]"
    :customRenderers="$customRenderers"
/>

@endsection

@section('modal')
    @include('backend.layouts.components.modals.delete_modal')
@endsection


@section('script')

    <script type="text/javascript">
        function change_status(el){
            var status = 0;
            if(el.checked){
                var status = 1;
            }
            $.post('{{ route('blog.change-status') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '{{ translate('Change blog status successfully') }}');
                }
                else{
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }
    </script>

@endsection
