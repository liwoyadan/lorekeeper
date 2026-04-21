{{ html()->label('Subtypes (Optional)') }} {!! add_help('This is cosmetic and does not limit choice of traits in selections.') !!}
{{ html()->select('subtype_ids[]', $subtypes, $subtype_ids)->class('form-control')->id('subtype')->attribute('multiple', 'multiple')->placeholder('Pick a species first.') }}
