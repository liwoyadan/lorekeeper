@extends('admin.layout')

@section('admin-title')
    {{ $gallery->id ? 'Edit' : 'Create' }} Gallery
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Galleries' => 'admin/data/galleries', ($gallery->id ? 'Edit' : 'Create') . ' Gallery' => $gallery->id ? 'admin/data/galleries/edit/' . $gallery->id : 'admin/data/galleries/create']) !!}

    <h1>{{ $gallery->id ? 'Edit' : 'Create' }} Gallery
        @if ($gallery->id)
            <a href="#" class="btn btn-danger float-right delete-gallery-button">Delete Gallery</a>
        @endif
    </h1>

    {{ html()->form('POST', $gallery->id ? 'admin/data/galleries/edit/' . $gallery->id : 'admin/data/galleries/create')->open() }}

    <h3>Basic Information</h3>

    <div class="row">
        <div class="col-md form-group">
            {{ html()->label('Name') }}
            {{ html()->text('name', $gallery->name)->class('form-control') }}
        </div>
        <div class="col-md-2 form-group">
            {{ html()->label('Sort (Optional)') }} {!! add_help('Galleries are ordered first by sort number, then by name-- so galleries without a sort number are sorted only by name.') !!}
            {{ html()->number('sort', $gallery->sort)->class('form-control') }}
        </div>
    </div>

    <div class="form-group">
        {{ html()->label('Parent Gallery (Optional)') }}
        {{ html()->select('parent_id', $galleries, $gallery->parent_id)->class('form-control')->placeholder('Select a gallery') }}
    </div>

    <div class="form-group">
        {{ html()->label('Description (Optional)') }}
        {{ html()->textarea('description', $gallery->description)->class('form-control') }}
    </div>

    <div class="row">
        <div class="col-md form-group">
            {{ html()->checkbox('submissions_open', $gallery->submissions_open, 1)->class('form-check-input')->attribute('data-toggle', 'toggle') }}
            {{ html()->label('Submissions Open', 'submissions_open')->class('form-check-label ml-3') }} {!! add_help(
                'Whether or not users can submit to this gallery. Admins can submit regardless of this setting. Does not override global setting. Leave this on for time-limited galleries; users wll not be able to submit outside of the start and end times regardless of this setting, but will not be able to submit at all if this is off.',
            ) !!}
        </div>
        @if (Settings::get('gallery_submissions_reward_currency'))
            <div class="col-md form-group">
                {{ html()->checkbox('currency_enabled', $gallery->currency_enabled, 1)->class('form-check-input')->attribute('data-toggle', 'toggle') }}
                {{ html()->label('Enable Currency Rewards', 'currency_enabled')->class('form-check-label ml-3') }} {!! add_help('Whether or not submissions to this gallery are eligible for rewards of group currency.') !!}
            </div>
        @endif
        <div class="col-md form-group">
            {{ html()->checkbox('prompt_selection', $gallery->prompt_selection, 1)->class('form-check-input')->attribute('data-toggle', 'toggle') }}
            {{ html()->label('Prompt Selection', 'prompt_selection')->class('form-check-label ml-3') }} {!! add_help(
                'Whether or not users can select a prompt to associate a gallery submission with when creating it. Gallery submissions will still auto-associate, prefix, etc. themselves with prompts if approved prompt submissions using the gallery submission exist.',
            ) !!}
        </div>
    </div>
    @if (Settings::get('gallery_submissions_require_approval'))
        <div class="form-group">
            {{ html()->label('Votes Required') }} {!! add_help('How many votes are required for submissions to this gallery to be accepted. Set to 0 to automatically accept submissions.') !!}
            {{ html()->number('votes_required', $gallery->votes_required)->class('form-control') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md form-group">
            {{ html()->label('Hide Before Start Time', 'hide_before_start')->class('form-check-label ml-3') }} {!! add_help('If hidden, the gallery will not be shown on the gallery list before the starting time is reached. A starting time needs to be set. Galleries are always visible after the end time.') !!}<br />
            {{ html()->checkbox('hide_before_start', $gallery->id ? $gallery->hide_before_start : 0, 1)->class('form-check-input')->attribute('data-toggle', 'toggle') }}
        </div>
        <div class="col-md form-group">
            {{ html()->label('Start Time (Optional)', 'start_at') }} {!! add_help('Pieces cannot be submitted to the gallery before the starting time.') !!}
            {{ html()->text('start_at', $gallery->start_at)->class('form-control datepicker') }}
        </div>
        <div class="col-md form-group">
            {{ html()->label('End Time (Optional)', 'end_at') }} {!! add_help('Pieces cannot be submitted to the gallery after the ending time.') !!}
            {{ html()->text('end_at', $gallery->end_at)->class('form-control datepicker') }}
        </div>
    </div>

    <div class="text-right">
        {{ html()->submit($gallery->id ? 'Edit' : 'Create')->class('btn btn-primary') }}
    </div>

    {{ html()->form()->close() }}
@endsection

@section('scripts')
    @parent
    @include('widgets._datetimepicker_js')
    <script>
        $(document).ready(function() {
            $('.delete-gallery-button').on('click', function(e) {
                e.preventDefault();
                loadModal("{{ url('admin/data/galleries/delete') }}/{{ $gallery->id }}", 'Delete Gallery');
            });

        });
    </script>
@endsection
