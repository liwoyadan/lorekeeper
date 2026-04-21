@if ($species)
    {{ html()->form('POST', 'admin/data/species/delete/' . $species->id)->open() }}

    <p>You are about to delete the species <strong>{{ $species->name }}</strong>. This is not reversible. If traits and/or characters that have this species exist, you will not be able to delete this species.</p>
    <p>Are you sure you want to delete <strong>{{ $species->name }}</strong>?</p>

    <div class="text-right">
        {{ html()->submit('Delete Species')->class('btn btn-danger') }}
    </div>

    {{ html()->form()->close() }}
@else
    Invalid species selected.
@endif
