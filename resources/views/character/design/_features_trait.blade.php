{{ html()->select('feature_id[]', $features, null)->class('form-control mr-2 feature-select')->placeholder('Select Trait') }}
{{ html()->text('feature_data[]', null)->class('form-control mr-2')->attribute('placeholder', 'Extra Info (Optional)') }}
<a href="#" class="remove-feature btn btn-danger mb-2">×</a>
