@extends('admin.layout')

@section('admin-title')
    {{ $changelog->id ? 'Edit' : 'Create' }} Changelog
@endsection

@section('admin-content')
    {!! breadcrumbs([
        'Admin Panel' => 'admin',
        'Changelogs' => 'admin/changelogs',
        ($changelog->id ? 'Edit' : 'Create') . ' Changelog' => $changelog->id ? 'admin/changelogs/edit/' . $changelog->id : 'admin/changelogs/create',
    ]) !!}

    <h1>{{ $changelog->id ? 'Edit' : 'Create' }} Changelog
        @if ($changelog->id)
            <a href="#" class="btn btn-danger float-right delete-changelog-button">Delete Changelog</a>
        @endif
    </h1>

    {!! Form::open(['url' => $changelog->id ? 'admin/changelogs/edit/' . $changelog->id : 'admin/changelogs/create']) !!}

    <div class="row">
        <div class="col-md">
            <div class="form-group">
                {!! Form::label('Type') !!} {!! add_help('Choose what kind of subject this changelog applies to. Selecting a type loads a list of records you can optionally pin the changelog to.') !!}
                {!! Form::select('type', $types, $changelog->type, ['class' => 'form-control selectize', 'id' => 'typeSelect', 'placeholder' => 'Select Type']) !!}
            </div>
        </div>
        <div class="col-md">
            <div class="form-group" id="subjectContainer">
                @if ($subjectOptions && $changelog->type)
                    @include('admin.changelogs._subject_options', ['options' => $subjectOptions, 'selected' => $changelog->type_id])
                @endif
            </div>
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('Text') !!}
        {!! Form::textarea('text', $changelog->text, ['class' => 'form-control wysiwyg']) !!}
    </div>

    <div class="form-group">
        {!! Form::checkbox('staff_only', 1, $changelog->staff_only, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
        {!! Form::label('staff_only', 'Staff Only', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If turned on, only staff will be able to see this changelog entry.') !!}
    </div>

    <div class="text-right">
        {!! Form::submit($changelog->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
    </div>

    {!! Form::close() !!}
@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            $('#typeSelect').selectize();
            initSubjectSelectize();

            $('#typeSelect').on('change', function() {
                var type = $(this).val();
                if (!type) {
                    $('#subjectContainer').html('');
                    return;
                }
                $.ajax({
                    type: "GET",
                    url: "{{ url('admin/changelogs/subject-options') }}",
                    data: {
                        type: type
                    },
                    dataType: "text"
                }).done(function(res) {
                    $('#subjectContainer').html(res);
                    initSubjectSelectize();
                }).fail(function(jqXHR, textStatus, errorThrown) {
                    alert("AJAX call failed: " + textStatus + ", " + errorThrown);
                });
            });

            $('.delete-changelog-button').on('click', function(e) {
                e.preventDefault();
                loadModal("{{ url('admin/changelogs/delete') }}/{{ $changelog->id }}", 'Delete Changelog');
            });
        });

        function initSubjectSelectize() {
            var $sel = $('#subjectSelect');
            if ($sel.length && !$sel[0].selectize) {
                $sel.selectize();
            }
        }
    </script>
@endsection
