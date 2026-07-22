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

// Accessibility/alt settings stuff...
// (a11yBuildRule comes from _accessibility_rule_js)
$(document).ready(function () {
    var $panel = $('#a11yPanel');
    if (!$panel.length || window.a11yPanelInit) {
        return;
    }
    window.a11yPanelInit = true;

    var map = window.a11yClientMap || {};
    var isAuth = $panel.data('authenticated') == 1;
    var token = $('meta[name="csrf-token"]').attr('content');
    var baseUrl = window.a11yBaseUrl || '';
    var suppress = false;

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

    function styleNode() {
        var $node = $('#user-a11y-settings');
        if (!$node.length) {
            $node = $('<style id="user-a11y-settings"></style>').appendTo('head');
        }
        return $node;
    }

    function repaint() {
        var css = '';
        $.each(state, function (key, value) {
            if (map[key] && value != null && value != '') {
                css += window.a11yBuildRule(map[key], value);
            }
        });
        styleNode().text(css);
    }

    function readControl($control) {
        var $input = $control.find('.a11y-input');
        if ($control.data('a11y-type') == 'toggle') {
            return $input.is(':checked') ? '1' : '0';
        }
        return $input.val();
    }

    function setControl($control, value) {
        var type = $control.data('a11y-type');
        var $input = $control.find('.a11y-input');
        suppress = true;
        if (type == 'toggle') {
            var on = value == '1' || value == 'on';
            $input.prop('checked', on).change();
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
                $input.colorpicker('setValue', value);
            }
        }
        suppress = false;
    }

    function updateRangeLabel($control) {
        var key = $control.data('a11y-setting');
        var unit = map[key] && map[key].unit ? map[key].unit : '';
        $control.find('.a11y-range-value').text($control.find('.a11y-input').val() + unit);
    }

    function saveLocal() {
        try {
            localStorage.setItem('a11y', JSON.stringify(state));
        } catch (e) {}
    }

    function persist(key, value) {
        if (isAuth) {
            $.ajax({url: baseUrl, method: 'POST', data: {_token: token, setting_key: key, value: value}});
        } else {
            saveLocal();
        }
    }

    function commit($control) {
        var key = $control.data('a11y-setting');
        var value = readControl($control);
        state[key] = value;
        repaint();
        persist(key, value);
    }

    // for guests/logged out
    if (!isAuth) {
        $.each(state, function (k, value) {
            var $c = $panel.find('.a11y-control[data-a11y-setting="' + k + '"]');
            if ($c.length) {
                setControl($c, value);
            }
        });
    }
    $panel.find('.a11y-control[data-a11y-type="range"]').each(function () {
        updateRangeLabel($(this));
    });
    repaint();

    // live-preview while dragging a range without spamming
    $panel.on('input', '.a11y-input', function () {
        if (suppress) {
            return;
        }
        var $control = $(this).closest('.a11y-control');
        if ($control.data('a11y-type') == 'range') {
            updateRangeLabel($control);
            state[$control.data('a11y-setting')] = readControl($control);
            repaint();
        }
    });

    $panel.on('change', '.a11y-input', function () {
        if (suppress) {
            return;
        }
        commit($(this).closest('.a11y-control'));
    });

    $panel.on('click', '.a11y-swatch', function (e) {
        e.preventDefault();
        var $control = $(this).closest('.a11y-control');
        $control.find('.a11y-swatch').removeClass('active');
        $(this).addClass('active');
        $control.find('.a11y-input').val($(this).data('value'));
        commit($control);
    });

    $panel.on('click', '.a11y-reset-one', function (e) {
        e.preventDefault();
        var $control = $(this).closest('.a11y-control');
        var key = $control.data('a11y-setting');
        var id = $control.data('a11y-id');
        delete state[key];
        setControl($control, '');
        repaint();
        if (isAuth) {
            $.ajax({url: baseUrl + '/reset/' + id, method: 'POST', data: {_token: token}});
        } else {
            saveLocal();
        }
    });

    $('#a11yResetAll').on('click', function (e) {
        e.preventDefault();
        state = {};
        $panel.find('.a11y-control').each(function () {
            setControl($(this), '');
        });
        repaint();
        if (isAuth) {
            $.ajax({url: baseUrl + '/reset', method: 'POST', data: {_token: token}});
        } else {
            try {
                localStorage.removeItem('a11y');
            } catch (e) {}
        }
    });
});