@if ($category)
    {{ html()->form('POST', 'admin/data/currency-categories/delete/' . $category->id)->open() }}

    <p>You are about to delete the category <strong>{{ $category->name }}</strong>. This is not reversible. If currencies in this category exist, you will not be able to delete this category.</p>
    <p>Are you sure you want to delete <strong>{{ $category->name }}</strong>?</p>

    <div class="text-right">
        {{ html()->submit('Delete Category')->class('btn btn-danger') }}
    </div>

    {{ html()->form()->close() }}
@else
    Invalid category selected.
@endif
