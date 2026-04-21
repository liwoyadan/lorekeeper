@if ($sales)
    {{ html()->form('POST', 'admin/sales/delete/' . $sales->id)->open() }}

    <p>You are about to delete the sales post <strong>{{ $sales->title }}</strong>. This is not reversible. If you would like to preserve the content while preventing users from accessing the post, you can use the viewable setting instead to hide
        the post.</p>
    <p>Are you sure you want to delete <strong>{{ $sales->title }}</strong>?</p>

    <div class="text-right">
        {{ html()->submit('Delete Post')->class('btn btn-danger') }}
    </div>

    {{ html()->form()->close() }}
@else
    Invalid post selected.
@endif
