@if (!$raffle->id)
    <p>
        Enter basic information about this raffle. Tickets can be added after the raffle is created.
    </p>
@endif
{{ html()->form('POST', 'admin/raffles/edit/raffle/' . ($raffle->id ?: ''))->open() }}
<div class="form-group">
    {{ html()->label('Raffle Name', 'name') }} {!! add_help('This is the name of the raffle. Naming it something after what is being raffled is suggested (does not have to be unique).') !!}
    {{ html()->text('name', $raffle->name)->class('form-control') }}
</div>
<div class="form-group">
    {{ html()->label('Number of Winners to Draw', 'winner_count') }}
    {{ html()->text('winner_count', $raffle->winner_count)->class('form-control') }}
</div>
<div class="form-group">
    {{ html()->label('Raffle Group', 'group_id') }} {!! add_help('Raffle groups must be created before you can select them here.') !!}
    {{ html()->select('group_id', $groups, $raffle->group_id)->class('form-control') }}
</div>
<div class="form-group">
    {{ html()->label('Raffle Order', 'order') }} {!! add_help('Enter a number. If a group of raffles is rolled, raffles will be drawn in ascending order.') !!}
    {{ html()->text('order', $raffle->order ?: 0)->class('form-control') }}
</div>
<div class="form-group">
    {{ html()->label('Ticket Cap (Optional)', 'ticket_cap') }} {!! add_help('A number of tickets per individual to cap at. Leave empty or unset to have no cap.') !!}
    {{ html()->text('ticket_cap', $raffle->ticket_cap ?: null)->class('form-control') }}
</div>
<div class="form-group">
    <label class="control-label">
        {{ html()->checkbox('is_active', $raffle->is_active, 1)->class('form-check-input mr-2')->attribute('data-toggle', 'toggle') }}
        {{ html()->label('Active (visible to users)', 'is_displayed')->class('form-check-label ml-3') }}
    </label>
</div>
<div class="text-right">
    {{ html()->submit('Confirm')->class('btn btn-primary') }}
</div>
{{ html()->form()->close() }}
