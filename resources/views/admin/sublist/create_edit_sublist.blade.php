@extends('admin.layout')

@section('admin-title')
    Sub Masterlists
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Sub Masterlists' => 'admin/data/sublists', ($sublist->id ? 'Edit' : 'Create') . ' Sub Masterlist' => $sublist->id ? 'admin/data/sublists/edit/' . $sublist->id : 'admin/data/sublists/create']) !!}

    <h1>{{ $sublist->id ? 'Edit' : 'Create' }} Sub Masterlist
        @if ($sublist->id)
            <a href="#" class="btn btn-danger float-right delete-sublist-button">Delete Sub Masterlist</a>
        @endif
    </h1>

    {{ html()->form('POST', $sublist->id ? 'admin/data/sublists/edit/' . $sublist->id : 'admin/data/sublists/create')->acceptsFiles()->open() }}

    <h3>Basic Information</h3>

    <div class="row">
        <div class="col-md-6 form-group">
            {{ html()->label('Name') }}
            {{ html()->text('name', $sublist->name)->class('form-control') }}
        </div>
        <div class="col-md-6 form-group">
            {{ html()->label('Key') }}
            {{ html()->text('key', $sublist->key)->class('form-control') }}
        </div>
    </div>

    <h3>Contents</h3>
    <p>Each category and species can only have ONE sublist. If you assign a sublist here, it will be removed from any other sublists. If you want a species shared across multiple lists, it is suggested you only use character categories. Likewise, if you
        want a category shared across multiple lists, it is suggested you only use species.</p>

    <div class="form-group">
        {{ html()->label('Categories', 'categories[]') }}
        {{ html()->select('categories[]', $categories, $subCategories)->id('categoryList')->class('form-control')->attribute('multiple', 'multiple') }}
    </div>

    <div class="form-group">
        {{ html()->label('Species', 'species[]') }}
        {{ html()->select('species[]', $species, $subSpecies)->id('speciesList')->class('form-control')->attribute('multiple', 'multiple') }}
    </div>

    <div class="form-group">
        {{ html()->checkbox('show_main', $sublist->id ? $sublist->show_main : 1, 1)->class('form-check-input')->attribute('data-toggle', 'toggle') }}
        {{ html()->label('Show on Main', 'show_main')->class('form-check-label ml-3') }} {!! add_help('Turn on to include these characters in the main masterlist as well. Turn off to entirely seperate them into the sub masterlist.') !!}
    </div>

    <div class="text-right">
        {{ html()->submit($sublist->id ? 'Edit' : 'Create')->class('btn btn-primary') }}
    </div>

    {{ html()->form()->close() }}
@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            $('.delete-sublist-button').on('click', function(e) {
                e.preventDefault();
                loadModal("{{ url('admin/data/sublists/delete') }}/{{ $sublist->id }}", 'Delete Sub Masterlist');
            });
        });

        $(document).ready(function() {
            $('#categoryList').selectize({});
            $('.default.item-select').selectize();
            $('#add-item').on('click', function(e) {
                e.preventDefault();
                addItemRow();
            });
            $('.remove-item').on('click', function(e) {
                e.preventDefault();
                removeItemRow($(this));
            })

            function addItemRow() {
                var $rows = $("#itemList > div")
                if ($rows.length === 1) {
                    $rows.find('.remove-item').removeClass('disabled')
                }
                var $clone = $('.item-row').clone();
                $('#itemList').append($clone);
                $clone.removeClass('hide item-row');
                $clone.addClass('d-flex');
                $clone.find('.remove-item').on('click', function(e) {
                    e.preventDefault();
                    removeItemRow($(this));
                })
                $clone.find('.item-select').selectize();
            }

            function removeItemRow($trigger) {
                $trigger.parent().remove();
                var $rows = $("#itemList > div")
                if ($rows.length === 1) {
                    $rows.find('.remove-item').addClass('disabled')
                }
            }
        });

        $(document).ready(function() {
            $('#speciesList').selectize({});
            $('.default.item-select').selectize();
            $('#add-item').on('click', function(e) {
                e.preventDefault();
                addItemRow();
            });
            $('.remove-item').on('click', function(e) {
                e.preventDefault();
                removeItemRow($(this));
            })

            function addItemRow() {
                var $rows = $("#itemList > div")
                if ($rows.length === 1) {
                    $rows.find('.remove-item').removeClass('disabled')
                }
                var $clone = $('.item-row').clone();
                $('#itemList').append($clone);
                $clone.removeClass('hide item-row');
                $clone.addClass('d-flex');
                $clone.find('.remove-item').on('click', function(e) {
                    e.preventDefault();
                    removeItemRow($(this));
                })
                $clone.find('.item-select').selectize();
            }

            function removeItemRow($trigger) {
                $trigger.parent().remove();
                var $rows = $("#itemList > div")
                if ($rows.length === 1) {
                    $rows.find('.remove-item').addClass('disabled')
                }
            }
        });
    </script>
@endsection
