<div class="card mb-3">
    <div class="card-header px-3 h5 mb-0 text-center">
        Recent Activity
    </div>

    <div class="card-body px-3 py-1">
        @if ($recentPosts->count())
            @foreach ($recentPosts as $recent)
                <div class="row no-gutters py-2 align-items-center {{ !$loop->last ? 'border-bottom' : '' }}">
                    <div class="col">
                        <div class="font-weight-bold">
                            {!! $recent->displayName !!}
                        </div>
                        <div class="small">
                            by {!! $recent->commenter->displayName !!}
                        </div>
                    </div>

                    <div class="col-auto col-md-12 col-xl-auto text-right small">
                        {!! pretty_date($recent->updated_at) !!}
                    </div>
                </div>
            @endforeach
        @else
            <div class="text-center text-muted py-2">
                No recent thread activity.
            </div>
        @endif
    </div>
</div>
