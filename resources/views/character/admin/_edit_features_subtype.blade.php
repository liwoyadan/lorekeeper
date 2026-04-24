{{ html()->label('Subtypes (Optional)') }}
{{ html()->select('subtype_ids[]', $subtypes, $image->subtypes()?->pluck('subtype_id')->toArray())->class('form-control')->id('subtype')->attribute('multiple', true)->placeholder('Select Subtype(s)') }}
