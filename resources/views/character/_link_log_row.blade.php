<div class="row flex-wrap no-gutters">
    <div class="col-6 col-md-2">
        <div class="logs-table-cell">
            {!! $log->sender ? $log->sender->displayName : '' !!}
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="logs-table-cell">
            {!! $log->recipient ? $log->recipient->displayName : '' !!}
        </div>
    </div>
    <div class="col-6 col-md-1">
        <div class="logs-table-cell">
            @if ($log->characterOne)
                <a href="{{ $log->characterOne->url }}" class="font-weight-bold">
                    {{ $log->characterOne->slug }}
                </a>
            @endif
        </div>
    </div>
    <div class="col-6 col-md-1">
        <div class="logs-table-cell">
            @if ($log->characterTwo)
                <a href="{{ $log->characterTwo->url }}" class="font-weight-bold">
                    {{ $log->characterTwo->slug }}
                </a>
            @endif
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="logs-table-cell">
            {!! $log->log !!}
        </div>
    </div>
    <div class="col-6 col-md-2">
        <div class="logs-table-cell">
            {!! pretty_date($log->created_at) !!}
        </div>
    </div>
</div>
