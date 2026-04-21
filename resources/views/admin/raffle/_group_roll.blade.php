@if ($group->is_active < 2)
    <div class="text-center">
        <p>This will roll all the raffles in the group <strong>{{ $group->name }}</strong>. Winners of raffles that come first will be excluded from later raffles.</p>
        {{ html()->form('POST', 'admin/raffles/roll/group/' . $group->id)->open() }}
        {{ html()->submit('Roll!')->class('btn btn-primary') }}
        {{ html()->form()->close() }}
    </div>
@else
    <div class="text-center">This set of raffles has already been completed.</div>
@endif
