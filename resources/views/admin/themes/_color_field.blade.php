<div class="input-group cp">
    {!! Form::text($name, $value, ['class' => 'form-control', 'placeholder' => $placeholder]) !!}
    <span class="input-group-append">
        <span class="input-group-text colorpicker-input-addon" style="background-color: {{ $value ?? $placeholder }};"><i></i></span>
    </span>
</div>
