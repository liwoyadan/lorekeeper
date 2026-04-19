@php
    $selected = $model->exists ? $model->configuredTags()->pluck('name')->all() : [];
    $options  = array_unique(array_merge($model->availableTagOptions(), $selected));
    $name     = $name ?? 'tags';
    $id       = 'tagInput-'.$model->tagType().'-'.$name;
@endphp
<div class="form-group">
    {!! Form::label($id, $label ?? 'Tags') !!}
    {!! isset($help) ? add_help($help) : null !!}
    <select name="{{ $name }}[]" id="{{ $id }}" class="form-control tag-input" multiple>
        @foreach ($options as $tag)
            <option value="{{ $tag }}" @if (in_array($tag, $selected)) selected @endif>{{ $tag }}</option>
        @endforeach
    </select>
</div>
