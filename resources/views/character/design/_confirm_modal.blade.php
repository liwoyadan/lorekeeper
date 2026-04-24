@if ($request->user_id == Auth::user()->id)
    @if ($request->isComplete)
        <p>This will submit the design approval request. While the request is in the queue, <u>you will not be able to edit it</u>. </p>
        <p>Are you sure you want to submit this request?</p>
        {{ html()->form('POST', 'designs/' . $request->id . '/submit')->class('text-right')->open() }}
        {{ html()->submit('Submit Request')->class('btn btn-primary') }}
        {{ html()->form()->close() }}
    @else
        <p class="text-danger">Not all sections have been completed yet. Please visit the necessary tab(s) and click Save to update them, even if no modifications to the information are needed.</p>
        <div class="text-right">
            <button class="btn btn-primary" disabled>Submit Request</button>
        </div>
    @endif
@else
    <div>You cannot submit this request.</div>
@endif
