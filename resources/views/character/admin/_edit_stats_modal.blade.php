{{ html()->form('POST', $isMyo ? 'admin/myo/' . $character->id . '/stats' : 'admin/character/' . $character->slug . '/stats')->open() }}
@if ($isMyo)
    <div class="form-group">
        {{ html()->label('Name') }}
        {{ html()->text('name', $character->name)->class('form-control') }}
    </div>
@else
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {{ html()->label('Character Category') }}
                {{ html()->select('character_category_id', $categories, $character->category->id)->class('form-control') }}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {{ html()->label('Number') }} {!! add_help('This number helps to identify the character and should preferably be unique either within the category, or among all characters.') !!}
                {{ html()->text('number', $number)->class('form-control mr-2')->id('number') }}
            </div>
        </div>
    </div>
    <div class="form-group">
        {{ html()->label('Character Code') }} {!! add_help('This code identifies the character itself. This must be unique among all characters (as it\'s used to generate the character\'s page URL).') !!}
        {{ html()->text('slug', $character->slug)->class('form-control')->id('code') }}
    </div>
@endif

<div class="alert alert-info">
    These are displayed on the character's profile, but don't have any effect on site functionality except for the following:
    <ul>
        <li>If all switches are off, the character cannot be transferred by the user (directly or through trades).</li>
        <li>If a transfer cooldown is set, the character also cannot be transferred by the user (directly or through trades) until the cooldown is up.</li>
    </ul>
</div>
<div class="form-group">
    {{ html()->checkbox('is_giftable', $character->is_giftable, 1)->class('form-check-input')->data('toggle', 'toggle') }}
    {{ html()->label('Is Giftable', 'is_giftable')->class('form-check-label ml-3') }}
</div>
<div class="form-group">
    {{ html()->checkbox('is_tradeable', $character->is_tradeable, 1)->class('form-check-input')->data('toggle', 'toggle') }}
    {{ html()->label('Is Tradeable', 'is_tradeable')->class('form-check-label ml-3') }}
</div>
<div class="form-group">
    {{ html()->checkbox('is_sellable', $character->is_sellable, 1)->class('form-check-input')->data('toggle', 'toggle')->id('resellable') }}
    {{ html()->label('Is Resellable', 'is_sellable')->class('form-check-label ml-3') }}
</div>
<div class="form-group">
    {{ html()->label('Resale Value') }} {!! add_help('This value is publicly displayed on the character\'s page. It\'s hidden if zero or lower.') !!}
    {{ html()->text('sale_value', $character->sale_value)->class('form-control') }}
</div>
<div class="form-group">
    {{ html()->label('On Transfer Cooldown Until (Optional)') }}
    <div class="input-group">
        {{ html()->text('transferrable_at', $character->transferrable_at)->class('form-control datepickeralt') }}
        <div class="input-group-append">
            <a class="btn btn-info collapsed" href="#collapsedt" data-toggle="collapse"><i class="fas fa-calendar-alt"></i></a>
        </div>
    </div>
    <div class="collapse datepicker" id="collapsedt"></div>
</div>

<div class="text-right">
    {{ html()->submit('Edit')->class('btn btn-primary') }}
</div>
{{ html()->form()->close() }}

@include('widgets._datetimepicker_js', ['dtinline' => 'datepickeralt', 'dtvalue' => $character->transferrable_at])
