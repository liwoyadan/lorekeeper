@if ($subtype)
    {{ html()->form('POST', 'admin/data/subtypes/delete/' . $subtype->id)->open() }}

    <p>You are about to delete the subtype <strong>{{ $subtype->name }}</strong>. This is not reversible. If traits and/or characters that have this subtype exist, you will not be able to delete this subtype.</p>
    <p>Are you sure you want to delete <strong>{{ $subtype->name }}</strong>?</p>

    <div class="text-right">
        {{ html()->submit('Delete Subtype')->class('btn btn-danger') }}
    </div>

    {{ html()->form()->close() }}
@else
    Invalid subtype selected.
@endif
