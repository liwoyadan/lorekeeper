@extends('admin.layout')

@section('admin-title')
    Character Transfers
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Character Transfer Queue' => 'admin/masterlist/transfers/incoming']) !!}

    <h1>
        Character Transfers
    </h1>

    @include('admin.masterlist._header', ['tradeCount' => $tradeCount, 'transferCount' => $transferCount])

    <div>
        {{ html()->form('GET')->class('form-inline justify-content-end')->open() }}
        <div class="form-inline justify-content-end">
            <div class="form-group ml-3 mb-3">
                {{ html()->select(
                        'sort',
                        [
                            'newest' => 'Newest First',
                            'oldest' => 'Oldest First',
                        ],
                        Request::get('sort') ?: 'oldest',
                    )->class('form-control') }}
            </div>
            <div class="form-group ml-3 mb-3">
                {{ html()->submit('Search')->class('btn btn-primary') }}
            </div>
        </div>
        {{ html()->form()->close() }}
    </div>

    {!! $transfers->render() !!}
    @foreach ($transfers as $transfer)
        @include('admin.masterlist._transfer', ['transfer' => $transfer])
    @endforeach
    {!! $transfers->render() !!}
@endsection


@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            $('.transfer-action-button').on('click', function(e) {
                e.preventDefault();
                console.log("{{ url('admin/masterlist/transfer/act') }}/" + $(this).data('id') + "/" + $(this).data('action'));
                loadModal("{{ url('admin/masterlist/transfer/act') }}/" + $(this).data('id') + "/" + $(this).data('action'), 'Process Transfer');
            });
        });
    </script>
@endsection
