@extends('admin.layout')

@section('admin-title')
    {{ $feature->id ? 'Edit' : 'Create' }} Trait
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Traits' => 'admin/data/traits', ($feature->id ? 'Edit' : 'Create') . ' Trait' => $feature->id ? 'admin/data/traits/edit/' . $feature->id : 'admin/data/traits/create']) !!}

    <h1>{{ $feature->id ? 'Edit' : 'Create' }} Trait
        @if ($feature->id)
            <a href="#" class="btn btn-danger float-right delete-feature-button">Delete Trait</a>
        @endif
    </h1>

    {{ html()->form('POST', $feature->id ? 'admin/data/traits/edit/' . $feature->id : 'admin/data/traits/create')->acceptsFiles()->open() }}

    <h3>Basic Information</h3>

    <div class="row">
        <div class="col-md-6 form-group">
            {{ html()->label('Name') }}
            {{ html()->text('name', $feature->name)->class('form-control') }}
        </div>
        <div class="col-md-6 form-group">
            {{ html()->label('Rarity') }}
            {{ html()->select('rarity_id', $rarities, $feature->rarity_id)->class('form-control') }}
        </div>
    </div>

    <div class="form-group">
        {{ html()->label('World Page Image (Optional)') }} {!! add_help('This image is used only on the world information pages.') !!}
        <div class="custom-file">
            {{ html()->label('Choose file...', 'image')->class('custom-file-label') }}
            {{ html()->file('image')->class('custom-file-input') }}
        </div>
        <div class="text-muted">Recommended size: 200px x 200px</div>
        @if ($feature->has_image)
            <div class="form-check">
                {{ html()->checkbox('remove_image', false, 1)->class('form-check-input') }}
                {{ html()->label('Remove current image', 'remove_image')->class('form-check-label') }}
            </div>
        @endif
    </div>

    <div class="row">
        <div class="col-md-4 form-group">
            {{ html()->label('Trait Category (Optional)') }}
            {{ html()->select('feature_category_id', $categories, $feature->feature_category_id)->class('form-control') }}
        </div>
        <div class="col-md-4 form-group">
            {{ html()->label('Species Restriction (Optional)') }}
            {{ html()->select('species_id', $specieses, $feature->species_id)->class('form-control')->id('species') }}
        </div>
        <div class="col-md-4 form-group" id="subtypes">
            {{ html()->label('Subtypes (Optional)') }} {!! add_help('This is cosmetic and does not limit choice of traits in selections.') !!}
            {{ html()->select('subtype_ids[]', $subtypes, $feature->subtypes)->class('form-control')->id('subtype')->attribute('multiple', 'multiple')->placeholder('Pick a species first.') }}
        </div>
    </div>
    <div class="form-group">
        {{ html()->label('Description (Optional)') }}
        {{ html()->textarea('description', $feature->description)->class('form-control wysiwyg') }}
    </div>

    <div class="form-group">
        {{ html()->checkbox('is_visible', $feature->id ? $feature->is_visible : 1, 1)->class('form-check-input')->attribute('data-toggle', 'toggle') }}
        {{ html()->label('Is Visible', 'is_visible')->class('form-check-label ml-3') }} {!! add_help('If turned off, the trait will not be visible in the trait list or available for selection in search and design updates. Permissioned staff will still be able to add them to characters, however.') !!}
    </div>

    <div class="text-right">
        {{ html()->submit($feature->id ? 'Edit' : 'Create')->class('btn btn-primary') }}
    </div>

    {{ html()->form()->close() }}

    @if ($feature->id)
        <h3>Preview</h3>
        <div class="card mb-3">
            <div class="card-body">
                @include('world._feature_entry', ['feature' => $feature])
            </div>
        </div>
    @endif
@endsection

@section('scripts')
    @parent
    @include('js._tinymce_wysiwyg')
    <script>
        $(document).ready(function() {
            $('.delete-feature-button').on('click', function(e) {
                e.preventDefault();
                loadModal("{{ url('admin/data/traits/delete') }}/{{ $feature->id }}", 'Delete Trait');
            });
            refreshSubtype();
        });

        $("#species").change(function() {
            refreshSubtype();
        });

        function refreshSubtype() {
            var species = $('#species').val();
            var subtype_ids = @json($feature->subtypes->pluck('id')->toArray());
            $.ajax({
                type: "GET",
                url: "{{ url('admin/data/traits/check-subtype') }}?species=" + species + "&subtype_ids=" + subtype_ids,
                dataType: "text"
            }).done(function(res) {
                $("#subtypes").html(res);
                $("#subtype").selectize();
            }).fail(function(jqXHR, textStatus, errorThrown) {
                alert("AJAX call failed: " + textStatus + ", " + errorThrown);
            });
        };

        $('#subtype').selectize();
    </script>
@endsection
