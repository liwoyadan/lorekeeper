<div>
    {{ html()->form('GET')->open() }}
    <div class="form-inline justify-content-end">
        <div class="form-group mr-3 mb-3">
            {{ html()->label('Character Name/Code: ', 'name')->class('mr-2') }}
            {{ html()->text('name', Request::get('name'))->class('form-control') }}
        </div>
        <div class="form-group mb-3 mr-1">
            {{ html()->select('rarity_id', $rarities, Request::get('rarity_id'))->class('form-control mr-2')->placeholder('Any Rarity') }}
        </div>
        <div class="form-group mb-3">
            {{ html()->select('species_id', $specieses, Request::get('species_id'))->class('form-control')->placeholder('Any Species') }}
        </div>
    </div>
    <div class="text-right mb-3"><a href="#advancedSearch" class="btn btn-sm btn-outline-info" data-toggle="collapse">Show Advanced Search Options <i class="fas fa-caret-down"></i></a></div>
    <div class="card bg-light mb-3 collapse" id="advancedSearch">
        <div class="card-body masterlist-advanced-search">
            @if (!$isMyo)
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {{ html()->label('Category: ', 'character_category_id') }}
                            {{ html()->select('character_category_id', $categories, Request::get('character_category_id'))->class('form-control')->placeholder('Any Category') }}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {{ html()->label('Species Subtype: ', 'subtype_ids[]') }}
                            {!! add_help('Search for characters that have <strong>' . (config('lorekeeper.extensions.exclusionary_search') ? 'all' : 'any') . '</strong> of the selected subtypes.') !!}
                            {{ html()->select('subtype_ids[]', $subtypes, Request::get('subtype_ids'))->class('form-control userselectize')->multiple() }}
                        </div>
                    </div>
                </div>
                <hr />
            @endif
            <div class="masterlist-search-field">
                {{ html()->label('Owner Username: ', 'owner') }}
                {{ html()->select('owner', $userOptions, Request::get('owner'))->class('form-control mr-2 userselectize')->attribute('style', 'width: 250px')->placeholder('Select a User') }}
            </div>
            <div class="masterlist-search-field">
                {{ html()->label('Artist: ', 'artist') }}
                {{ html()->select('artist', $userOptions, Request::get('artist'))->class('form-control mr-2 userselectize')->attribute('style', 'width: 250px')->placeholder('Select a User') }}
            </div>
            <div class="masterlist-search-field">
                {{ html()->label('Designer: ', 'designer') }}
                {{ html()->select('designer', $userOptions, Request::get('designer'))->class('form-control mr-2 userselectize')->attribute('style', 'width: 250px')->placeholder('Select a User') }}
            </div>
            <hr />
            <div class="masterlist-search-field">
                {{ html()->label('Owner URL / Username: ', 'owner_url') }} {!! add_help('Example: https://deviantart.com/username OR username') !!}
                {{ html()->text('owner_url', Request::get('owner_url'))->class('form-control mr-2')->attribute('style', 'width: 250px')->placeholder('Type a Username') }}
            </div>
            <div class="masterlist-search-field">
                {{ html()->label('Artist URL / Username: ', 'artist_url') }} {!! add_help('Example: https://deviantart.com/username OR username') !!}
                {{ html()->text('artist_url', Request::get('artist_url'))->class('form-control mr-2')->attribute('style', 'width: 250px')->placeholder('Type a Username') }}
            </div>
            <div class="masterlist-search-field">
                {{ html()->label('Designer URL / Username: ', 'designer_url') }} {!! add_help('Example: https://deviantart.com/username OR username') !!}
                {{ html()->text('designer_url', Request::get('designer_url'))->class('form-control mr-2')->attribute('style', 'width: 250px')->placeholder('Type a Username') }}
            </div>
            <hr />
            <div class="masterlist-search-field">
                {{ html()->label('Resale Minimum ($): ', 'sale_value_min') }}
                {{ html()->text('sale_value_min', Request::get('sale_value_min'))->class('form-control mr-2')->attribute('style', 'width: 250px') }}
            </div>
            <div class="masterlist-search-field">
                {{ html()->label('Resale Maximum ($): ', 'sale_value_max') }}
                {{ html()->text('sale_value_max', Request::get('sale_value_max'))->class('form-control mr-2')->attribute('style', 'width: 250px') }}
            </div>
            @if (!$isMyo)
                <div class="masterlist-search-field">
                    {{ html()->label('Gift Art Status: ', 'is_gift_art_allowed') }}
                    {{ html()->select('is_gift_art_allowed', [2 => 'Ask First', 1 => 'Yes', 3 => 'Yes OR Ask First'], Request::get('is_gift_art_allowed'))->class('form-control')->attribute('style', 'width: 250px')->placeholder('Any') }}
                </div>
                <div class="masterlist-search-field">
                    {{ html()->label('Gift Writing Status: ', 'is_gift_writing_allowed') }}
                    {{ html()->select('is_gift_writing_allowed', [2 => 'Ask First', 1 => 'Yes', 3 => 'Yes OR Ask First'], Request::get('is_gift_writing_allowed'))->class('form-control')->attribute('style', 'width: 250px')->placeholder('Any') }}
                </div>
            @endif
            <br />
            {{-- Setting the width and height on the toggles as they don't seem to calculate correctly if the div is collapsed. --}}
            <div class="masterlist-search-field">
                {{ html()->checkbox('is_trading', Request::get('is_trading'), 1)->class('form-check-input')->attribute('data-toggle', 'toggle')->attribute('data-on', 'Open For Trade')->attribute('data-off', 'Any Trading Status')->attribute('data-width', '200')->attribute('data-height', '46') }}
            </div>
            <div class="masterlist-search-field">
                {{ html()->checkbox('is_sellable', Request::get('is_sellable'), 1)->class('form-check-input')->attribute('data-toggle', 'toggle')->attribute('data-on', 'Can Be Sold')->attribute('data-off', 'Any Sellable Status')->attribute('data-width', '204')->attribute('data-height', '46') }}
            </div>
            <div class="masterlist-search-field">
                {{ html()->checkbox('is_tradeable', Request::get('is_tradeable'), 1)->class('form-check-input')->attribute('data-toggle', 'toggle')->attribute('data-on', 'Can Be Traded')->attribute('data-off', 'Any Tradeable Status')->attribute('data-width', '220')->attribute('data-height', '46') }}
            </div>
            <div class="masterlist-search-field">
                {{ html()->checkbox('is_giftable', Request::get('is_giftable'), 1)->class('form-check-input')->attribute('data-toggle', 'toggle')->attribute('data-on', 'Can Be Gifted')->attribute('data-off', 'Any Giftable Status')->attribute('data-width', '202')->attribute('data-height', '46') }}
            </div>
            <hr />
            <div class="form-group">
                {{ html()->label('Has Traits: ') }} {!! add_help('This will narrow the search to characters that have ALL of the selected traits at the same time.') !!}
                {{ html()->select('feature_ids[]', $features, Request::get('feature_ids'))->class('form-control feature-select')->placeholder('Select Traits')->multiple() }}
            </div>
            @if (!$isMyo)
                <div class="row">
                    <div class="col-md-6 form-group">
                        {{ html()->label('Exclude Selected Tags: ') }} {!! add_help('This will exclude characters that have ANY of the selected tags.') !!}
                        {{ html()->select('excluded_tags[]', ['all' => 'Exclude All'] + $contentWarnings, Request::get('excluded_tags'))->class('form-control feature-select userselectize')->placeholder('Select Tags')->multiple() }}
                    </div>
                    <div class="col-md-6 form-group">
                        {{ html()->label('Include Selected Tags: ') }} {!! add_help('This will include characters that have ANY of the selected tags.') !!}
                        {{ html()->select('included_tags[]', ['all' => 'Include All'] + $contentWarnings, Request::get('included_tags'))->class('form-control feature-select userselectize')->placeholder('Select Tags')->multiple() }}
                    </div>
                </div>
            @endif
            <hr />
            <div class="form-group">
                {{ html()->checkbox('search_images', Request::get('search_images'), 1)->class('form-check-input')->attribute('data-toggle', 'toggle') }}
                {{ html()->label('Include all character images in search', 'search_images')->class('form-check-label ml-3') }} {!! add_help(
                    'Each character can have multiple images for each updated version of the character, which captures the traits on that character at that point in time. By default the search will only search on the most up-to-date image, but this option will retrieve characters that match the criteria on older images - you may get results that are outdated.',
                ) !!}
            </div>
        </div>

    </div>
    <div class="form-inline justify-content-end mb-3">
        <div class="form-group mr-3">
            {{ html()->label('Sort: ', 'sort')->class('mr-2') }}
            @if (!$isMyo)
                {{ html()->select(
                        'sort',
                        ['number_desc' => 'Number Descending', 'number_asc' => 'Number Ascending', 'id_desc' => 'Newest First', 'id_asc' => 'Oldest First', 'sale_value_desc' => 'Highest Sale Value', 'sale_value_asc' => 'Lowest Sale Value'],
                        Request::get('sort'),
                    )->class('form-control') }}
            @else
                {{ html()->select('sort', ['id_desc' => 'Newest First', 'id_asc' => 'Oldest First', 'sale_value_desc' => 'Highest Sale Value', 'sale_value_asc' => 'Lowest Sale Value'], Request::get('sort'))->class('form-control') }}
            @endif
        </div>
        {{ html()->submit('Search')->class('btn btn-primary') }}
    </div>
    {{ html()->form()->close() }}
</div>
<div class="text-right mb-3">
    <div class="btn-group">
        <button type="button" class="btn btn-secondary active grid-view-button" data-toggle="tooltip" title="Grid View" alt="Grid View"><i class="fas fa-th"></i></button>
        <button type="button" class="btn btn-secondary list-view-button" data-toggle="tooltip" title="List View" alt="List View"><i class="fas fa-bars"></i></button>
    </div>
</div>

{!! $characters->render() !!}
<div id="gridView" class="hide">
    @foreach ($characters->chunk(4) as $chunk)
        <div class="row">
            @foreach ($chunk as $character)
                <div class="col-md-3 col-6 text-center mb-3">
                    <div>
                        <a href="{{ $character->url }}">
                            <img src="{{ $character->image->thumbnailUrl }}" class="img-thumbnail {{ $character->image->showContentWarnings(Auth::user() ?? null) ? 'content-warning' : '' }}" alt="Thumbnail for {{ $character->fullName }}" />
                        </a>
                    </div>
                    <div class="mt-1">
                        <a href="{{ $character->url }}" class="h5 mb-0">
                            @if (!$character->is_visible)
                                <i class="fas fa-eye-slash"></i>
                            @endif {!! $character->warnings !!} {{ Illuminate\Support\Str::limit($character->fullName, 20, $end = '...') }}
                        </a>
                    </div>
                    <div class="small">
                        {!! $character->image->species_id ? $character->image->species->displayName : 'No Species' !!} ・ {!! $character->image->rarity_id ? $character->image->rarity->displayName : 'No Rarity' !!} ・ {!! $character->displayOwner !!}
                        @if (count($character->image->content_warnings ?? []) && (!Auth::check() || (Auth::check() && Auth::user()->settings->content_warning_visibility < 2)))
                            <p class="mb-0"><span class="text-danger mr-1"><strong>Character Warning:</strong></span> {{ implode(', ', $character->image->content_warnings) }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach
</div>
<div id="listView" class="hide">
    <table class="table table-sm">
        <thead>
            <tr>
                <th>Owner</th>
                <th>Name</th>
                <th>Rarity</th>
                <th>Species</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($characters as $character)
                <tr>
                    <td>{!! $character->displayOwner !!}</td>
                    <td>
                        @if (!$character->is_visible)
                            <i class="fas fa-eye-slash"></i>
                        @endif {!! $character->displayName !!}
                    </td>
                    <td>{!! $character->image->rarity_id ? $character->image->rarity->displayName : 'None' !!}</td>
                    <td>{!! $character->image->species_id ? $character->image->species->displayName : 'None' !!}</td>
                    <td>{!! format_date($character->created_at) !!}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
{!! $characters->render() !!}

<div class="text-center mt-4 small text-muted">{{ $characters->total() }} result{{ $characters->total() == 1 ? '' : 's' }} found.</div>
