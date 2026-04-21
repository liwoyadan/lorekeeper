@extends('admin.layout')

@section('admin-title')
    Add Item Tag
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Items' => 'admin/data/items', 'Edit Item' => 'admin/data/items/edit/' . $item->id, 'Add Item Tag' => 'admin/data/items/tag/' . $item->id]) !!}

    <h1>Add Item Tag</h1>

    <p>Select an item tag to add to the item. You cannot add duplicate tags to the same item (they are removed from the selection). You will be taken to the parameter editing page after adding the tag. </p>

    {{ html()->form('POST', 'admin/data/items/tag/' . $item->id)->open() }}

    <div class="form-group">
        {{ html()->label('Tag', 'tag') }}
        {{ html()->select('tag', $tags, null)->class('form-control')->placeholder('Select a Tag') }}
    </div>

    <div class="text-right">
        {{ html()->submit('Add Tag')->class('btn btn-primary') }}
    </div>

    {{ html()->form()->close() }}
@endsection
