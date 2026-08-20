@extends('admin.layout')

@section('admin-title')
    Housing Patterns
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Housing Patterns' => 'admin/data/housing-patterns']) !!}

    <h1>Housing Patterns</h1>

    <p>These are tileable patterns that furniture, wall, and floor recolor zones can be filled with. They form a shared library referenced by decor zones.</p>

    <div class="text-right mb-3"><a class="btn btn-primary" href="{{ url('admin/data/housing-patterns/create') }}"><i class="fas fa-plus"></i> Create New Pattern</a></div>
    @if (!count($patterns))
        <p>No patterns found.</p>
    @else
        <table class="table table-sm pattern-table">
            <tbody id="sortable" class="sortable">
                @foreach ($patterns as $pattern)
                    <tr class="sort-item" data-id="{{ $pattern->id }}">
                        <td>
                            <a class="fas fa-arrows-alt-v handle mr-3" href="#"></a>
                            @if ($pattern->has_image)
                                <img src="{{ $pattern->patternImageUrl }}" style="width:32px; height:32px; object-fit:cover; vertical-align:middle;" class="mr-2" alt="">
                            @endif
                            {{ $pattern->name }}
                            @if (!$pattern->is_visible)
                                <i class="fas fa-eye-slash ml-1" data-toggle="tooltip" title="This pattern is hidden."></i>
                            @endif
                        </td>
                        <td class="text-right">
                            <a href="{{ url('admin/data/housing-patterns/edit/'.$pattern->id) }}" class="btn btn-primary">Edit</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mb-4">
            {!! Form::open(['url' => 'admin/data/housing-patterns/sort']) !!}
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
