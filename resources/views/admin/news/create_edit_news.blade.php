@extends('admin.layout')

@section('admin-title')
    {{ $news->id ? 'Edit' : 'Create' }} News Post
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'News' => 'admin/news', ($news->id ? 'Edit' : 'Create') . ' Post' => $news->id ? 'admin/news/edit/' . $news->id : 'admin/news/create']) !!}

    <h1>{{ $news->id ? 'Edit' : 'Create' }} News Post
        @if ($news->id)
            <a href="#" class="btn btn-danger float-right delete-news-button">Delete Post</a>
            <a href="#" class="btn btn-secondary float-right regen-news-button mr-md-2">Regenerate Post</a>
            <a href="{{ $news->url }}" class="btn btn-info float-right mr-md-2">View Post</a>
        @endif
    </h1>

    {{ html()->form('POST', $news->id ? 'admin/news/edit/' . $news->id : 'admin/news/create')->acceptsFiles()->open() }}

    <h3>Basic Information</h3>

    <div class="row">
        <div class="col-md-6 form-group">
            {{ html()->label('Title') }}
            {{ html()->text('title', $news->title)->class('form-control') }}
        </div>

        <div class="col-md-6 form-group">
            {{ html()->label('Post Time (Optional)') }} {!! add_help('This is the time that the news post should be posted. Make sure the Is Viewable switch is off.') !!}
            {{ html()->text('post_at', $news->post_at)->class('form-control datepicker') }}
        </div>
    </div>

    <div class="form-group">
        {{ html()->label('Header Image (Optional)') }} {!! add_help('This image will show up above the news content and on the meta-image.') !!}
        <div class="custom-file">
            {{ html()->label('Choose file...', 'image')->class('custom-file-label') }}
            {{ html()->file('image')->class('custom-file-input') }}
        </div>
        @if ($news->has_image)
            <div class="form-check">
                {{ html()->checkbox('remove_image', false, 1)->class('form-check-input') }}
                {{ html()->label('Remove current image', 'remove_image')->class('form-check-label') }}
            </div>
        @endif
    </div>

    <div class="form-group">
        {{ html()->label('Post Content') }}
        {{ html()->textarea('text', $news->text)->class('form-control wysiwyg') }}
    </div>

    <div class="row">
        <div class="col-md form-group">
            {{ html()->checkbox('is_visible', $news->id ? $news->is_visible : 1, 1)->class('form-check-input')->attribute('data-toggle', 'toggle') }}
            {{ html()->label('Is Viewable', 'is_visible')->class('form-check-label ml-3') }} {!! add_help('If this is turned off, the post will not be visible. If the post time is set, it will automatically become visible at/after the given post time, so make sure the post time is empty if you want it to be completely hidden.') !!}
        </div>
        @if ($news->id && $news->is_visible)
            <div class="col-md form-group">
                {{ html()->checkbox('bump', null, 1)->class('form-check-input')->attribute('data-toggle', 'toggle') }}
                {{ html()->label('Bump News', 'bump')->class('form-check-label ml-3') }} {!! add_help('If toggled on, this will alert users that there is new news. Best in conjunction with a clear notification of changes!') !!}
            </div>
        @endif
    </div>

    <div class="text-right">
        {{ html()->submit($news->id ? 'Edit' : 'Create')->class('btn btn-primary') }}
    </div>

    {{ html()->form()->close() }}
@endsection

@section('scripts')
    @parent
    @include('widgets._datetimepicker_js')
    @include('js._tinymce_wysiwyg')
    <script>
        $(document).ready(function() {
            $('.delete-news-button').on('click', function(e) {
                e.preventDefault();
                loadModal("{{ url('admin/news/delete') }}/{{ $news->id }}", 'Delete Post');
            });
            $('.regen-news-button').on('click', function(e) {
                e.preventDefault();
                loadModal("{{ url('admin/news/regen') }}/{{ $news->id }}", 'Regenerate Post');
            });
        });
    </script>
@endsection
