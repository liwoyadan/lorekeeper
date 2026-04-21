@extends('admin.layout')

@section('admin-title')
    {{ $page->id ? 'Edit' : 'Create' }} Page
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Pages' => 'admin/pages', ($page->id ? 'Edit' : 'Create') . ' Page' => $page->id ? 'admin/pages/edit/' . $page->id : 'admin/pages/create']) !!}

    <h1>{{ $page->id ? 'Edit' : 'Create' }} Page
        @if ($page->id && !config('lorekeeper.text_pages.' . $page->key))
            <a href="#" class="btn btn-danger float-right delete-page-button">Delete Page</a>
        @endif
        @if ($page->id)
            <a href="#" class="btn btn-secondary float-right regen-page-button mr-md-2">Regenerate Page</a>
            <a href="{{ $page->url }}" class="btn btn-info float-right mr-md-2">View Page</a>
        @endif
    </h1>

    {{ html()->form('POST', $page->id ? 'admin/pages/edit/' . $page->id : 'admin/pages/create')->acceptsFiles()->open() }}

    <h3>Basic Information</h3>

    <div class="row">
        <div class="col-md-6 form-group">
            {{ html()->label('Title') }}
            {{ html()->text('title', $page->title)->class('form-control') }}
        </div>

        <div class="col-md-6 form-group">
            {{ html()->label('Key') }} {!! add_help('This is a unique name used to form the URL of the page. Only alphanumeric characters, dash and underscore (no spaces) can be used.') !!}
            {{ html()->text('key', $page->key)->class('form-control') }}
        </div>
    </div>

    <div class="form-group">
        {{ html()->label('Header Image (Optional)') }} {!! add_help('This image will show up above the page content and on the meta-image.') !!}
        <div class="custom-file">
            {{ html()->label('Choose file...', 'image')->class('custom-file-label') }}
            {{ html()->file('image')->class('custom-file-input') }}
        </div>
        @if ($page->has_image)
            <div class="form-check">
                {{ html()->checkbox('remove_image', false, 1)->class('form-check-input') }}
                {{ html()->label('Remove current image', 'remove_image')->class('form-check-label') }}
            </div>
        @endif
    </div>

    <div class="form-group">
        {{ html()->label('Page Content') }}
        {{ html()->textarea('text', $page->text)->class('form-control wysiwyg') }}
    </div>

    <div class="row">
        <div class="col-md-4 form-group">
            {{ html()->checkbox('is_visible', $page->id ? $page->is_visible : 1, 1)->class('form-check-input')->attribute('data-toggle', 'toggle') }}
            {{ html()->label('Is Viewable', 'is_visible')->class('form-check-label ml-3') }} {!! add_help('If this is turned off, users will not be able to view the page even if they have the link to it.') !!}
        </div>

        <div class="col-md-4 form-group">
            {{ html()->checkbox('can_comment', $page->id ? $page->can_comment : 0, 1)->class('form-check-input')->attribute('data-toggle', 'toggle') }}
            {{ html()->label('Commentable', 'can_comment')->class('form-check-label ml-3') }} {!! add_help('If this is turned on, users will be able to comment on the page.') !!}
            @if (!Settings::get('comment_dislikes_enabled'))
                <div class="form-group">
                    {{ html()->checkbox('allow_dislikes', $page->id ? $page->allow_dislikes : 0, 1)->class('form-check-input')->attribute('data-toggle', 'toggle') }}
                    {{ html()->label('Allow Dislikes On Comments?', 'allow_dislikes')->class('form-check-label ml-3') }} {!! add_help('If this is turned off, users cannot dislike comments.') !!}
                </div>
            @endif
        </div>
    </div>

    <div class="text-right">
        {{ html()->submit($page->id ? 'Edit' : 'Create')->class('btn btn-primary') }}
    </div>

    {{ html()->form()->close() }}
@endsection

@section('scripts')
    @parent
    @include('js._tinymce_wysiwyg')
    <script>
        $(document).ready(function() {
            $('.delete-page-button').on('click', function(e) {
                e.preventDefault();
                loadModal("{{ url('admin/pages/delete') }}/{{ $page->id }}", 'Delete Page');
            });
            $('.regen-page-button').on('click', function(e) {
                e.preventDefault();
                loadModal("{{ url('admin/pages/regen') }}/{{ $page->id }}", 'Regenerate Page');
            });
        });
    </script>
@endsection
