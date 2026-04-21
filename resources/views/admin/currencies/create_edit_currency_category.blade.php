@extends('admin.layout')

@section('admin-title')
    {{ $category->id ? 'Edit' : 'Create' }} Currency Category
@endsection

@section('admin-content')
    {!! breadcrumbs([
        'Admin Panel' => 'admin',
        'Currency Categories' => 'admin/data/currency-categories',
        ($category->id ? 'Edit' : 'Create') . ' Category' => $category->id ? 'admin/data/currency-categories/edit/' . $category->id : 'admin/data/currency-categories/create',
    ]) !!}

    <h1>{{ $category->id ? 'Edit' : 'Create' }} Currency Category
        @if ($category->id)
            <a href="#" class="btn btn-danger float-right delete-category-button">Delete Category</a>
        @endif
    </h1>

    {{ html()->form('POST', $category->id ? 'admin/data/currency-categories/edit/' . $category->id : 'admin/data/currency-categories/create')->acceptsFiles()->open() }}

    <h3>Basic Information</h3>

    <div class="form-group">
        {{ html()->label('Name') }}
        {{ html()->text('name', $category->name)->class('form-control') }}
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

    <div class="form-group">
        {{ html()->checkbox('is_visible', $category->id ? $category->is_visible : 1, 1)->class('form-check-input')->attribute('data-toggle', 'toggle') }}
        {{ html()->label('Is Visible', 'is_visible')->class('form-check-label ml-3') }} {!! add_help('If turned off, the category will not be visible in the category list or available for selection in search. Permissioned staff will still be able to add currencies to them, however.') !!}
    </div>

    <div class="text-right">
        {{ html()->submit($category->id ? 'Edit' : 'Create')->class('btn btn-primary') }}
    </div>

    {{ html()->form()->close() }}

    @if ($category->id)
        <h3>Preview</h3>
        <div class="card mb-3">
            <div class="card-body">
                @include('world._entry', ['imageUrl' => $category->categoryImageUrl, 'name' => $category->displayName, 'description' => $category->parsed_description, 'category' => $category, 'visible' => $category->is_visible])
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
                loadModal("{{ url('admin/data/currency-categories/delete') }}/{{ $category->id }}", 'Delete Category');
            });
        });
    </script>
@endsection
