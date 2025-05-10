@extends('backend.layouts.app')

@section('title')
{{translate('Edit Recipe Category')}}
@endsection

@section('content')

<ul class="nav nav-tabs nav-fill language-bar">
    @foreach (get_all_active_language() as $key => $language)
        <li class="nav-item">
            <a class="nav-link text-reset @if ($language->code == $lang) active @endif py-3" href="{{ route('recipe-category.edit', ['recipe_category'=>$cateogry->id, 'lang'=> $language->code] ) }}">
                <img src="{{ static_asset('assets/img/flags/'.$language->code.'.png') }}" height="11" class="mr-1">
                <span>{{ $language->name }}</span>
            </a>
        </li>
  @endforeach
</ul>

<x-dynamic-form 
    title="Edit Recipe Category" 
    action="{{ route('recipe-category.update', $cateogry->id) }}" 
    method="PATCH" 
    submitLabel="Save"
    :fields="[
        ['type' => 'hidden', 'name' => 'lang', 'value' => $lang],
        [
            'name' => 'category_name',
            'type' => 'text',
            'label' => 'Name',
            'value' => $cateogry->getTranslation('category_name',$lang),
            'required' => true,
            'col' => 'col-md-12',
            'options' => [
                'id' => 'category_name',
                'placeholder' => translate('Name')
            ]
        ]
    ]"
/>
@endsection
