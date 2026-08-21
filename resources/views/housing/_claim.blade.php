<div class="text-center my-4">
    <p>{{ $intro }}@if ($cost > 0 && $currency) Claiming costs {{ $cost }} {{ $currency->name }}.@endif</p>
    {!! Form::open(['url' => $action]) !!}
    {!! Form::submit('Claim Your Home', ['class' => 'btn btn-primary']) !!}
    {!! Form::close() !!}
</div>
