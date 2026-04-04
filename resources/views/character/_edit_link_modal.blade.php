{!! Form::open(['url' => 'character/' . $character->slug . '/links/info/' . $link->id]) !!}
<p>
    You are editing the relationship information for {!! $character->displayName !!} in their link with {!! $otherCharacter->displayName !!}. Here you may choose a title that your character feels about the other, as well as elaborate on their feelings in the text box below.
</p>

<div class="form-group">
    @if (isset($types) && $types && count($types) > 0)
        {!! Form::label('Relationship Title') !!}
        {!! Form::select('type', $types, $link->getRelationType($character->id), ['class' => 'form-control', 'placeholder' => 'Select a relationship title...']) !!}
    @elseif (Auth::user()->hasPower('manage_characters'))
        <div class="alert alert-warning">
            <i class="fas fa-exclamation" aria-hidden="true"></i> You are editing this link as a staff member but no specific relationship types are associated with this established link. Therefore, you may type a custom relationship title below.
        </div>
        {!! Form::label('Relationship Title') !!}
        {!! Form::text('type', $link->getRelationType($character->id), ['class' => 'form-control', 'placeholder' => 'Type a relationship title...']) !!}
    @else
        <div class="alert alert-danger">
            <i class="fas fa-exclamation" aria-hidden="true"></i> <b>No selectable relationship titles have been found.</b> If you believe this is an error, please create a bug report or contact a staff member.
        </div>
    @endif
</div>

<div class="form-group">
    {!! Form::label(($character->name ?? $character->slug) . '\'s Thoughts') !!}
    {!! Form::textarea('info', $link->getRelationshipInfo($character->id), ['class' => 'form-control wysiwyg']) !!}
</div>

<div class="text-right">
    {!! Form::submit('Edit Link', ['class' => 'btn btn-primary']) !!}
</div>
{!! Form::close() !!}

<hr class="my-2" style="border-style: dashed;">

<a class="btn btn-primary btn-sm" data-toggle="collapse" href="#deleteLink" role="button" aria-expanded="false" aria-controls="deleteLink">
    <i class="fas fa-trash"></i>
    Delete Link
</a>
<div class="collapse pt-2 px-2" id="deleteLink">
    <span class="text-danger font-weight-bold">
        <i class="fas fa-exclamation-triangle" aria-hidden="true"></i>
        Are you sure you want to delete this link? This action cannot be undone.
    </span>
    If you are sure, click the button below to delete this link. The owner of the other character (if not yourself) will not be notified of this deletion, but it will be recorded in both characters' logs.
    {!! Form::open(['url' => 'character/' . $character->slug . '/links/delete/' . $link->id]) !!}
    <div class="text-right">
        {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
    </div>
    {!! Form::close() !!}
</div>

<script>
    $(document).ready(function() {
        @include('js._modal_wysiwyg_simple')
    });
</script>
