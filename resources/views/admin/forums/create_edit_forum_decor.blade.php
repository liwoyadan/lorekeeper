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

    <div class="row">
        <div class="col-md">
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
        </div>

        <div class="col-md">
            <div class="form-group">
                {!! Form::label('Decor Type') !!}
                {!! Form::select('type', config('lorekeeper.forums.decor_types') ?? [], $decor->type ?? null, ['class' => 'form-control', 'id' => 'decor-type', 'placeholder' => 'Select Type']) !!}
            </div>
        </div>
    </div>

    <div id="background-options" class="card p-3 mb-3" style="display: none;">
        <h5>Background Options</h5>
        <div class="row align-items-end">
            <div class="col-md">
                <div class="form-group">
                    {!! Form::label('opacity', 'Opacity (%)') !!} {!! add_help('The opacity of the background image, as a percentage (0–100).') !!}
                    {!! Form::number('opacity', $decor->data['opacity'] ?? 15, ['class' => 'form-control', 'min' => 0, 'max' => 100]) !!}
                </div>
            </div>
            <div class="col-md">
                <div class="form-group">
                    {!! Form::label('background_size', 'Background Size') !!} {!! add_help('CSS background-size value (e.g. cover, contain, 50%). If left blank, defaults to cover.') !!}
                    {!! Form::text('background_size', $decor->data['background_size'] ?? null, ['class' => 'form-control', 'placeholder' => 'cover']) !!}
                </div>
            </div>
            <div class="col-md">
                <div class="form-group">
                    {!! Form::checkbox('background_repeat', 1, $decor->data['background_repeat'] ?? false, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
                    {!! Form::label('background_repeat', 'Repeat Image', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If enabled, the background image will tile/repeat. If disabled, it will not repeat.') !!}
                </div>
            </div>
        </div>
    </div>

    <div id="border-options" class="card p-3 mb-3" style="display: none;">
        <h5>Border Options</h5>
        <div class="row">
            <div class="col-md">
                <div class="form-group">
                    {!! Form::label('border_image_slice', 'Border Image Slice') !!} {!! add_help('CSS border-image-slice value (e.g. 30, 10%). Controls how the image is sliced into regions.') !!}
                    {!! Form::text('border_image_slice', $decor->data['border_image_slice'] ?? null, ['class' => 'form-control', 'placeholder' => 'e.g. 30']) !!}
                </div>
            </div>
            <div class="col-md">
                <div class="form-group">
                    {!! Form::label('border_image_width', 'Border Image Width') !!} {!! add_help('CSS border-image-width value (e.g. 10px, 1). Controls the width of the border image.') !!}
                    {!! Form::text('border_image_width', $decor->data['border_image_width'] ?? null, ['class' => 'form-control', 'placeholder' => 'e.g. 10px']) !!}
                </div>
            </div>
            <div class="col-md">
                <div class="form-group">
                    {!! Form::label('border_image_outset', 'Border Image Outset') !!} {!! add_help('CSS border-image-outset value (e.g. 0, 5px). Controls how far the border image extends beyond the border box.') !!}
                    {!! Form::text('border_image_outset', $decor->data['border_image_outset'] ?? null, ['class' => 'form-control', 'placeholder' => 'e.g. 0']) !!}
                </div>
            </div>
            <div class="col-md">
                <div class="form-group">
                    {!! Form::label('border_image_repeat', 'Border Image Repeat') !!} {!! add_help('CSS border-image-repeat value (e.g. stretch, repeat, round, space).') !!}
                    {!! Form::text('border_image_repeat', $decor->data['border_image_repeat'] ?? null, ['class' => 'form-control', 'placeholder' => 'e.g. stretch']) !!}
                </div>
            </div>
        </div>
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
        @include('admin.forums._preview_post', [
            'bgDecor' => $decor->type == 'background' ? $decor : null,
            'borderDecor' => $decor->type == 'border' ? $decor : null,
        ])
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

            function toggleTypeOptions() {
                var type = $('#decor-type').val();
                $('#background-options').toggle(type === 'background');
                $('#border-options').toggle(type === 'border');
            }

            $('#decor-type').on('change', toggleTypeOptions);
            toggleTypeOptions();
        });
    </script>
@endsection
