<div class="card mb-4">
    <div class="card-header d-flex flex-column flex-sm-row justify-content-between align-items-center">
        <h4 class="mb-0"><i class="fas fa-money-bill-wave"></i> Recent Sales</h4>
        <a href="{{ url('sales') }}" class="btn btn-primary">View All Sales <i class="fas fa-arrow-right"></i></a>
    </div>

    <div class="card-body pt-0">
        @if ($saleses->count())
            @foreach ($saleses as $sales)
                <div class="row {{ $loop->last ? '' : 'border-bottom' }} py-3 align-items-center">
                    <div class="col-md-3 text-center">
                        <a href="{{ $sales->url }}">
                            @if ($sales->has_image)
                                <img src="{{ $sales->imageUrl }}" alt="{!! $sales->name !!}" class="img-thumbnail" />
                            @else
                                @if ($sales->characters->count())
                                    <img src="{{ $sales->characters->first()->character->image->thumbnailUrl }}" alt="{!! $sales->characters->first()->character->fullName !!}" class="img-thumbnail" />
                                @endif
                            @endif
                        </a>
                    </div>

                    <div class="{{ $sales->characters->count() || $sales->has_image ? 'col-md-9' : 'col-12' }} d-flex flex-column justify-content-center">
                        <span class="d-flex flex-column flex-sm-row align-items-sm-end">
                            <h5 class="mb-0">{!! $sales->displayName !!}</h5>
                            <span class="ml-2 small">Posted {!! $sales->post_at ? pretty_date($sales->post_at) : pretty_date($sales->created_at) !!} :: Last edited {!! pretty_date($sales->updated_at) !!} by {!! $sales->user->displayName !!}</span>
                        </span>
                        @if ($sales->characters->count())
                            <div class="pl-3">
                                <b>{!! $sales->characters->first()->price !!} ({{ $sales->characters->first()->displayType }})</b>
                                @if ($sales->characters->first()->description)
                                    <br>{!! $sales->characters->first()->description !!}
                                @endif
                                <br>
                                <b>Artist:</b>
                                @foreach ($sales->characters->first()->character->image->artists as $artist)
                                    <br><span class="pl-2">{!! $artist->displayLink() !!}</span>
                                @endforeach
                                <br>
                                <b>Designer:</b>
                                @foreach ($sales->characters->first()->character->image->designers as $designer)
                                    <br><span class="pl-2">{!! $designer->displayLink() !!}</span>
                                @endforeach
                                <br>
                                @if ($sales->characters->count() == 1)
                                    <a href="{{ $sales->url }}" class="btn btn-secondary mt-2">View Character For {{ $sales->characters->first()->displayType }} <i class="fas fa-arrow-right"></i></a>
                                @else
                                    <a href="{{ $sales->url }}" class="btn btn-secondary mt-2">View {!! $sales->characters->count() !!} Characters For {{ $sales->characters->first()->displayType }} <i class="fas fa-arrow-right"></i></a>
                                @endif
                            </div>
                        @else
                            <p class="pl-3 mb-0">{!! substr(strip_tags(str_replace('<br />', '&nbsp;', $sales->parsed_text)), 0, 300) !!}... <a href="{!! $sales->url !!}">View sale <i class="fas fa-arrow-right"></i></a></p>
                        @endif
                    </div>
                </div>
            @endforeach
        @else
            <div class="text-center pt-3">
                <h5 class="mb-0">There are no sales.</h5>
            </div>
        @endif
    </div>
</div>
