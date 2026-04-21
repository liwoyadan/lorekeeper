@if ($gallery)
    {{ html()->form('POST', 'admin/data/galleries/delete/' . $gallery->id)->open() }}

    <p>You are about to delete the gallery <strong>{{ $gallery->name }}</strong>. This is not reversible. If submissions in this gallery exist, or this gallery has sub-galleries, you will not be able to delete this gallery.</p>
    <p>Are you sure you want to delete <strong>{{ $gallery->name }}</strong>?</p>

    <div class="text-right">
        {{ html()->submit('Delete Gallery')->class('btn btn-danger') }}
    </div>

    {{ html()->form()->close() }}
@else
    Invalid gallery selected.
@endif
