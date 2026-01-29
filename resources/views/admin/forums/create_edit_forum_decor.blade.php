@extends('admin.layout')

@section('admin-title')
    {{ $decor->id ? 'Edit' : 'Create' }} Forum Decor
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Forums' => 'admin/forums', 'Forum Decors' => 'admin/forum-decors', ($decor->id ? 'Edit' : 'Create') . ' Decor' => $decor->id ? 'admin/forum-decors/edit/' . $decor->id : 'admin/forum-decors/create']) !!}

    <h1>
        {{ $decor->id ? 'Edit' : 'Create' }} Forum Decor
        @if ($decor->id)
            <a href="#" class="btn btn-outline-danger float-right delete-decor-button">Delete Decor</a>
        @endif
    </h1>

    {!! Form::open(['url' => $decor->id ? 'admin/forum-decors/edit/' . $decor->id : 'admin/forum-decors/create', 'files' => true]) !!}

    <h3>Basic Information</h3>

    <div class="form-group">
        {!! Form::label('Name') !!}
        {!! Form::text('name', $decor->name, ['class' => 'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('Decor Type') !!}
        {!! Form::select('type', [], $decor->type ?? null, ['class' => 'form-control', 'placeholder' => 'Select Type']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('Decor Image') !!} {!! add_help('This image will be used for the decor display.') !!}
        <div class="custom-file">
            {!! Form::label('image', 'Choose file...', ['class' => 'custom-file-label']) !!}
            {!! Form::file('image', ['class' => 'custom-file-input']) !!}
        </div>
        @if ($decor->has_image)
            <div class="form-check">
                {!! Form::checkbox('remove_image', 1, false, ['class' => 'form-check-input']) !!}
                {!! Form::label('remove_image', 'Remove current image', ['class' => 'form-check-label']) !!}
            </div>
        @endif
    </div>

    <div class="form-group">
        {!! Form::label('Description (Optional)') !!} {!! add_help('A description of how to obtain or what this decor represents.') !!}
        {!! Form::textarea('description', $decor->description, ['class' => 'form-control wysiwyg']) !!}
    </div>

    <div class="row">
        <div class="col-md">
            <div class="form-group">
                {!! Form::checkbox('is_visible', 1, $decor->id ? $decor->is_visible : 1, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
                {!! Form::label('is_visible', 'Is Visible', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If this is off, users will not be able to see or select this decor.') !!}
            </div>
        </div>

        <div class="col-md">
            <div class="form-group">
                {!! Form::checkbox('is_default', 1, $decor->id ? $decor->is_default : 0, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
                {!! Form::label('is_default', 'Is Default', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If this is on, all users will start with this decor.') !!}
            </div>
        </div>

        <div class="col-md">
            <div class="form-group">
                {!! Form::checkbox('staff_only', 1, $decor->id ? $decor->staff_only : 0, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
                {!! Form::label('staff_only', 'Staff Only', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If this is on, only staff members can use this decor.') !!}
            </div>
        </div>
    </div>

    <div class="text-right">
        {!! Form::submit($decor->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
    </div>

    {!! Form::close() !!}

    @if ($decor->id)
        <h3>Preview</h3>
    @endif

@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            $('.delete-decor-button').on('click', function(e) {
                e.preventDefault();
                loadModal("{{ url('admin/forum-decors/delete') }}/{{ $decor->id }}", 'Delete Forum Decor');
            });
        });
    </script>
@endsection
