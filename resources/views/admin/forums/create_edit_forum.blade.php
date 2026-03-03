@extends('admin.layout')

@section('admin-title')
    Forums
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Forums' => 'admin/forums', ($forum->id ? 'Edit' : 'Create') . ' Forum' => $forum->id ? 'admin/forums/edit/' . $forum->id : 'admin/forums/create']) !!}

    <h1>{{ $forum->id ? 'Edit' : 'Create' }} Forum
        @if ($forum->id && !config('lorekeeper.text_forums.' . $forum->key))
            <a href="#" class="btn btn-danger float-right delete-forum-button">Delete Forum</a>
        @endif
    </h1>

    {!! Form::open(['url' => $forum->id ? 'admin/forums/edit/' . $forum->id : 'admin/forums/create', 'files' => true]) !!}

    <h3>Basic Information</h3>

    <div class="row">
        <div class="col-md-7">
            <div class="form-group">
                {!! Form::label('Name') !!}
                {!! Form::text('name', $forum->name, ['class' => 'form-control']) !!}
            </div>
        </div>

        <div class="col-md">
            <div class="form-group">
                {!! Form::label('Color (Hex code; optional)') !!}
                <div class="input-group cp">
                    {!! Form::text('color', $forum->color, ['class' => 'form-control']) !!}
                    <span class="input-group-append">
                        <span class="input-group-text colorpicker-input-addon"><i></i></span>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md">
            <div class="form-group">
                {!! Form::label('Sort (Optional)') !!} {!! add_help('Forums are organized by their container (category or board) and then ordered by sort and then by id.') !!}
                {!! Form::number('sort', $forum->sort, ['class' => 'form-control']) !!}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('Parent Forum (Optional)') !!} {!! add_help('If you do not pick a parent, this forum will be considered a Category and nobody will be able to make threads in it.') !!}
                {!! Form::select('parent_id', $forums, $forum->parent_id, ['class' => 'form-control', 'placeholder' => 'Select a forum']) !!}
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('Rank Restriction (Optional)') !!} {!! add_help('Only staff and users of this role are able to see and create threads in this forum.') !!}
                {!! Form::select('role_limit', $ranks, $forum->role_limit, ['class' => 'form-control', 'placeholder' => 'Select a role']) !!}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md">
            <div class="form-group">
                {!! Form::label('Banner Image (Optional)') !!} {!! add_help('This image is visible at the top of the forum.') !!}
                <div class="custom-file">
                    {!! Form::label('image', 'Choose file...', ['class' => 'custom-file-label']) !!}
                    {!! Form::file('image', ['class' => 'custom-file-input']) !!}
                </div>
                <div class="text-muted">
                    PNG, GIF, or WebP format. No recommended size.
                    @if ($forum->has_image)
                        (<a href="{{ $forum->imageUrl }}">View Current Banner Image</a>)
                    @endif
                </div>
                @if ($forum->has_image)
                    <div class="form-check">
                        {!! Form::checkbox('remove_image', 1, false, ['class' => 'form-check-input']) !!}
                        {!! Form::label('remove_image', 'Remove current image', ['class' => 'form-check-label']) !!}
                    </div>
                @endif
            </div>
        </div>

        <div class="col-md">
            <div class="form-group">
                {!! Form::label('Forum Icon (Optional)') !!} {!! add_help('This icon next to the forum\'s name.') !!}
                <div class="custom-file">
                    {!! Form::label('icon', 'Choose file...', ['class' => 'custom-file-label']) !!}
                    {!! Form::file('icon', ['class' => 'custom-file-input']) !!}
                </div>
                <div class="text-muted">
                    PNG, GIF, or WebP format. Square aspect ratio (1:1), recommended 50x50.
                    @if ($forum->has_icon)
                        (<a href="{{ $forum->iconUrl }}">View Current Icon</a>)
                    @endif
                </div>
                @if ($forum->has_icon)
                    <div class="form-check">
                        {!! Form::checkbox('remove_icon', 1, false, ['class' => 'form-check-input']) !!}
                        {!! Form::label('remove_icon', 'Remove current icon', ['class' => 'form-check-label']) !!}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('Forum Description') !!} {!! add_help('This should be under 300 characters.') !!}
        {!! Form::textarea('description', $forum->description, ['class' => 'form-control']) !!}
    </div>

    <div class="row">
        <div class="col-md">
            <div class="form-group">
                {!! Form::checkbox('is_active', 1, $forum->id ? $forum->is_active : 1, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
                {!! Form::label('is_active', 'Is Active', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If this is turned off, users will not be able to view the forum even if they have the link to it, unless they are staff.') !!}
            </div>
        </div>

        <div class="col-md">
            <div class="form-group">
                {!! Form::checkbox('is_locked', 1, $forum->id ? $forum->is_locked : 0, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
                {!! Form::label('is_locked', 'Locked', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If this is turned off, users will be able to create new threads and reply to them.') !!}
            </div>
        </div>

        <div class="col-md">
            <div class="form-group">
                {!! Form::checkbox('staff_only', 1, $forum->id ? $forum->staff_only : 0, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
                {!! Form::label('staff_only', 'Staff Only', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If this is turned on, only staff members will see this forum.') !!}
            </div>
        </div>

        @if ($forum->id && $forum->parent_id)
            <div class="col-md">
                <div class="form-group">
                    {!! Form::checkbox('characters_enabled', 1, $forum->id ? $forum->characters_enabled : 0, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
                    {!! Form::label('characters_enabled', 'Characters Enabled?', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If this is turned on, users will be able to post on threads in this forum as characters they own.') !!}
                </div>
            </div>
        @endif
    </div>
    
    @if ($forum->id && $forum->has_image)
        <div class="row align-items-end">
            <div class="col-md">
                <div class="form-group">
                    {!! Form::checkbox('forum_styles[use_board_bg]', 1, isset($forum->forum_styles['use_board_bg']) ? $forum->forum_styles['use_board_bg'] : 0, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
                    {!! Form::label('forum_styles[use_board_bg]', 'Banner as Board BG?', ['class' => 'form-check-label ml-3']) !!} {!! add_help('Turning this on will use the set banner image as a background for the board\'s entry on the forum index page.') !!}
                </div>
            </div>

            <div class="col-md">
                <div class="form-group">
                    {!! Form::label('forum_styles[board_bg_opacity]', 'Board BG Opacity? (Percentage)') !!} {!! add_help('This is the <b>percentage</b> of opacity that the board background will be set to. 15 is a recommended default as to note obscure text.') !!}
                    {!! Form::number('forum_styles[board_bg_opacity]', isset($forum->forum_styles['board_bg_opacity']) ? $forum->forum_styles['board_bg_opacity'] : 15, ['class' => 'form-control']) !!}
                </div>
            </div>
        </div>
    @endif
    
    <hr>
    <h3>
        Forum Rules
    </h3>
    <p class="mb-0">
        Optional. Here you can add and specific rules pertaining to this forum. Note that rules applied to <b>forum categories</b> (aka parent forums) will also be shown in all of its forum/subforums within it, while rules applied to regular forum boards are specific to that board.
    </p>
    <div class="text-right mb-2">
        <a class="btn btn-primary" id="addRule" href="#">Add Rule</a>
    </div>
    <div id="rulesBody">
        @if ($forum->id && $forum->forum_rules)
            @foreach ($forum->forum_rules as $rule)
                <div class="row mb-2">
                    <div class="col">
                        {!! Form::text('forum_rules[]', $rule, ['class' => 'form-control']) !!}
                    </div>
                    <div class="col-auto text-right">
                        <a href="#" class="btn btn-danger remove-rule"><i class="fas fa-times"></i></a>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <div class="text-right mt-3">
        {!! Form::submit($forum->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
    </div>
    {!! Form::close() !!}

    <div class="row mb-2 rule-row hide">
        <div class="col">
            {!! Form::text('forum_rules[]', null, ['class' => 'form-control']) !!}
        </div>
        <div class="col-auto text-right">
            <a href="#" class="btn btn-danger remove-rule"><i class="fas fa-times"></i></a>
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            $('.delete-forum-button').on('click', function(e) {
                e.preventDefault();
                loadModal("{{ url('admin/forums/delete') }}/{{ $forum->id }}", 'Delete Forum');
            });

            var $rules = $('#rulesBody');
            var $ruleRow = $('.rule-row');

            attachRuleRemoveListener($('#rulesBody .remove-rule'));

            $('#addRule').on('click', function(e) {
                e.preventDefault();
                var $clone = $ruleRow.clone();
                $rules.append($clone);
                $clone.removeClass('rule-row hide');
                attachRuleRemoveListener($clone.find('.remove-rule'));
            });

            function attachRuleRemoveListener(node) {
                node.on('click', function(e) {
                    e.preventDefault();
                    $(this).parent().parent().remove();
                });
            }
        });
    </script>
@endsection
