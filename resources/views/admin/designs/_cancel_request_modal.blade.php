<p>This will cancel the design approval request, returning it to draft form and allowing the user to edit it again.</p>
{{ html()->form('POST', 'admin/designs/edit/' . $request->id . '/cancel')->open() }}
<div class="form-group">
    {{ html()->label('Comment', 'staff_comments') }} {!! add_help('Enter a comment for the user. They will see this on their request page.') !!}
    {{ html()->textarea('staff_comments', $request->staff_comment)->class('form-control') }}
</div>
<div class="form-group">
    {{ html()->checkbox('preserve_queue', 1, 1)->class('form-check-input')->attribute('data-toggle', 'toggle') }}
    {{ html()->label('Preserve Queue Position', 'preserve_queue')->class('form-check-label ml-3') }} {!! add_help('Allows the user to avoid needing to wait for their request to return to the front of the queue. If this is turned off, the request will go into the back of the queue as per normal.') !!}
</div>
<div class="text-right">
    {{ html()->submit('Cancel Request')->class('btn btn-secondary') }}
</div>
{{ html()->form()->close() }}
