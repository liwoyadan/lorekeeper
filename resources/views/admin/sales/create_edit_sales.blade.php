@extends('admin.layout')

@section('admin-title')
    {{ $sales->id ? 'Edit' : 'Create' }} Sales Post
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Sales' => 'admin/sales', ($sales->id ? 'Edit' : 'Create') . ' Post' => $sales->id ? 'admin/sales/edit/' . $sales->id : 'admin/sales/create']) !!}

    <h1>{{ $sales->id ? 'Edit' : 'Create' }} Sales Post
        @if ($sales->id)
            <a href="#" class="btn btn-danger float-right delete-sales-button">Delete Post</a>
            <a href="{{ $sales->url }}" class="btn btn-info float-right mr-md-2">View Post</a>
        @endif
    </h1>

    {{ html()->form('POST', $sales->id ? 'admin/sales/edit/' . $sales->id : 'admin/sales/create')->acceptsFiles()->open() }}

    <h3>Basic Information</h3>

    <div class="row">
        <div class="col-md-6 form-group">
            {{ html()->label('Title') }}
            {{ html()->text('title', $sales->title)->class('form-control') }}
        </div>

        <div class="col-md-6 form-group">
            {{ html()->label('Post Time (Optional)') }} {!! add_help('This is the time that the sales post should be posted. Make sure the Is Viewable switch is off.') !!}
            {{ html()->text('post_at', $sales->post_at)->class('form-control datepicker') }}
        </div>
    </div>

    <div class="form-group">
        {{ html()->label('Header Image (Optional)') }} {!! add_help('This image will show up above the sales content and on the meta-image.') !!}
        <div class="custom-file">
            {{ html()->label('Choose file...', 'image')->class('custom-file-label') }}
            {{ html()->file('image')->class('custom-file-input') }}
        </div>
        @if ($sales->has_image)
            <div class="form-check">
                {{ html()->checkbox('remove_image', false, 1)->class('form-check-input') }}
                {{ html()->label('Remove current image', 'remove_image')->class('form-check-label') }}
            </div>
        @endif
    </div>

    <div class="form-group">
        {{ html()->label('Post Content') }}
        {{ html()->textarea('text', $sales->text)->class('form-control wysiwyg') }}
    </div>

    <div class="row">
        <div class="col-md form-group">
            {{ html()->checkbox('is_visible', $sales->id ? $sales->is_visible : 1, 1)->class('form-check-input')->attribute('data-toggle', 'toggle') }}
            {{ html()->label('Is Viewable', 'is_visible')->class('form-check-label ml-3') }} {!! add_help('If this is turned off, the post will not be visible. If the post time is set, it will automatically become visible at/after the given post time, so make sure the post time is empty if you want it to be completely hidden.') !!}
        </div>
        @if ($sales->id && $sales->is_visible)
            <div class="col-md form-group">
                {{ html()->checkbox('bump', null, 1)->class('form-check-input')->attribute('data-toggle', 'toggle') }}
                {{ html()->label('Bump Sale', 'bump')->class('form-check-label ml-3') }} {!! add_help('If toggled on, this will alert users that there is a new sale. Best in conjunction with a clear notification of changes!') !!}
            </div>
        @endif
        <div class="col-md form-group">
            {{ html()->checkbox('is_open', $sales->id ? $sales->is_open : 1, 1)->class('form-check-input')->attribute('data-toggle', 'toggle') }}
            {{ html()->label('Is Open', 'is_open')->class('form-check-label ml-3') }} {!! add_help('Whether or not the sale is open; used to label the post in the title. This should be on unless the sale is finished; if a time is set for comments to open, the sale will be labeled as \'Preview\' instead.') !!}
        </div>
        <div class="col-md form-group">
            {{ html()->label('Comments Open At (Optional)', 'comments_open_at') }} {!! add_help('The time at which comments open to members. Staff can post comments before this time.') !!}
            {{ html()->text('comments_open_at', $sales->comments_open_at)->class('form-control datepicker') }}
        </div>
    </div>

    <h3>Characters</h3>
    <p>
        Add characters to be featured in this sales post.
    </p>

    <div id="characters" class="mb-3">
        @if ($sales->id)
            @if (count($sales->characters()->whereRelation('character', 'deleted_at', null)->get()) != count($sales->characters))
                <div class="alert alert-warning">
                    <strong>Warning!</strong> Some characters have been deleted since they were added to this post. Editing this post will remove those characters permanently from the post.
                </div>
            @endif
            @foreach ($sales->characters()->whereRelation('character', 'deleted_at', null)->get() as $character)
                @include('admin.sales._character_select_entry', ['character' => $character])
            @endforeach
        @endif
    </div>
    <div class="text-right mb-3">
        <a href="#" class="btn btn-outline-info" id="addCharacter">Add Character</a>
    </div>

    <div class="text-right">
        {{ html()->submit($sales->id ? 'Edit' : 'Create')->class('btn btn-primary') }}
    </div>

    {{ html()->form()->close() }}

    @include('admin.sales._character_select')

@endsection

@section('scripts')
    @parent
    @include('admin.sales._character_select_js')
    @include('widgets._datetimepicker_js')
    @include('js._tinymce_wysiwyg')
    <script>
        $(document).ready(function() {
            $('.delete-sales-button').on('click', function(e) {
                e.preventDefault();
                loadModal("{{ url('admin/sales/delete') }}/{{ $sales->id }}", 'Delete Post');
            });

        });
    </script>
@endsection
