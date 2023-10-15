@extends('user.layout')

@section('profile-title')
    {{ $user->name }}'s Borders
@endsection

@section('profile-content')
    {!! breadcrumbs(['Users' => 'users', $user->name => $user->url, 'Borders' => $user->url . '/borders']) !!}

    <h1>
        {!! $user->displayName !!}'s Borders
    </h1>

    <h4>Default</h4>
    <div class="row mb-3">
        @foreach ($default as $border)
            <div class="class="col-md-3 col-6 mb-3 text-center">
                <div class="shop-image">
                    {!! $border->preview($user->id) !!}
                </div>
                <div class="shop-name mt-1 text-center">
                    <h5>{!! $border->displayName !!}</h5>
                </div>
            </div>
        @endforeach
    </div>
    @if ($user->borders->count())
    <h4>Unlocked</h4>
    <div class="row mb-3">
        @foreach ($user->borders as $border)
            <div class="class="col-md-3 col-6 mb-3 text-center">
                <div class="shop-image">
                    {!! $border->preview($user->id) !!}
                </div>
                <div class="shop-name mt-1 text-center">
                    <h5>{!! $border->displayName !!}</h5>
                </div>
            </div>
        @endforeach
    </div>
    @endif
    @if ($user->isStaff)
        <h4>Staff-Only</h4>
        <small>{!! $user->displayName !!} has access to these as a member of staff</small>
        <div class="row mb-3">
            @foreach ($admin as $border)
                <div class="class="col-md-3 col-6 mb-3 text-center">
                    <div class="shop-image">
                        {!! $border->preview($user->id) !!}
                    </div>
                    <div class="shop-name mt-1 text-center">
                        <h5>{!! $border->displayName !!}</h5>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
    <h3>Latest Activity</h3>
    <div class="row ml-md-2 mb-4">
        <div class="d-flex row flex-wrap col-12 mt-1 pt-1 px-0 ubt-bottom">
            <div class="col-6 col-md-2 font-weight-bold">Sender</div>
            <div class="col-6 col-md-2 font-weight-bold">Recipient</div>
            <div class="col-6 col-md-2 font-weight-bold">Border</div>
            <div class="col-6 col-md-4 font-weight-bold">Log</div>
            <div class="col-6 col-md-2 font-weight-bold">Date</div>
        </div>
        @foreach ($logs as $log)
            <div class="d-flex row flex-wrap col-12 mt-1 pt-1 px-0 ubt-top">
                <div class="col-6 col-md-2">
                    <i
                        class="btn py-1 m-0 px-2 btn-{{ $log->recipient_id == $user->id ? 'success' : 'danger' }} fas {{ $log->recipient_id == $user->id && $log->recipient_type == $user->logType ? 'fa-arrow-up' : 'fa-arrow-down' }} mr-2"></i>
                    {!! $log->sender ? $log->sender->displayName : '' !!}
                </div>
                <div class="col-6 col-md-2">{!! $log->recipient ? $log->recipient->displayName : '' !!}</div>
                <div class="col-6 col-md-2">{!! $log->border ? $log->border->displayName : '(Deleted Border)' !!}</div>
                <div class="col-6 col-md-4">{!! $log->log !!}</div>
                <div class="col-6 col-md-2">{!! pretty_date($log->created_at) !!}</div>
            </div>
        @endforeach
    </div>
    <div class="text-right">
        <a href="{{ url($user->url . '/border-logs') }}">View all...</a>
    </div>
@endsection