{!! Form::label('Subject (Optional)') !!} {!! add_help('Optionally pin this changelog entry to a specific record of the selected type. Leave blank to apply broadly to the type.') !!}
{!! Form::select('type_id', $options, $selected, ['class' => 'form-control selectize', 'id' => 'subjectSelect']) !!}
