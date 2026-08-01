@extends('admin.layout')

@section('admin-title')
    {{ $theme->id ? 'Edit Theme: ' . $theme->name : 'Create Theme' }}
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Themes' => 'admin/themes', ($theme->id ? 'Edit' : 'Create') . ' Theme' => $theme->id ? 'admin/themes/edit/' . $theme->id : 'admin/themes/create']) !!}

    <h1 class="mb-0">
        {{ $theme->id ? 'Edit ' . $theme->name : 'Create Theme' }}
        @if ($theme->id)
            <a href="#" class="btn btn-danger float-right delete-theme-button">Delete Theme</a>
        @endif
    </h1>
    @if ($theme->creators && count(array_filter($theme->creatorData)))
        <div class="mb-0">
            by {!! $theme->creatorDisplayName !!}
        </div>
    @endif

    {!! Form::open(['url' => $theme->id ? 'admin/themes/edit/' . $theme->id : 'admin/themes/create', 'files' => true]) !!}
    <h3 class="my-1">
        Basic Information
    </h3>
    <div class="row">
        <div class="col-md-8">
            <div class="form-group">
                {!! Form::label('Theme Name') !!}
                {!! Form::text('name', $theme->name, ['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('theme_type', 'Type') !!}{!! add_help('Users can select both a base, and a decorator theme, where decorator themes will be layered over the selected base theme.') !!}
                {!! Form::select('theme_type', ['base' => 'Base', 'decorator' => 'Decorator'], $theme->theme_type ?? 'base', ['class' => 'form-control', 'id' => 'themeTypeSelect']) !!}
            </div>
        </div>
    </div>
    <div class="row {{ ($theme->theme_type ?? 'base') != 'base' ? 'hide' : '' }}" id="themeBootstrapRow">
        <div class="col-md">
            <div class="form-group">
                {!! Form::label('theme_bootstrap_id', 'Bootstrap Theme') !!}{!! add_help(
                    'Compiled Bootstrap theme to apply to the site when this theme is active. Only for base themes. If this is blank, it will fallback to either your set sitewide default if applicable, if not then LK\'s slightly edited default (app.css).',
                ) !!}
                {!! Form::select('theme_bootstrap_id', $bootstrapOptions, $theme->theme_bootstrap_id, ['class' => 'form-control', 'placeholder' => 'None (Default)']) !!}
            </div>
        </div>
    </div>
    <p>
        If a theme isn't active it keeps it from being useable by any feature.<br />
        Default may be overridden by conditional themes (like seasonal based if you add the weather extension), or user selected themes.
    </p>
    <div class="row">
        <div class="col-md">
            <div class="form-group">
                {!! Form::checkbox('is_active', 1, $theme->id ? $theme->is_active : 1, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
                {!! Form::label('is_active', 'Is Active', ['class' => 'form-check-label ml-3']) !!}{!! add_help('If this is turned off, the theme won\'t be useable.') !!}
            </div>
        </div>
        <div class="col-md">
            <div class="form-group">
                {!! Form::checkbox('is_default', 1, $theme->id ? $theme->is_default : 0, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
                {!! Form::label('is_default', 'Is Default', ['class' => 'form-check-label ml-3']) !!}{!! add_help('One at a time. Users with no theme selected default to this theme and logged out visitors default to this theme.') !!}
            </div>
        </div>
        <div class="col-md">
            <div class="is_user_selectable">
                {!! Form::checkbox('is_user_selectable', 1, $theme->id ? $theme->is_user_selectable : 0, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
                {!! Form::label('is_user_selectable', 'Is User Selectable by Default', ['class' => 'form-check-label ml-3']) !!}{!! add_help('Is this a theme users can select freely? Themes granted by items should have this turned off.') !!}
            </div>
        </div>
    </div>

    @if (get_object_vars($conditions))
        <hr />
        <h5 class="mb-1">
            Conditional Theme
        </h5>
        <p>
            Setting a condition here will cause this theme to override the default theme if the conditions below are met. <br />
            As such you should only select one condition, or risk the site themes getting a bit confused. <br />
            Conditional Themes will be layered on top of a users base theme, and under a user's decorative theme selections.
        </p>
        @if (isset($conditions->weathers))
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('Seasonal') !!} {!! add_help('This will implement this theme when this season is active.') !!}
                        {!! Form::select('season_link_id', $conditions->seasons, $theme->link_type === 'season' ? $theme->link_id : null, ['class' => 'form-control', 'placeholder' => 'Select a Season']) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('Weather') !!} {!! add_help('This will implement this theme when this weather is active.') !!}
                        {!! Form::select('weather_link_id', $conditions->weathers, $theme->link_type === 'weather' ? $theme->link_id : null, ['class' => 'form-control', 'placeholder' => 'Select a Weather']) !!}
                    </div>
                </div>
            </div>
        @endif
    @endif

    <hr />

    <h4 class="mb-1">
        Creator(s)
    </h4>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group row align-items-center">
                <div class="col-md-auto">
                    {!! Form::label('creator_name[]', 'Creator(s) Name', ['class' => 'mb-md-0']) !!}{!! add_help('On-site users you would like to credit for this theme.') !!}
                </div>
                <div class="col-md">
                    {!! Form::select('creator_name[]', $userOptions, $theme->creatorData['name'] ?? (null ?? null), ['class' => 'form-control creator-select', 'multiple']) !!}
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group row align-items-center">
                <div class="col-md-auto">
                    {!! Form::label('creator_url', 'Creator Url(s)', ['class' => 'mb-md-0']) !!}{!! add_help('Separate multiples via comma.') !!}
                </div>
                <div class="col-md">
                    {!! Form::text('creator_url', $theme->creators ? $theme->creatorData['url'] ?? null : null, ['class' => 'form-control']) !!}
                </div>
            </div>
        </div>
    </div>

    <hr />

    <h4 class="mb-1">
        Custom CSS File
    </h4>
    <div class="row align-items-center">
        <div class="col-md">
            <div class="form-group">
                @if ($theme->has_css)
                    <a href="{{ $theme->cssUrl }}"><i class="fas fa-link"></i></a>
                @endif
                {!! Form::label('css', 'CSS File') !!}
                <div class="custom-file">
                    {!! Form::label('css', 'Choose CSS...', ['class' => 'custom-file-label']) !!}
                    {!! Form::file('css', ['class' => 'custom-file-input']) !!}
                </div>
                <div class="text-muted">Only CSS Files. Max file size: 1000 KB.</div>
                @if ($theme->has_css)
                    <div class="form-check">
                        {!! Form::checkbox('remove_css', 1, false, ['class' => 'form-check-input']) !!}
                        {!! Form::label('remove_css', 'Remove current css file', ['class' => 'form-check-label']) !!}
                    </div>
                @endif
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {!! Form::checkbox('prioritize_css', 1, $theme->prioritize_css ?? false, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
                {!! Form::label('prioritize_css', 'Prioritize CSS over Below Values', ['class' => 'form-check-label ml-2 mb-md-0']) !!} {!! add_help(
                    'If you would rather the css ovverride the following values turn this on, otherwise if you want the below values to override the css leave this off. It is possible to have css selectors that will still override the below values with this off, but more conditionally.',
                ) !!}
            </div>
        </div>
    </div>
    <hr class="w-75 mx-auto">
    <h5 class="mb-1">
        Raw CSS
    </h5>
    <p>
        For any extra snippets of raw CSS you would like added to this theme.
    </p>
    <div class="card rounded-0">
        {!! Form::textarea('raw_css', '', ['id' => 'rawCSS']) !!}
    </div>

    <hr>

    <h4 class="mb-1">
        Header Image
    </h4>
    <p>
        The Header Image can be uploaded directly or specified by url. Finally you can turn the header off entirely and have just the top nav.
    </p>
    <div class="row">
        <div class="col-md">
            <div class="form-group">
                @if ($theme->has_header)
                    <a href="{{ $theme->headerImageUrl }}">
                        <i class="fas fa-link"></i>
                    </a>
                @endif
                {!! Form::label('header', 'Header Image') !!}
                <div class="custom-file">
                    {!! Form::label('header', 'Choose header...', ['class' => 'custom-file-label']) !!}
                    {!! Form::file('header', ['class' => 'custom-file-input']) !!}
                </div>
                <div class="text-muted">Header image.</div>
                @if ($theme->has_header)
                    <div class="form-check">
                        {!! Form::checkbox('remove_header', 1, false, ['class' => 'form-check-input']) !!}
                        {!! Form::label('remove_header', 'Remove current header', ['class' => 'form-check-label']) !!}
                    </div>
                @endif
            </div>
        </div>
        <div class="col-md">
            <div class="form-group">
                {!! Form::label('Header Image Url') !!}
                {!! Form::text('header_image_url', $theme->themeEditor->header_image_url ?? '', ['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="col-12">
            <div class="form-group">
                {!! Form::checkbox('header_image_display', 1, $theme->id ? $theme->themeEditor?->header_image_display == 'inline' ?? 1 : 1, ['class' => 'form-check-input form-control', 'data-toggle' => 'toggle']) !!}
                {!! Form::label('header_image_display', 'Show Header Image', ['class' => 'ml-2 form-check-label mb-0']) !!}
            </div>
        </div>
    </div>

    <hr class="w-75 mx-auto" />

    <h4 class="mb-1">
        Background Image
    </h4>
    <p>
        The Background Image can be uploaded directly or specified by url. If you only specify a color there will be no background image.
    </p>
    <div class="row">
        <div class="col-md">
            <div class="form-group">
                @if ($theme->has_background)
                    <a href="{{ $theme->backgroundImageUrl }}">
                        <i class="fas fa-link"></i>
                    </a>
                @endif
                {!! Form::label('background', 'Background Image') !!}
                <div class="custom-file">
                    {!! Form::label('background', 'Choose file...', ['class' => 'custom-file-label']) !!}
                    {!! Form::file('background', ['class' => 'custom-file-input']) !!}
                </div>
                <div class="text-muted">Background image.</div>
                @if ($theme->has_background)
                    <div class="form-check">
                        {!! Form::checkbox('remove_background', 1, false, ['class' => 'form-check-input']) !!}
                        {!! Form::label('remove_background', 'Remove current background', ['class' => 'form-check-label']) !!}
                    </div>
                @endif
            </div>
        </div>
        <div class="col-md">
            <div class="form-group">
                {!! Form::label('Background Image Url') !!}
                {!! Form::text('background_image_url', $theme->themeEditor->background_image_url ?? '', ['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="col-md">
            <div class="form-group">
                {!! Form::label('Select Background Color') !!}
                <div class="input-group cp">
                    {!! Form::text('background_color', $theme->themeEditor->background_color ?? null, ['class' => 'form-control']) !!}
                    <span class="input-group-append">
                        <span class="input-group-text colorpicker-input-addon"><i></i></span>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md">
            <div class="form-group">
                {!! Form::label('background_size', 'Background Repeat') !!}{!! add_help('If set to <b>repeat</b>, your background image will repeat to fill the page. If set to <b>not repeat</b>, your background image will cover the full width of the screen.') !!}
                {!! Form::select('background_size', ['repeat' => 'Repeat (Tile) Background Image', 'no-repeat' => 'Don\'t Repeat Background Image'], $theme->themeEditor && $theme->themeEditor?->background_size == 'cover' ? 'no-repeat' : 'repeat', [
                    'class' => 'form-control',
                ]) !!}
            </div>
        </div>
    </div>

    <hr>

    <h5 class="mb-1">
        Menu Bar
    </h5>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('Select title color') !!}
                <div class="input-group cp">
                    {!! Form::text('title_color', $theme->themeEditor->title_color ?? null, ['class' => 'form-control']) !!}
                    <span class="input-group-append">
                        <span class="input-group-text colorpicker-input-addon"><i></i></span>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('Select menu color') !!}
                <div class="input-group cp">
                    {!! Form::text('nav_color', $theme->themeEditor->nav_color ?? null, ['class' => 'form-control']) !!}
                    <span class="input-group-append">
                        <span class="input-group-text colorpicker-input-addon"><i></i></span>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('Select menu text color') !!}
                <div class="input-group cp">
                    {!! Form::text('nav_text_color', $theme->themeEditor->nav_text_color ?? null, ['class' => 'form-control']) !!}
                    <span class="input-group-append">
                        <span class="input-group-text colorpicker-input-addon"><i></i></span>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <hr />

    <h5 class="mb-1">
        Main Content
    </h5>
    <p>
        These colors also affect modal colors, the sidebar and input fields.
    </p>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('Select main content color') !!}
                <div class="input-group cp">
                    {!! Form::text('main_color', $theme->themeEditor->main_color ?? null, ['class' => 'form-control']) !!}
                    <span class="input-group-append">
                        <span class="input-group-text colorpicker-input-addon"><i></i></span>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('Select main text color') !!}
                <div class="input-group cp">
                    {!! Form::text('main_text_color', $theme->themeEditor->main_text_color ?? null, ['class' => 'form-control']) !!}
                    <span class="input-group-append">
                        <span class="input-group-text colorpicker-input-addon"><i></i></span>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <hr>

    <h5 class="mb-1">
        Card Content
    </h5>
    <p>
        These colors also affect list groups and the nav tabs.
    </p>
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('Select card color') !!}
                <div class="input-group cp">
                    {!! Form::text('card_color', $theme->themeEditor->card_color ?? null, ['class' => 'form-control']) !!}
                    <span class="input-group-append">
                        <span class="input-group-text colorpicker-input-addon"><i></i></span>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('Select card text color') !!}
                <div class="input-group cp">
                    {!! Form::text('card_text_color', $theme->themeEditor->card_text_color ?? null, ['class' => 'form-control']) !!}
                    <span class="input-group-append">
                        <span class="input-group-text colorpicker-input-addon"><i></i></span>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('Select card header color') !!}
                <div class="input-group cp">
                    {!! Form::text('card_header_color', $theme->themeEditor->card_header_color ?? null, ['class' => 'form-control']) !!}
                    <span class="input-group-append">
                        <span class="input-group-text colorpicker-input-addon"><i></i></span>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('Select card header text color') !!}
                <div class="input-group cp">
                    {!! Form::text('card_header_text_color', $theme->themeEditor->card_header_text_color ?? null, ['class' => 'form-control']) !!}
                    <span class="input-group-append">
                        <span class="input-group-text colorpicker-input-addon"><i></i></span>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <hr>

    <h5 class="mb-1">
        Links & Buttons
    </h5>
    <p>
        Primary and secondary buttons will use the same text color as links.
    </p>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('Select link color') !!}
                <div class="input-group cp">
                    {!! Form::text('link_color', $theme->themeEditor->link_color ?? null, ['class' => 'form-control']) !!}
                    <span class="input-group-append">
                        <span class="input-group-text colorpicker-input-addon"><i></i></span>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('Select primary button color') !!}
                <div class="input-group cp">
                    {!! Form::text('primary_button_color', $theme->themeEditor->primary_button_color ?? null, ['class' => 'form-control']) !!}
                    <span class="input-group-append">
                        <span class="input-group-text colorpicker-input-addon"><i></i></span>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('Select secondary button color') !!}
                <div class="input-group cp">
                    {!! Form::text('secondary_button_color', $theme->themeEditor->secondary_button_color ?? null, ['class' => 'form-control']) !!}
                    <span class="input-group-append">
                        <span class="input-group-text colorpicker-input-addon"><i></i></span>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="text-right">
        {!! Form::submit($theme->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
    </div>
    {!! Form::close() !!}
@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            const codeEditor = CodeMirror.fromTextArea(document.getElementById('rawCSS'), {
                mode: 'css',
                theme: 'dracula',
                lineNumbers: true,
                lineWrapping: true,
                indentWithTabs: false,
                tabSize: 4,
            });

            $('.delete-theme-button').on('click', function(e) {
                e.preventDefault();
                loadModal("{{ url('admin/themes/delete') }}/{{ $theme->id }}", 'Delete Theme');
            });

            $('.creator-select').selectize();

            $('#themeTypeSelect').on('change', function() {
                $('#themeBootstrapRow').toggleClass('hide', $(this).val() != 'base');
            });
        });
    </script>
@endsection
