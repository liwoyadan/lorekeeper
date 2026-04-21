@extends('admin.layout')

@section('admin-title')
    Grant Loot Tables
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Grant Loot Tables' => 'admin/grants/loot-tables']) !!}

    <h1>Grant Loot Tables</h1>

    {{ html()->form('POST', 'admin/grants/loot-tables')->open() }}
    <h3>Basic Information</h3>

    <div class="form-group">
        {{ html()->label('Username(s)', 'names[]') }} {!! add_help('You can select up to 10 users at once.') !!}
        {{ html()->select('names[]', $users, null)->id('usernameList')->class('form-control')->attribute('multiple', 'multiple') }}
    </div>

    <div class="form-group">
        {{ html()->label('Loot Table(s)') }} {!! add_help('Must have at least 1 loot table and Quantity must be at least 1.') !!}
        <div id="loot_tableList">
            <div class="d-flex mb-2">
                {{ html()->select('loot_table_ids[]', $loot_tables, null)->class('form-control mr-2 default loot_table-select')->placeholder('Select Loot Table') }}
                {{ html()->text('quantities[]', 1)->class('form-control mr-2')->placeholder('Quantity') }}
                <a href="#" class="remove-loot_table btn btn-danger mb-2 disabled">×</a>
            </div>
        </div>
        <div><a href="#" class="btn btn-primary" id="add-loot_table">Add Loot Table</a></div>
        <div class="loot_table-row hide mb-2">
            {{ html()->select('loot_table_ids[]', $loot_tables, null)->class('form-control mr-2 loot_table-select')->placeholder('Select Loot Table') }}
            {{ html()->text('quantities[]', 1)->class('form-control mr-2')->placeholder('Quantity') }}
            <a href="#" class="remove-loot table btn btn-danger mb-2">×</a>
        </div>
    </div>

    <div class="form-group">
        {{ html()->label('Reason (Optional)', 'data') }} {!! add_help('A reason for the grant. This will be noted in the logs and in the inventory description.') !!}
        {{ html()->text('data', null)->class('form-control')->attribute('maxlength', 400) }}
    </div>

    <h3>Additional Data</h3>

    <div class="form-group">
        {{ html()->label('Notes (Optional)', 'notes') }} {!! add_help('Additional notes for the loot table. This will appear in the loot table\'s description, but not in the logs.') !!}
        {{ html()->text('notes', null)->class('form-control')->attribute('maxlength', 400) }}
    </div>

    <div class="form-group">
        {{ html()->checkbox('disallow_transfer', 0, 1)->class('form-check-input')->attribute('data-toggle', 'toggle') }}
        {{ html()->label('Account-bound', 'disallow_transfer')->class('form-check-label ml-3') }} {!! add_help('If this is on, the recipient(s) will not be able to transfer this loot table to other users. Loot Tables that disallow transfers by default will still not be transferrable.') !!}
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
            $('.default.loot_table-select').selectize();
            $('#add-loot_table').on('click', function(e) {
                e.preventDefault();
                addLootTableRow();
            });
            $('.remove-loot_table').on('click', function(e) {
                e.preventDefault();
                removeLootTableRow($(this));
            })

            function addLootTableRow() {
                var $rows = $("#loot_tableList > div")
                if ($rows.length === 1) {
                    $rows.find('.remove-loot_table').removeClass('disabled')
                }
                var $clone = $('.loot_table-row').clone();
                $('#loot_tableList').append($clone);
                $clone.removeClass('hide loot_table-row');
                $clone.addClass('d-flex');
                $clone.find('.remove-loot_table').on('click', function(e) {
                    e.preventDefault();
                    removeLootTableRow($(this));
                })
                $clone.find('.loot_table-select').selectize();
            }

            function removeLootTableRow($trigger) {
                $trigger.parent().remove();
                var $rows = $("#loot_tableList > div")
                if ($rows.length === 1) {
                    $rows.find('.remove-loot_table').addClass('disabled')
                }
            }
        });
    </script>
@endsection
