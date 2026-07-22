{{-- Guests/users who aren't logged in store their accessibility preferences in localStorage. 
    This runs at the end of <head> (before content paint, but after the theme blocks)
    so override rules win the CSS cascade with no flash of unstyled content. 
    (Unless if something has...specificity priority *and* !important...?)
    Plain JS because jQuery doesn't load in this early... --}}
@include('layouts._accessibility_rule_js')
<script>
    (function () {
        try {
            var map = {{ Js::from($a11yClientMap ?? []) }};
            var raw = localStorage.getItem('a11y');
            if (!raw) {
                return;
            }
            var values = JSON.parse(raw);
            if (!values) {
                return;
            }

            var css = '';
            for (var key in values) {
                if (map[key]) {
                    css += window.a11yBuildRule(map[key], values[key]);
                }
            }
            if (css == '') {
                return;
            }

            var style = document.createElement('style');
            style.id = 'user-a11y-settings';
            style.textContent = css;
            document.head.appendChild(style);
        } catch (e) {}
    })();
</script>
