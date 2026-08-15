@if ($model->exists)
    <h3>Tags</h3>
    <p>Add tags to help organize and filter this entry. Type to search existing tags or press Enter to create a new one.</p>
    {!! Form::open(['url' => 'admin/tags/' . $model->tagSlug() . '/' . $model->id]) !!}
    @include('widgets._tag_input', ['model' => $model])
    <div class="text-right">
        {!! Form::submit('Save Tags', ['class' => 'btn btn-primary']) !!}
    </div>
    {!! Form::close() !!}
@endif
