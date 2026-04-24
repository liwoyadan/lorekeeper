{{ html()->form('POST', 'admin/character/image/' . $image->id . '/notes')->open() }}
<div class="form-group">
    {{ html()->label('Image Notes') }}
    {{ html()->textarea('description', $image->description)->class('form-control wysiwyg') }}
</div>

<div class="text-right">
    {{ html()->submit('Edit')->class('btn btn-primary') }}
</div>
{{ html()->form()->close() }}

@include('js._tinymce_wysiwyg', ['tinymceSelector' => '.imagenoteseditingparse .wysiwyg'])
