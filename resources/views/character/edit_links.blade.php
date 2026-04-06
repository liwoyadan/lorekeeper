@extends('character.layout', ['isMyo' => $character->is_myo_slot])

@section('profile-title')
    Editing {{ $character->fullName }}'s {{ ucfirst(__('links.links')) }}
@endsection

@section('meta-img')
    {{ $character->image->thumbnailUrl }}
@endsection

@section('profile-content')
    {!! breadcrumbs([
        $character->category->masterlist_sub_id ? $character->category->sublist->name . ' Masterlist' : 'Character masterlist' => $character->category->masterlist_sub_id ? 'sublist/' . $character->category->sublist->key : 'masterlist',
        $character->fullName => $character->url,
        'Editing ' . ucfirst(__('links.links')) => $character->url . '/' . __('links.links') . '/edit',
    ]) !!}

    @include('character._header', ['character' => $character])

    <div class="alert alert-info">
        Initiating a request will create a one-to-one {{ __('links.link') }} between both characters that requires the other character owner's approval. <b>If you own both characters it will auto-link</b> and not require approval.
    </div>

    <h3>
        Pending Requests
    </h3>
    @if ($character->pendingLinks->count())
        @foreach ($character->pendingLinks as $pendingLink)
            @include('character._pending_link', ['character' => $character, 'link' => $pendingLink, 'otherCharacter' => $pendingLink->getOtherCharacter($character->id), 'recipient' => $pendingLink->initialLog()->recipient])
        @endforeach
    @else
        <p class="text-muted text-center mb-0">
            {{ $character->fullName }} currently has no pending {{ __('links.link') }} requests.
        </p>
    @endif

    <hr>

    <h3>
        Establish {{ ucfirst(__('links.link')) }}
    </h3>
    {!! Form::open(['url' => $character->url . '/' . __('links.links') . '/edit']) !!}

    @include('widgets._link_select', ['character' => $character, 'linkItems' => $linkItems])

    <div class="text-right mt-3">
        {!! Form::submit('Request ' . ucfirst(__('links.link')), ['class' => 'btn btn-primary']) !!}
    </div>

    {!! Form::close() !!}
@endsection

@section('scripts')
    @parent
    @include('js._link_select_js')
@endsection
