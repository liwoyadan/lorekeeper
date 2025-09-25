<ul>
    <li class="sidebar-header"><a href="{{ url('raids') }}" class="card-link">Raids</a></li>

    @if ($currentRaid)
        <li class="sidebar-section text-center">
            <div class="sidebar-section-header h5 mb-0">Current Raid</div>
            <div class="sidebar-item">
                <a href="{{ url('raids/current') }}" class="font-weight-bold {{ set_active('raids/current*') }}">
                    {!! $currentRaid->name !!}
                </a>
            </div>
            @if ($currentRaid->start_at)
                <div class="sidebar-item small">
                    Started {!! pretty_date($currentRaid->start_at) !!}
                </div>
            @endif
            <div class="sidebar-item small">
                Until {!! $currentRaid->end_at ? format_date($currentRaid->end_at) : 'boss defeated.' !!}
            </div>
        </li>
    @endif

    <li class="sidebar-section">
        <div class="sidebar-section-header">Raid Data</div>
        <div class="sidebar-item"><a href="{{ url('raids') }}" class="{{ set_active('raids') }}">All Raids</a></div>
        <div class="sidebar-item"><a href="{{ url('raids/bosses') }}" class="{{ set_active('raids/bosses*') }}">Raid Bosses</a></div>
    </li>
</ul>
