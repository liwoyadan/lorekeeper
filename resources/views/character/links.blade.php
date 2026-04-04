@extends('character.layout', ['isMyo' => $character->is_myo_slot])

@section('profile-title')
    {{ $character->fullName }}'s Links
@endsection

@section('meta-img')
    {{ $character->image->thumbnailUrl }}
@endsection

@section('profile-content')
    {!! breadcrumbs([
        $character->category->masterlist_sub_id ? $character->category->sublist->name . ' Masterlist' : 'Character masterlist' => $character->category->masterlist_sub_id ? 'sublist/' . $character->category->sublist->key : 'masterlist',
        $character->fullName => $character->url,
        'Links' => $character->url . '/links',
    ]) !!}

    @include('character._header', ['character' => $character])

    <div class="row no-gutters justify-content-between">
        <div class="col-auto">
            <h3>
                {{ $character->fullName }}'s Links
            </h3>
        </div>

        <div class="col-auto">
            <div class="text-right">
                <a class="small" href="{{ url('reports/new?url=') . $character->url . '/links' }}">
                    <i class="fas fa-exclamation-triangle text-danger" data-toggle="tooltip" title="Click here to report this character's links." style="opacity: 50%;"></i>
                </a>
            </div>
        </div>
    </div>

    @if (count($character->links))
        @foreach ($character->links as $link)
            <div class="row no-gutters align-items-center mb-2 position-relative character-link-row mt-4">
                @include('character._link_character', ['character' => $link->characterOne, 'direction' => 'left', 'link' => $link])

                @include('character._link_character', ['character' => $link->characterTwo, 'direction' => 'right', 'link' => $link])
            </div>

            @if (!$loop->last)
                <hr style="border-style: dashed;">
            @endif
        @endforeach
    @else
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> This character currently has no established links.
        </div>
    @endif

    <div class="text-right mt-3">
        @if (Auth::check() && ($character->user_id == Auth::user()->id || Auth::user()->hasPower('manage_characters')))
            <a href="{{ $character->url . '/links/edit' }}" class="btn btn-outline-info btn-sm mb-1">
                <i class="fas fa-envelope mr-1" aria-hidden="true"></i>Create Links For {!! $character->name ?? $character->slug !!}
            </a>
        @endif
        <a href="{{ $character->url . '/relationship-logs' }}" class="btn btn-outline-info btn-sm">
            <i class="fas fa-book mr-1" aria-hidden="true"></i>View Logs
        </a>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('.edit-link-btn').on('click', function(e) {
                e.preventDefault();
                loadModal("{{ url('character') }}/" + $(this).data('slug') + "/links/info/" + $(this).data('id'), 'Edit Link');
            });
        });
    </script>
@endsection
