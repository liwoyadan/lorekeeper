@extends('admin.layout')

@section('admin-title')
    Housing
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Housing' => 'admin/data/housing']) !!}

    <h1>
        Housing Decor</h1>

    <p>These are the decor pieces players can obtain and place: furniture, walls, and floors. Recolor zones for each piece are managed on its edit page.</p>

    <div class="text-right mb-3"><a class="btn btn-primary" href="{{ url('admin/data/housing/create') }}"><i class="fas fa-plus"></i> Create New Decor</a></div>
    @if (!count($decors))
        <p>No decor found.</p>
    @else
        <table class="table table-sm decor-table">
            <tbody id="sortable" class="sortable">
                @foreach ($decors as $decor)
                    <tr class="sort-item" data-id="{{ $decor->id }}">
                        <td>
                            <a class="fas fa-arrows-alt-v handle mr-3" href="#"></a>
                            @if ($decor->has_image)
                                <img src="{{ $decor->decorImageUrl }}" style="width:32px; height:32px; object-fit:contain; vertical-align:middle;" class="mr-2" alt="">
                            @endif
                            {{ $decor->name }}
                            <span class="badge badge-secondary ml-1">{{ $decor->kindLabel }}@if ($decor->layerLabel)
                                    &middot; {{ $decor->layerLabel }}
                                @endif
                            </span>
                            @if (!$decor->is_visible)
                                <i class="fas fa-eye-slash ml-1" data-toggle="tooltip" title="This decor is hidden."></i>
                            @endif
                        </td>
                        <td class="text-right">
                            <a href="{{ url('admin/data/housing/edit/' . $decor->id) }}" class="btn btn-primary">Edit</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mb-4">
            {!! Form::open(['url' => 'admin/data/housing/sort']) !!}
            {!! Form::hidden('sort', '', ['id' => 'sortableOrder']) !!}
            {!! Form::submit('Save Order', ['class' => 'btn btn-primary']) !!}
            {!! Form::close() !!}
        </div>
    @endif
@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            $('.handle').on('click', function(e) {
                e.preventDefault();
            });
            $("#sortable").sortable({
                items: '.sort-item',
                handle: ".handle",
                placeholder: "sortable-placeholder",
                stop: function(event, ui) {
                    $('#sortableOrder').val($(this).sortable("toArray", {
                        attribute: "data-id"
                    }));
                },
                create: function() {
                    $('#sortableOrder').val($(this).sortable("toArray", {
                        attribute: "data-id"
                    }));
                }
            });
            $("#sortable").disableSelection();
        });
    </script>
@endsection
