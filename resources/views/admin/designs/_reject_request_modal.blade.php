<p>This will reject the design approval request, which closes the request and disallows the user from editing it further. Any attached items and/or currency will be returned.</p>
{{ html()->form('POST', 'admin/designs/edit/' . $request->id . '/reject')->open() }}
<div class="form-group">
    {{ html()->label('Comment', 'staff_comments') }} {!! add_help('Enter a comment for the user. They will see this on their request page.') !!}
    {{ html()->textarea('staff_comments', $request->staff_comments)->class('form-control') }}
</div>
<div class="text-right">
    {{ html()->submit('Reject Request')->class('btn btn-danger') }}
</div>
{{ html()->form()->close() }}
