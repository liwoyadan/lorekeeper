<style>
    .a11y-swatch.active {
        box-shadow: 0 0 0 2px #007bff;
    }
</style>

@if (!count($a11yPanelData))
    <p class="text-muted text-center">
        No accessibility/alt options are available right now.
    </p>
@else
    <h2 class="mb-1">Preview</h2>

    {{-- PREVIEW BOX --}}
    <div id="a11yPanel" data-authenticated="{{ $a11yIsAuth ? '1' : '0' }}">
        <div id="a11yPreview" class="mb-3">
            <div class="main-content p-3 rounded">
                {{-- HEADINGS --}}
                <div class="row no-gutters flex-wrap align-items-end justify-content-center text-center">
                    <div class="col-auto col-md-4 p-1">
                        <h1 class="mb-0">h1 Heading</h1>
                    </div>
                    <div class="col-auto col-md-4 p-1">
                        <h2 class="mb-0">h2 Heading</h2>
                    </div>
                    <div class="col-auto col-md-4 p-1">
                        <h3 class="mb-0">h3 Heading</h3>
                    </div>
                    <div class="col-auto col-md-4 p-1">
                        <h4 class="mb-0">h4 Heading</h4>
                    </div>
                    <div class="col-auto col-md-4 p-1">
                        <h5 class="mb-0">h5 Heading</h5>
                    </div>
                    <div class="col-auto col-md-4 p-1">
                        <h6 class="mb-0">h6 Heading</h6>
                    </div>
                </div>

                {{-- TEXT & TEXT STYLES --}}
                <p class="mb-2">
                    The quick brown fox jumps over the lazy dog. <a href="#" onclick="return false;">This is a sample link.</a> <span class="text-muted">This is some muted text.</span>
                </p>
                <p class="mb-2">
                    <b>Some bolded text for preview.</b> <i>Here's some text that's italicized.</i> <u>This sentence is underlined.</u> <span class="font-weight-bold font-italic">This text is bolded AND italicized.</span> <span class="text-uppercase">This text is in all caps!</span> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed auctor, magna quis tristique bibendum, nulla ante sagittis massa, vel dignissim risus nisi non nisi. Integer non semper enim, quis ultrices nunc.
                </p>
                <div class="card">
                    <div class="card-body">
                        <b>This text is in a card element.</b> Proin tristique mauris fermentum, convallis elit ut, aliquam est. Sed arcu tortor, auctor a lorem ut, interdum tincidunt est. Vestibulum et elit id elit tempor luctus in ac ex. Vivamus quis ligula ipsum. Sed varius tincidunt fermentum. Fusce efficitur turpis nec nibh cursus gravida. Quisque scelerisque hendrerit auctor. Vestibulum dapibus erat feugiat blandit pharetra. Morbi fermentum nisl ut felis ultricies gravida.
                    </div>
                </div>
            </div>
        </div>
        <p class="text-muted text-center mb-2">
            Adjust the options below. The box above previews your changes. Nothing is saved until you press <b>Apply</b>.
        </p>

        {{-- OPTIONS --}}
        @foreach ($a11yPanelData as $panel)
            <h4 class="mt-4">
                {{ $panel['label'] }}
            </h4>
            <hr class="my-1">
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

        <div class="d-flex justify-content-between align-items-center">
            <a href="#" class="btn btn-outline-secondary btn-sm" id="a11yResetAll">Reset all</a>
            <button type="button" class="btn btn-primary btn-sm" id="a11yApply">Apply</button>
        </div>

        <div class="alert alert-success mt-2 mb-0 d-none a11y-saved-msg text-center">
            Saved. Your settings will apply as you keep browsing.
            <a href="#" class="font-weight-bold" onclick="location.reload(); return false;">Reload now</a> to see them on this page.
        </div>
    </div>

    <script>
        window.a11yClientMap = {{ Js::from($a11yClientMap) }};
        window.a11yUserValues = {{ Js::from((object) $a11ySaved) }};
        window.a11yBaseUrl = '{{ url('accessibility') }}';
        if (typeof initA11yPanel == 'function') { 
            initA11yPanel(); 
        }
    </script>
@endif
