<p>You are about to delete your bookmark for {!! $bookmark->character->displayName !!}. Are you sure?</p>
<div class="text-right">
    {{ html()->form('POST', 'account/bookmarks/delete/' . $bookmark->id)->open() }}
    {{ html()->submit('Delete Bookmark')->class('btn btn-danger') }}
    {{ html()->form()->close() }}
</div>
