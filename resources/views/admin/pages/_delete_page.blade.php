@if ($page)
    {{ html()->form('POST', 'admin/pages/delete/' . $page->id)->open() }}

    <p>You are about to delete the page <strong>{{ $page->name }}</strong>. This is not reversible. If you would like to preserve the content while preventing users from accessing the page, you can use the viewable setting instead to hide the page.
    </p>
    <p>Are you sure you want to delete <strong>{{ $page->name }}</strong>?</p>

    <div class="text-right">
        {{ html()->submit('Delete Page')->class('btn btn-danger') }}
    </div>

    {{ html()->form()->close() }}
@else
    Invalid page selected.
@endif
