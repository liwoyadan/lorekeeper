@if ($submission)
    {{ html()->form('POST', 'gallery/archive/' . $submission->id)->open() }}

    <p>You are about to {{ $submission->is_visible ? 'archive' : 'unarchive' }} the submission <strong>{{ $submission->title }}</strong>. This is reversible; you will be able to {{ $submission->is_visible ? 'unarchive' : 'archive' }} the
        submission at any time. Archiving a submission hides it from view by other users, but not staff.</p>
    <p>Are you sure you want to {{ $submission->is_visible ? 'archive' : 'unarchive' }} <strong>{{ $submission->title }}</strong>?</p>

    <div class="text-right">
        {{ html()->submit(($submission->is_visible ? 'Archive' : 'Unarchive') . ' Submission')->class('btn btn-warning') }}
    </div>

    {{ html()->form()->close() }}
@else
    Invalid submission selected.
@endif
