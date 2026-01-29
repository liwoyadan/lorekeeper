@extends('admin.layout')

@section('admin-title')
    {{ $flair->id ? 'Edit' : 'Create' }} Forum Flair
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Forums' => 'admin/forums', 'Forum Flairs' => 'admin/forum-flairs', ($flair->id ? 'Edit' : 'Create') . ' Flair' => $flair->id ? 'admin/forum-flairs/edit/' . $flair->id : 'admin/forum-flairs/create']) !!}

    <h1>{{ $flair->id ? 'Edit' : 'Create' }} Forum Flair
        @if ($flair->id)
            <a href="#" class="btn btn-outline-danger float-right delete-flair-button">Delete Flair</a>
        @endif
    </h1>

    {!! Form::open(['url' => $flair->id ? 'admin/forum-flairs/edit/' . $flair->id : 'admin/forum-flairs/create', 'files' => true]) !!}

    <h3>Basic Information</h3>

    <div class="form-group">
        {!! Form::label('Name') !!}
        {!! Form::text('name', $flair->name, ['class' => 'form-control']) !!}
    </div>

    <div class="row">
        <div class="col-md">
            <div class="form-group">
                {!! Form::label('Username Text Color (Optional)') !!}
                <div class="input-group cp-alpha">
                    {!! Form::text('color', $flair->color ?? null, ['class' => 'form-control']) !!}
                    <span class="input-group-append">
                        <span class="input-group-text colorpicker-input-addon"><i></i></span>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md">
            <div class="form-group">
                {!! Form::label('Username BG Color (Optional)') !!}
                <div class="input-group cp-alpha">
                    {!! Form::text('bg_color', $flair->bg_color ?? null, ['class' => 'form-control']) !!}
                    <span class="input-group-append">
                        <span class="input-group-text colorpicker-input-addon"><i></i></span>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md">
            <div class="form-group">
                {!! Form::label('Post Requirement (Optional)') !!} {!! add_help('The number of forum posts a user needs to automatically unlock this flair.') !!}
                {!! Form::number('post_requirement', $flair->post_requirement, ['class' => 'form-control']) !!}
            </div>
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('Flair Icon (Optional)') !!} {!! add_help('This icon will be displayed next to the user\'s username.') !!}
        <div class="custom-file">
            {!! Form::label('image', 'Choose file...', ['class' => 'custom-file-label']) !!}
            {!! Form::file('image', ['class' => 'custom-file-input']) !!}
        </div>
        <div class="text-muted">Recommended size: 16px x 16px. PNG, GIF, or WebP format.</div>
        @if ($flair->has_image)
            <div class="form-check">
                {!! Form::checkbox('remove_image', 1, false, ['class' => 'form-check-input']) !!}
                {!! Form::label('remove_image', 'Remove current image', ['class' => 'form-check-label']) !!}
            </div>
        @endif
    </div>

    <div class="form-group">
        {!! Form::label('Description (Optional)') !!} {!! add_help('A description of how to obtain or what this flair represents.') !!}
        {!! Form::textarea('description', $flair->description, ['class' => 'form-control wysiwyg']) !!}
    </div>

    <div class="row">
        <div class="col-md">
            <div class="form-group">
                {!! Form::checkbox('is_visible', 1, $flair->id ? $flair->is_visible : 1, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
                {!! Form::label('is_visible', 'Is Visible', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If this is off, users will not be able to see or select this flair.') !!}
            </div>
        </div>

        <div class="col-md">
            <div class="form-group">
                {!! Form::checkbox('is_default', 1, $flair->id ? $flair->is_default : 0, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
                {!! Form::label('is_default', 'Is Default', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If this is on, all users will start with this flair.') !!}
            </div>
        </div>

        <div class="col-md">
            <div class="form-group">
                {!! Form::checkbox('staff_only', 1, $flair->id ? $flair->staff_only : 0, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
                {!! Form::label('staff_only', 'Staff Only', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If this is on, only staff members can use this flair.') !!}
            </div>
        </div>
    </div>

    @if ($flair->id)
        <hr>
        <h4 class="mb-1">
            Text Shadow Options
        </h4>
        <div class="text-right mb-2">
            <a href="#" class="btn btn-primary btn-sm" id="addShadow">
                Add Text Shadow
            </a>
        </div>
        <div class="card" id="textShadows">
            <div class="card-body">
                @if (count($flair->data['text_shadow'] ?? []))
                    @foreach ($flair->data['text_shadow'] as $textShadow)
                        <div class="row no-gutters align-items-end px-3 mb-2">
                            <div class="col-3 pr-1">
                                {!! Form::label('text_shadow_x', 'X Offset', ['class' => 'form-label mb-0']) !!}{!! add_help('If no valid unit is specified, this will default to px.') !!}
                                {!! Form::text('text_shadow_x[]', $textShadow['offset_x'] ?? '0px', ['class' => 'form-control', 'placeholder' => 'Default 0px']) !!}
                            </div>
                            <div class="col-3 px-1">
                                {!! Form::label('text_shadow_y', 'Y Offset', ['class' => 'form-label mb-0']) !!}{!! add_help('If no valid unit is specified, this will default to px.') !!}
                                {!! Form::text('text_shadow_y[]', $textShadow['offset_y'] ?? '0px', ['class' => 'form-control', 'placeholder' => 'Default 0px']) !!}
                            </div>
                            <div class="col-3 px-1">
                                {!! Form::label('text_shadow_blur', 'Blur Radius', ['class' => 'form-label mb-0']) !!}{!! add_help('If no valid unit is specified, this will default to px.') !!}
                                {!! Form::text('text_shadow_blur[]', $textShadow['blur_radius'] ?? '0px', ['class' => 'form-control', 'placeholder' => 'Default 0px']) !!}
                            </div>
                            <div class="col pr-1">
                                {!! Form::label('text_shadow_color', 'Color', ['class' => 'form-label mb-0']) !!}
                                <div class="input-group cp">
                                    {!! Form::text('text_shadow_color[]', $textShadow['color'] ?? null, ['class' => 'form-control']) !!}
                                    <span class="input-group-append">
                                        <span class="input-group-text colorpicker-input-addon"><i></i></span>
                                    </span>
                                </div>
                            </div>
                            <div class="col-auto pb-1">
                                <a href="#" class="remove-shadow btn btn-danger btn-sm" data-toggle="tooltip" title="Remove">
                                    <i class="fas fa-times" aria-hidden="true"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    @endif

    <div class="text-right mt-3">
        {!! Form::submit($flair->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
    </div>

    {!! Form::close() !!}

    @if ($flair->id)
        <div class="text-shadow-row hide row no-gutters align-items-end px-3 mb-2">
            <div class="col-3 pr-1">
                {!! Form::label('text_shadow_x', 'X Offset', ['class' => 'form-label mb-0']) !!}{!! add_help('If no valid unit is specified, this will default to px.') !!}
                {!! Form::text('text_shadow_x[]', null, ['class' => 'form-control', 'placeholder' => 'Default 0px']) !!}
            </div>
            <div class="col-3 px-1">
                {!! Form::label('text_shadow_y', 'Y Offset', ['class' => 'form-label mb-0']) !!}{!! add_help('If no valid unit is specified, this will default to px.') !!}
                {!! Form::text('text_shadow_y[]', null, ['class' => 'form-control', 'placeholder' => 'Default 0px']) !!}
            </div>
            <div class="col-3 px-1">
                {!! Form::label('text_shadow_blur', 'Blur Radius', ['class' => 'form-label mb-0']) !!}{!! add_help('If no valid unit is specified, this will default to px.') !!}
                {!! Form::text('text_shadow_blur[]', null, ['class' => 'form-control', 'placeholder' => 'Default 0px']) !!}
            </div>
            <div class="col pr-1">
                {!! Form::label('text_shadow_color', 'Color', ['class' => 'form-label mb-0']) !!}
                <div class="input-group cp-alpha">
                    {!! Form::text('text_shadow_color[]', null, ['class' => 'form-control']) !!}
                    <span class="input-group-append">
                        <span class="input-group-text colorpicker-input-addon"><i></i></span>
                    </span>
                </div>
            </div>
            <div class="col-auto pb-1">
                <a href="#" class="remove-shadow btn btn-danger btn-sm" data-toggle="tooltip" title="Remove">
                    <i class="fas fa-times" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    
        <h3>Preview</h3>
    @endif

@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            $('.delete-flair-button').on('click', function(e) {
                e.preventDefault();
                loadModal("{{ url('admin/forum-flairs/delete') }}/{{ $flair->id }}", 'Delete Forum Flair');
            });

            $('.cp-alpha').colorpicker({
                format: 'rgba'
            });

            $('#addShadow').on('click', function(e) {
                e.preventDefault();
                addShadowRow();
            });
            $('.remove-shadow').on('click', function(e) {
                e.preventDefault();
                removeShadowRow($(this));
            })

            function addShadowRow() {
                var $clone = $('.text-shadow-row').clone();
                $('#textShadows').append($clone);
                $clone.removeClass('hide text-shadow-row');
                $clone.find('.remove-shadow').on('click', function(e) {
                    e.preventDefault();
                    removeShadowRow($(this));
                });
                $clone.find('[data-toggle="tooltip"]').tooltip({
                    html: true
                });
                $clone.find('.cp-alpha').colorpicker({
                    format: 'rgba'
                });
            }

            function removeShadowRow($trigger) {
                $trigger.parent().parent().remove();
            }
        });
    </script>
@endsection
