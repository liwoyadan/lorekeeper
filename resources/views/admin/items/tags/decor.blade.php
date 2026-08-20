<h3>Housing Decor</h3>
<p>Choose which housing decor piece a user receives when they redeem this item. The user customizes its recolor zones at redemption.</p>

<div class="form-group">
    {!! Form::label('decor_id', 'Decor') !!}
    {!! Form::select('decor_id', $decors, $tag->getData()['decor_id'] ?? null, ['class' => 'form-control', 'placeholder' => 'Select Decor']) !!}
</div>
