@extends('shops.layout')

@section('shops-title')
    Shop Index
@endsection

@section('shops-content')
    {!! breadcrumbs(['Shops' => 'shops']) !!}

    <h1>
        Shops
    </h1>

    <div class="row shops-row">
        @foreach ($shops as $shop)
            <div class="col-md-3 col-6 mb-3 text-center">
                @if ($shop->has_image)
                    <div class="shop-image">
                        <a href="{{ $shop->url }}"><img src="{{ $shop->shopImageUrl }}" alt="{{ $shop->name }}" /></a>
                    </div>
                @endif
                <div class="shop-name mt-1">
                    <a href="{{ $shop->url }}" class="h5 mb-0">{{ $shop->name }}</a>
                </div>
                @if (isset($shop->blurb) && $shop->blurb)
                    <div class="card">
                        <div class="card-header font-weight-bold py-1" role="button" data-toggle="collapse" data-target="#collapse{{ $shop->id }}" aria-conrols="collapse{{ $shop->id }}">
                            Show Info
                        </div>
                        <div class="collapse" id="collapse{{ $shop->id }}">
                            <div class="shop-blurb p-2">
                                {!! $shop->parsed_blurb !!}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
@endsection
