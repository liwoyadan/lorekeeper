{{ html()->form('POST', $isMyo ? 'admin/myo/' . $character->id . '/description' : 'admin/character/' . $character->slug . '/description')->open() }}
<div class="form-group">
    {{ html()->label('Character Description') }}
    {{ html()->textarea('description', $character->description)->class('form-control wysiwyg') }}
</div>

<div class="text-right">
    {{ html()->submit('Edit')->class('btn btn-primary') }}
</div>
{{ html()->form()->close() }}

@include('js._tinymce_wysiwyg', ['tinymceSelector' => '.descriptioneditingparse .wysiwyg'])
