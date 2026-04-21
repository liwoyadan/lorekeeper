@extends('admin.layout')

@section('admin-title')
    Grant Items
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Grant Items' => 'admin/grants/items']) !!}

    <h1>Grant Items</h1>

    {{ html()->form('POST', 'admin/grants/items')->open() }}

    <h3>Basic Information</h3>

    <div class="form-group">
        {{ html()->label('Username(s)', 'names[]') }} {!! add_help('You can select up to 10 users at once.') !!}
        {{ html()->select('names[]', $users, null)->id('usernameList')->class('form-control')->attribute('multiple', 'multiple') }}
    </div>

    <div class="form-group">
        {{ html()->label('Item(s)') }} {!! add_help('Must have at least 1 item and Quantity must be at least 1.') !!}
        <div id="itemList">
            <div class="d-flex mb-2">
                {{ html()->select('item_ids[]', $items, null)->class('form-control mr-2 default item-select')->placeholder('Select Item') }}
                {{ html()->text('quantities[]', 1)->class('form-control mr-2')->placeholder('Quantity') }}
                <a href="#" class="remove-item btn btn-danger mb-2 disabled">×</a>
            </div>
        </div>
        <div><a href="#" class="btn btn-primary" id="add-item">Add Item</a></div>
        <div class="item-row hide mb-2">
            {{ html()->select('item_ids[]', $items, null)->class('form-control mr-2 item-select')->placeholder('Select Item') }}
            {{ html()->text('quantities[]', 1)->class('form-control mr-2')->placeholder('Quantity') }}
            <a href="#" class="remove-item btn btn-danger mb-2">×</a>
        </div>
    </div>

    <div class="form-group">
        {{ html()->label('Reason (Optional)', 'data') }} {!! add_help('A reason for the grant. This will be noted in the logs and in the inventory description.') !!}
        {{ html()->text('data', null)->class('form-control')->attribute('maxlength', 400) }}
    </div>

    <h3>Additional Data</h3>

    <div class="form-group">
        {{ html()->label('Notes (Optional)', 'notes') }} {!! add_help('Additional notes for the item. This will appear in the item\'s description, but not in the logs.') !!}
        {{ html()->text('notes', null)->class('form-control')->attribute('maxlength', 400) }}
    </div>

    <div class="form-group">
        {{ html()->checkbox('disallow_transfer', 0, 1)->class('form-check-input')->attribute('data-toggle', 'toggle') }}
        {{ html()->label('Account-bound', 'disallow_transfer')->class('form-check-label ml-3') }} {!! add_help('If this is on, the recipient(s) will not be able to transfer this item to other users. Items that disallow transfers by default will still not be transferrable.') !!}
    </div>

    <div class="text-right">
        {{ html()->submit('Submit')->class('btn btn-primary') }}
    </div>

    {{ html()->form()->close() }}

    <script>
        $(document).ready(function() {
            $('#usernameList').selectize({
                maxItems: 10
            });
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
