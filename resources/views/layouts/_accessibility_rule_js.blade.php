{{-- Client-side builder that's shared. Mirrors cssFor in AccessibilityManager,
    so the guest head script and the panel live-preview have identical CSS from a single source. --}}
<script>
    window.a11yBuildRule = function (target, value) {
        if (!target || !target.selector || !target.property || value == null || value == '') {
            return '';
        }
        if (target.input_type == 'toggle') {
            var on = value == '1' || value == 'on';
            var token = on ? target.on_value : target.off_value;
            if (token == null || token == '') {
                return '';
            }
            var decls = target.property + ': ' + token + ' !important;';
            if (target.extra_property) {
                decls += ' ' + target.extra_property + ': ' + token + ' !important;';
            }
            return target.selector + ' { ' + decls + ' }';
        }
        var rendered = value;
        if (target.unit && !isNaN(parseFloat(value)) && isFinite(value)) {
            rendered = value + target.unit;
        }
        return target.selector + ' { ' + target.property + ': ' + rendered + ' !important; }';
    };
</script>
