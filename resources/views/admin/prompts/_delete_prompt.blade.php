@if ($prompt)
    {{ html()->form('POST', 'admin/data/prompts/delete/' . $prompt->id)->open() }}

    <p>You are about to delete the prompt <strong>{{ $prompt->name }}</strong>. This is not reversible. If submissions exist under this prompt, you will not be able to delete it.</p>
    <p>Are you sure you want to delete <strong>{{ $prompt->name }}</strong>?</p>

    <div class="text-right">
        {{ html()->submit('Delete Prompt')->class('btn btn-danger') }}
    </div>

    {{ html()->form()->close() }}
@else
    Invalid prompt selected.
@endif
