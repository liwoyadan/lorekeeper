function loadModal(url, title) {
    $('#modal').find('.modal-body').html('');
    $('#modal').find('.modal-title').html(title);
    $('#modal').find('.modal-body').load(url, function( response, status, xhr ) {
        if ( status == "error" ) {
            var msg = "Error: ";
            $( "#modal" ).find('.modal-body').html( msg + xhr.status + " " + xhr.statusText );
        }
        else {
            $('#modal [data-toggle=tooltip]').tooltip({html: true});
            $('#modal [data-toggle=toggle]').bootstrapToggle();
            $('#modal .cp').colorpicker({
                'autoInputFallback': false,
                'autoHexInputFallback': false,
                'format': 'auto',
                'useAlpha': true,
                extensions: [{
                    name: 'blurValid'
                }]
            });
        }
    });
    $('#modal').modal('show');
}

// Accessibility settings stuff
// a11yBuildRule (in _accessibility_rule_js) is only used by the guest head script.
// The modal live previews on the little box with .css()

// Open the settings modal from the icon
// identified via id 'a11yNavToggle', if you
// wanted to move/change it.
$(document).ready(function () {
    $('#a11yNavToggle').on('click', function (e) {
        e.preventDefault();
        loadModal($(this).data('url'), 'Accessibility Settings');
    });
});

// where each setting shows up in the live preview box 
// within the modal ('' = targets the whole preview box)
var a11yPreviewBits = {
    body_font_size: '.main-content',
    body_font_family: '',
    body_text_color: '',
    line_height: '',
    letter_spacing: '',
    contrast: '',
    reduce_motion: '',
    heading_font_family: 'h2',
    link_color: 'a',
    main_content_color: '.main-content'
};

function initA11yPanel() {
    var $panel = $('#a11yPanel');
    if (!$panel.length) {
        return;
    }

    var map = window.a11yClientMap || {};
    var isAuth = $panel.data('authenticated') == 1;
    var token = $('meta[name="csrf-token"]').attr('content');
    var baseUrl = window.a11yBaseUrl || '';
    var busy = false; // safeguard against the js not updating

    var state = {};
    if (isAuth) {
        state = window.a11yUserValues || {};
    } else {
        try {
            state = JSON.parse(localStorage.getItem('a11y') || '{}') || {};
        } catch (e) {
            state = {};
        }
    }

    // write a property onto the preview bit(s) as !important, so it always wins over the
    // site-wide a11y <style> that also lands on the modal (jQuery .css can't set priority)
    function paintProp($bit, prop, value) {
        $bit.each(function () {
            this.style.setProperty(prop, value, 'important');
        });
    }

    // the theme's own value for each preview bit, read once with the saved a11y block switched
    // off - this is what 'reset' falls back to instead of the still-applied saved value
    function captureBaseline() {
        var styleEl = document.getElementById('user-a11y-settings');
        var wasDisabled = styleEl ? styleEl.disabled : false;
        if (styleEl) {
            styleEl.disabled = true;
        }
        var base = {};
        var $preview = $('#a11yPreview');
        $.each(a11yPreviewBits, function (key, bit) {
            var t = map[key];
            if (!t || !t.property) {
                return;
            }
            var node = (bit == '' ? $preview : $preview.find(bit)).get(0);
            if (!node) {
                return;
            }
            var cs = window.getComputedStyle(node);
            base[key] = cs.getPropertyValue(t.property);
            if (t.extra_property) {
                base[key + '|' + t.extra_property] = cs.getPropertyValue(t.extra_property);
            }
        });
        if (styleEl) {
            styleEl.disabled = wasDisabled;
        }
        return base;
    }

    var baseline = captureBaseline();

    // draw the preview box - put every bit back to the theme baseline, then lay the set ones on top
    function repaint() {
        var $preview = $('#a11yPreview');
        $.each(a11yPreviewBits, function (key, bit) {
            var t = map[key];
            if (!t || !t.property) {
                return;
            }
            var $bit = bit == '' ? $preview : $preview.find(bit);
            if (baseline[key] != undefined) {
                paintProp($bit, t.property, baseline[key]);
            }
            if (t.extra_property && baseline[key + '|' + t.extra_property] != undefined) {
                paintProp($bit, t.extra_property, baseline[key + '|' + t.extra_property]);
            }
        });

        // headings/heading classes scale per-number (1-6), aren't in a11yPreviewBits, and their rule
        // is #main scoped so it never reaches the modal - a plain reset is enough here
        $preview.find('h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6').css('font-size', '');
        $.each(state, function (key, value) {
            var t = map[key];
            if (!t || value == null || value == '') {
                return;
            }

            if (t.levels) {
                var factor = parseFloat(value);
                if (isNaN(factor)) {
                    return;
                }
                $.each(t.levels, function (lvl, info) {
                    if (info.base == null) {
                        return;
                    }
                    $preview.find(lvl + ', .' + lvl).css('font-size', (info.base * factor) + (t.unit || 'rem'));
                });
                return;
            }

            var bit = a11yPreviewBits[key];
            if (bit == undefined) {
                return;
            }
            var $bit = bit == '' ? $preview : $preview.find(bit);
            if (t.input_type == 'toggle') {
                var tok = value == '1' ? t.on_value : t.off_value;
                if (tok) {
                    paintProp($bit, t.property, tok);
                    if (t.extra_property) {
                        paintProp($bit, t.extra_property, tok);
                    }
                }
            } else if (t.unit && !isNaN(parseFloat(value))) {
                paintProp($bit, t.property, value + t.unit);
            } else {
                paintProp($bit, t.property, value);
            }
        });
    }

    function readControl($control) {
        var $input = $control.find('.a11y-input');
        if ($control.data('a11y-type') == 'toggle') {
            return $input.is(':checked') ? '1' : '0';
        }
        return $input.val();
    }

    function updateRangeLabel($control) {
        var key = $control.data('a11y-setting');
        var unit = map[key] && map[key].unit ? map[key].unit : '';
        $control.find('.a11y-range-value').text($control.find('.a11y-input').val() + unit);
    }

    function setControl($control, value) {
        var type = $control.data('a11y-type');
        var $input = $control.find('.a11y-input');
        busy = true;

        try {
            if (type == 'toggle') {
                $input.prop('checked', value == '1').change();
            } else if (type == 'color' && $control.find('.a11y-swatch').length) {
                $input.val(value);
                $control.find('.a11y-swatch').removeClass('active');
                $control.find('.a11y-swatch[data-value="' + value + '"]').addClass('active');
            } else {
                $input.val(value);
                if (type == 'range') {
                    updateRangeLabel($control);
                }
                if ($input.hasClass('cp') && $input.data('colorpicker')) {
                    // ccolourpicker doesn't like empty values.
                    try { $input.colorpicker('setValue', value); } catch (err) {}
                }
            }
        } finally {
            busy = false;
        }
    }

    function hideSaved() {
        $panel.find('.a11y-saved-msg').addClass('d-none');
    }

    // set the controls to whatever's saved, then draw the preview
    $.each(state, function (key, value) {
        var $c = $panel.find('.a11y-control[data-a11y-setting="' + key + '"]');
        if ($c.length) {
            setControl($c, value);
        }
    });

    $panel.find('.a11y-control[data-a11y-type="range"]').each(function () {
        updateRangeLabel($(this));
    });
    repaint();

    $panel.on('input', '.a11y-input', function () {
        if (busy) {
            return;
        }

        var $control = $(this).closest('.a11y-control');
        if ($control.data('a11y-type') == 'range') {
            updateRangeLabel($control);
            state[$control.data('a11y-setting')] = readControl($control);
            repaint();
            hideSaved();
        }
    });

    $panel.on('change', '.a11y-input', function () {
        if (busy) {
            return;
        }
        var $control = $(this).closest('.a11y-control');
        state[$control.data('a11y-setting')] = readControl($control);
        repaint();
        hideSaved();
    });

    $panel.on('click', '.a11y-swatch', function (e) {
        e.preventDefault();
        var $control = $(this).closest('.a11y-control');
        $control.find('.a11y-swatch').removeClass('active');
        $(this).addClass('active');
        $control.find('.a11y-input').val($(this).data('value'));
        state[$control.data('a11y-setting')] = $(this).data('value');
        repaint();
        hideSaved();
    });

    $panel.on('click', '.a11y-reset-one', function (e) {
        e.preventDefault();
        var $control = $(this).closest('.a11y-control');
        setControl($control, '');
        delete state[$control.data('a11y-setting')];
        repaint();
        hideSaved();
    });

    $panel.on('click', '#a11yResetAll', function (e) {
        e.preventDefault();
        $panel.find('.a11y-control').each(function () {
            setControl($(this), '');
        });
        
        state = {};
        repaint();
        hideSaved();
    });

    // Save changes...! (And show notice to reload)
    $panel.on('click', '#a11yApply', function (e) {
        e.preventDefault();
        if (isAuth) {
            $.ajax({url: baseUrl, method: 'POST', data: {_token: token, values: state}}).done(function () {
                $panel.find('.a11y-saved-msg').removeClass('d-none');
            });
        } else {
            try {
                if ($.isEmptyObject(state)) {
                    localStorage.removeItem('a11y');
                } else {
                    localStorage.setItem('a11y', JSON.stringify(state));
                }
            } catch (err) {}
            $panel.find('.a11y-saved-msg').removeClass('d-none');
        }
    });
}