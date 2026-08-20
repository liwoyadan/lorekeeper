@extends('admin.layout')

@section('admin-title')
    Housing Patterns
@endsection

@section('admin-content')
    {!! breadcrumbs([
        'Admin Panel' => 'admin',
        'Housing Patterns' => 'admin/data/housing-patterns',
        ($pattern->id ? 'Edit' : 'Create') . ' Pattern' => $pattern->id ? 'admin/data/housing-patterns/edit/' . $pattern->id : 'admin/data/housing-patterns/create',
    ]) !!}

    <h1>{{ $pattern->id ? 'Edit' : 'Create' }} Pattern
        @if ($pattern->id)
            <a href="#" class="btn btn-danger float-right delete-pattern-button">Delete Pattern</a>
        @endif
    </h1>

    {!! Form::open(['url' => $pattern->id ? 'admin/data/housing-patterns/edit/' . $pattern->id : 'admin/data/housing-patterns/create', 'files' => true]) !!}

    <div class="form-group">
        {!! Form::label('Name') !!}
        {!! Form::text('name', $pattern->name, ['class' => 'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('Pattern Image (PNG, tileable)') !!} {!! add_help('This image is tiled to fill a recolor zone. Use a seamless, tileable PNG.') !!}
        <div class="custom-file">
            {!! Form::label('image', 'Choose file...', ['class' => 'custom-file-label']) !!}
            {!! Form::file('image', ['class' => 'custom-file-input']) !!}
        </div>
        @if ($pattern->has_image)
            <div class="mt-2"><img src="{{ $pattern->patternImageUrl }}" style="width:64px; height:64px; object-fit:cover;" alt=""></div>
            <div class="form-check">
                {!! Form::checkbox('remove_image', 1, false, ['class' => 'form-check-input']) !!}
                {!! Form::label('remove_image', 'Remove current image', ['class' => 'form-check-label']) !!}
            </div>
        @endif
    </div>

    <div class="form-group">
        {!! Form::checkbox('is_visible', 1, $pattern->id ? $pattern->is_visible : 1, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
        {!! Form::label('is_visible', 'Is Visible', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If turned off, the pattern is hidden from zone allow-list selection.') !!}
    </div>

    <div class="text-right">
        {!! Form::submit($pattern->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
    </div>

    {!! Form::close() !!}
@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            $('.delete-pattern-button').on('click', function(e) {
                e.preventDefault();
                loadModal("{{ url('admin/data/housing-patterns/delete') }}/{{ $pattern->id }}", 'Delete Pattern');
            });
        });
    </script>
@endsection
