@extends('admin.layout')

@section('admin-title')
    {{ $category->id ? 'Edit' : 'Create' }} Prompt Category
@endsection

@section('admin-content')
    {!! breadcrumbs([
        'Admin Panel' => 'admin',
        'Prompt Categories' => 'admin/data/prompt-categories',
        ($category->id ? 'Edit' : 'Create') . ' Category' => $category->id ? 'admin/data/prompt-categories/edit/' . $category->id : 'admin/data/prompt-categories/create',
    ]) !!}

    <h1>{{ $category->id ? 'Edit' : 'Create' }} Prompt Category
        @if ($category->id)
            <a href="#" class="btn btn-danger float-right delete-category-button">Delete Category</a>
        @endif
    </h1>

    {{ html()->form('POST', $category->id ? 'admin/data/prompt-categories/edit/' . $category->id : 'admin/data/prompt-categories/create')->acceptsFiles()->open() }}

    <h3>Basic Information</h3>

    <div class="row">
        <div class="form-group col-md-6">
            {{ html()->label('Name') }}
            {{ html()->text('name', $category->name)->class('form-control') }}
        </div>
        <div class="form-group col-md-6">
            {{ html()->label('Parent Category (Optional)') }}
            {{ html()->select('parent_id', $categories, $category->parent_id)->class('form-control')->placeholder('Select Parent Category') }}
        </div>
    </div>

    <div class="form-group">
        {{ html()->label('World Page Image (Optional)') }} {!! add_help('This image is used only on the world information pages.') !!}
        <div class="custom-file">
            {{ html()->label('Choose file...', 'image')->class('custom-file-label') }}
            {{ html()->file('image')->class('custom-file-input') }}
        </div>
        <div class="text-muted">Recommended size: 200px x 200px</div>
        @if ($category->has_image)
            <div class="form-check">
                {{ html()->checkbox('remove_image', false, 1)->class('form-check-input') }}
                {{ html()->label('Remove current image', 'remove_image')->class('form-check-label') }}
            </div>
        @endif
    </div>

    <div class="form-group">
        {{ html()->label('Description (Optional)') }}
        {{ html()->textarea('description', $category->description)->class('form-control wysiwyg') }}
    </div>

    <div class="text-right">
        {{ html()->submit($category->id ? 'Edit' : 'Create')->class('btn btn-primary') }}
    </div>

    {{ html()->form()->close() }}

    @if ($category->id)
        <h3>Preview</h3>
        <div class="card mb-3">
            <div class="card-body">
                @include('prompts._entry', [
                    'category' => $category,
                ])
            </div>
        </div>
    @endif
@endsection

@section('scripts')
    @parent
    @include('js._tinymce_wysiwyg')
    <script>
        $(document).ready(function() {
            $('.delete-category-button').on('click', function(e) {
                e.preventDefault();
                loadModal("{{ url('admin/data/prompt-categories/delete') }}/{{ $category->id }}", 'Delete Category');
            });
        });
    </script>
@endsection
