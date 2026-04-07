@extends('character.layout', ['isMyo' => $character->is_myo_slot])

@section('profile-title')
    {{ $character->fullName }}'s {{ ucfirst(__('links.links')) }}
@endsection

@section('meta-img')
    {{ $character->image->thumbnailUrl }}
@endsection

@section('profile-content')
    {!! breadcrumbs([
        $character->category->masterlist_sub_id ? $character->category->sublist->name . ' Masterlist' : 'Character masterlist' => $character->category->masterlist_sub_id ? 'sublist/' . $character->category->sublist->key : 'masterlist',
        $character->fullName => $character->url,
        ucfirst(__('links.links')) => $character->url . '/' . __('links.links'),
    ]) !!}

    @include('character._header', ['character' => $character])

    <div class="row no-gutters justify-content-between mb-3">
        <div class="col-auto">
            <h3 class="mb-0">{{ $character->fullName }}'s {{ ucfirst(__('links.links')) }}</h3>
        </div>
        <div class="col-auto d-flex align-items-center">
            @if (Auth::check() && Auth::user()->id == $character->user_id && count($links) > 1)
                <a href="#" id="toggle-sort-btn" class="btn btn-outline-secondary btn-sm mr-2">
                    <i class="fas fa-sort mr-1"></i>Sort
                </a>
            @endif
            <a href="{{ url('reports/new?url=') . $character->url . '/' . __('links.links') }}">
                <i class="fas fa-exclamation-triangle text-danger" data-toggle="tooltip" title="Report this character's {{ __('links.links') }}." style="opacity: 50%;"></i>
            </a>
        </div>
    </div>

    {{-- Normal view --}}
    <div id="links-view">
        @if (count($links))
            @foreach ($links as $link)
                <div class="row no-gutters {{ $loop->last ? 'mb-3' : 'mb-2' }}">
                    @include('character._link_character', ['character' => $link->characterOne, 'link' => $link])
                    @include('character._link_character', ['character' => $link->characterTwo, 'link' => $link])
                </div>
                {!! !$loop->last ? '<hr>' : '' !!}
            @endforeach
        @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> This character currently has no established {{ __('links.links') }}.
            </div>
        @endif
    </div>

    {{-- Sort mode (owner only, shown when toggled) --}}
    @if (Auth::check() && Auth::user()->id == $character->user_id && count($links) > 1)
        <div id="links-sort" class="d-none">
            <p class="text-muted small mb-2">
                <i class="fas fa-info-circle mr-1"></i>
                Drag to reorder. This only changes <strong>{{ $character->name ?? $character->slug }}</strong>'s sort order — the other character's order is unaffected.
            </p>

            <table class="table table-sm">
                <tbody id="linksSortable" class="sortable">
                    @foreach ($links as $link)
                        @php $other = $link->getOtherCharacter($character->id); @endphp
                        <tr class="sort-item" data-id="{{ $link->id }}">
                            <td class="align-middle" style="width: 1px;">
                                <a class="fas fa-arrows-alt-v handle text-muted px-2" href="#"></a>
                            </td>
                            <td class="align-middle" style="width: 40px;">
                                <img src="{{ $other->image->thumbnailUrl }}" class="img-thumbnail" style="width: 36px; height: 36px; object-fit: cover;" alt="{{ $other->fullName }}" />
                            </td>
                            <td class="align-middle">
                                <span class="font-weight-bold">{{ $other->fullName }}</span>
                                <span class="badge badge-secondary ml-1">{{ $link->getRelationType($character->id) }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {!! Form::open(['url' => 'character/' . $character->slug . '/' . __('links.links') . '/sort']) !!}
            {!! Form::hidden('sort', '', ['id' => 'linksSortOrder']) !!}
            <div class="d-flex justify-content-end">
                {!! Form::submit('Save Order', ['class' => 'btn btn-primary btn-sm']) !!}
            </div>
            {!! Form::close() !!}
        </div>
    @endif

    <div class="text-right mt-3">
        @if (Auth::check() && ($character->user_id == Auth::user()->id || Auth::user()->hasPower('manage_characters')))
            <a href="{{ $character->url . '/' . __('links.links') . '/edit' }}" class="btn btn-outline-info btn-sm mb-1">
                <i class="fas fa-envelope mr-1" aria-hidden="true"></i>Create {{ ucfirst(__('links.links')) }} For {!! $character->name ?? $character->slug !!}
            </a>
        @endif
        <a href="{{ $character->url . '/' . __('links.links') . '-logs' }}" class="btn btn-outline-info btn-sm mb-1">
            <i class="fas fa-book mr-1" aria-hidden="true"></i>View Logs
        </a>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Edit link modal
            $('.edit-link-btn').on('click', function(e) {
                e.preventDefault();
                loadModal("{{ url('character') }}/" + $(this).data('slug') + "/{{ __('links.links') }}/info/" + $(this).data('id'), 'Edit {{ ucfirst(__('links.link')) }}');
            });

            // Feature toggle
            $('.feature-link-btn').on('click', function(e) {
                e.preventDefault();
                var btn = $(this);
                var icon = btn.find('i');
                $.post(
                    "{{ url('character') }}/" + btn.data('slug') + "/{{ __('links.links') }}/feature/" + btn.data('id'), {
                        _token: "{{ csrf_token() }}"
                    },
                    function(data) {
                        if (data.featured) {
                            icon.removeClass('far').addClass('fas');
                            btn.attr('title', 'Unfeature this {{ __('links.link') }}');
                        } else {
                            icon.removeClass('fas').addClass('far');
                            btn.attr('title', 'Feature this {{ __('links.link') }}');
                        }
                        btn.tooltip('dispose').tooltip();
                    }
                ).fail(function(xhr) {
                    var msg = xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : 'An error occurred.';
                    alert(msg);
                });
            });

            // Sort mode toggle
            $('#toggle-sort-btn').on('click', function(e) {
                e.preventDefault();
                var inSort = $('#links-sort').hasClass('d-none');
                $('#links-view').toggleClass('d-none', inSort);
                $('#links-sort').toggleClass('d-none', !inSort);
                $(this).text(inSort ? ' Done' : ' Sort')
                    .find('i').attr('class', inSort ? 'fas fa-times mr-1' : 'fas fa-sort mr-1');
            });

            // Sortable list
            $('#linksSortable').sortable({
                items: '.sort-item',
                handle: '.handle',
                placeholder: 'sortable-placeholder',
                stop: function() {
                    $('#linksSortOrder').val($(this).sortable('toArray', {
                        attribute: 'data-id'
                    }));
                },
                create: function() {
                    $('#linksSortOrder').val($(this).sortable('toArray', {
                        attribute: 'data-id'
                    }));
                }
            });
            $('#linksSortable').disableSelection();

            $('.handle').on('click', function(e) {
                e.preventDefault();
            });
        });
    </script>
@endsection
