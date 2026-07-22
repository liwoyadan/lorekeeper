<style>
    .a11y-swatch.active {
        box-shadow: 0 0 0 2px #007bff;
    }
</style>

@if (!count($a11yPanelData))
    <p class="text-muted">
        No accessibility/alt options are available right now.
    </p>
@else
    <div id="a11yPanel" data-authenticated="{{ $a11yIsAuth ? '1' : '0' }}">
        @foreach ($a11yPanelData as $panel)
            <h4 class="mt-4">{{ $panel['label'] }}</h4>
            <hr class="mt-1">
            @foreach ($panel['settings'] as $row)
                @include('account._accessibility_input', [
                    'setting' => $row['setting'],
                    'value' => $row['value'],
                    'default' => $row['default'],
                    'options' => $row['options'],
                ])
            @endforeach
        @endforeach

        <hr>
        <div class="text-right">
            <a href="#" class="btn btn-outline-secondary btn-sm" id="a11yResetAll">Reset all to defaults</a>
        </div>
    </div>

    @include('layouts._accessibility_rule_js')

    <script>
        window.a11yClientMap = {{ Js::from($a11yClientMap) }};
        window.a11yUserValues = {{ Js::from($a11ySaved) }};
        window.a11yBaseUrl = '{{ url('accessibility') }}';
    </script>
@endif
