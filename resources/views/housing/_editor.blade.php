<div class="housing-editor mt-3" data-reference-width="{{ config('lorekeeper.housing.reference_width') }}" data-min-scale="{{ config('lorekeeper.housing.min_scale') }}" data-max-scale="{{ config('lorekeeper.housing.max_scale') }}"
    data-max-placements="{{ Settings::get('housing_max_placements') }}">
    <hr>
    <h3>Decorate</h3>
    <p class="text-muted small">Click a decor to add it, then drag to move and drag the corner to resize. Select a placed decor to flip, layer, or remove it.</p>

    <div class="housing-toolbar mb-2">
        <div class="btn-group">
            <button type="button" class="btn btn-sm btn-outline-secondary housing-flip" disabled><i class="fas fa-arrows-alt-h"></i> Flip</button>
            <button type="button" class="btn btn-sm btn-outline-secondary housing-forward" disabled><i class="fas fa-arrow-up"></i> Forward</button>
            <button type="button" class="btn btn-sm btn-outline-secondary housing-backward" disabled><i class="fas fa-arrow-down"></i> Back</button>
            <button type="button" class="btn btn-sm btn-outline-danger housing-delete" disabled><i class="fas fa-trash"></i> Remove</button>
        </div>
        <span class="ml-2 text-muted small"><span class="housing-count">0</span> / {{ Settings::get('housing_max_placements') }} placed</span>
    </div>

    <div class="housing-slot-bar mb-2">
        <button type="button" class="btn btn-sm btn-outline-secondary" data-toggle="collapse" data-target="#housingWallPicker"><i class="fas fa-border-all"></i> Change Wall</button>
        <button type="button" class="btn btn-sm btn-outline-secondary" data-toggle="collapse" data-target="#housingFloorPicker"><i class="fas fa-th-large"></i> Change Floor</button>
    </div>

    @foreach (['wall' => $wallOptions, 'floor' => $floorOptions] as $slot => $options)
        <div id="housing{{ ucfirst($slot) }}Picker" class="collapse mb-2">
            <div class="d-flex flex-wrap border rounded p-2" style="gap: 0.5rem;">
                <div class="housing-slot-option text-center" role="button" data-slot="{{ $slot }}" data-owned-decor-id="" style="width: 92px;">
                    <div class="border rounded p-1 text-muted small" style="height: 72px; display: flex; align-items: center; justify-content: center;">None</div>
                </div>
                @forelse ($options as $owned)
                    <div class="housing-slot-option text-center" role="button" data-slot="{{ $slot }}" data-owned-decor-id="{{ $owned->id }}" style="width: 92px;">
                        <div class="border rounded p-1" style="height: 72px; display: flex; align-items: center; justify-content: center;">
                            @if ($owned->decor->has_image)
                                <img src="{{ $owned->decor->decorImageUrl }}" alt="{{ $owned->decor->name }}" style="max-width: 100%; max-height: 64px;">
                            @else
                                <span class="text-muted small">No image</span>
                            @endif
                        </div>
                        <div class="small text-truncate">{{ $owned->decor->name }}</div>
                    </div>
                @empty
                    <p class="text-muted small mb-0">You own no {{ $slot }} decor yet.</p>
                @endforelse
            </div>
        </div>
    @endforeach

    <div id="housing-backdrop-prototypes" style="display: none;">
        @foreach ($wallOptions->concat($floorOptions) as $owned)
            <div class="housing-backdrop-proto" data-owned-decor-id="{{ $owned->id }}">
                @include('housing._backdrop', ['slot' => $owned->decor->kind, 'ownedDecor' => $owned])
            </div>
        @endforeach
    </div>

    <div class="housing-palette d-flex flex-wrap border rounded p-2 mb-2" style="gap: 0.5rem; max-height: 220px; overflow-y: auto;">
        @forelse ($palette as $owned)
            <div class="housing-palette-item text-center" role="button" data-owned-decor-id="{{ $owned->id }}" data-layer="{{ $owned->decor->layer }}" data-default-scale="{{ $owned->decor->default_scale ?: 1 }}"
                data-owned-count="{{ $owned->count }}" style="width: 92px;">
                <div class="border rounded p-1" style="height: 72px; display: flex; align-items: center; justify-content: center;">
                    @if ($owned->decor->has_image)
                        <img src="{{ $owned->decor->decorImageUrl }}" alt="{{ $owned->decor->name }}" style="max-width: 100%; max-height: 64px;">
                    @else
                        <span class="text-muted small">No image</span>
                    @endif
                </div>
                <div class="small text-truncate">{{ $owned->decor->name }}</div>
                <div class="small text-muted"><span class="housing-available">{{ $owned->count }}</span> left</div>
            </div>
        @empty
            <p class="text-muted small mb-0">You have no decor to place yet. Redeem decor items from your inventory first.</p>
        @endforelse
    </div>

    <div id="housing-prototypes" style="display: none;">
        @foreach ($palette as $owned)
            <div class="housing-proto" data-owned-decor-id="{{ $owned->id }}">
                @include('housing._piece', ['p' => ['x' => 0, 'y' => 0, 'scale' => 20, 'z' => 0], 'ownedDecor' => $owned])
            </div>
        @endforeach
    </div>

    {!! Form::open(['url' => 'housing/' . $home->id . '/layout', 'id' => 'housingSaveForm']) !!}
    {!! Form::hidden('layout', '', ['id' => 'housingLayoutField']) !!}
    {!! Form::submit('Save Home', ['class' => 'btn btn-primary']) !!}
    {!! Form::close() !!}
</div>

<script>
    $(document).ready(function() {
        var $editor = $('.housing-editor');
        var $stage = $('.housing-room');
        var referenceWidth = parseFloat($editor.data('reference-width'));
        var minScale = parseFloat($editor.data('min-scale'));
        var maxScale = parseFloat($editor.data('max-scale'));
        var maxPlacements = parseInt($editor.data('max-placements'), 10);
        var $selected = null;

        var naturals = {};
        $('#housing-prototypes .housing-proto').each(function() {
            var protoId = $(this).data('owned-decor-id');
            var img = $(this).find('img')[0];
            if (!img) {
                return;
            }
            if (img.complete && img.naturalWidth) {
                naturals[protoId] = img.naturalWidth;
            } else {
                $(img).on('load', function() {
                    naturals[protoId] = img.naturalWidth;
                });
            }
        });

        function clamp(value, min, max) {
            return Math.min(max, Math.max(min, value));
        }

        function totalPlaced() {
            return $stage.find('.housing-piece').length;
        }

        function currentSlotId(slot) {
            var el = $stage.find('.housing-backdrop-' + slot);
            return el.length ? (parseInt(el.attr('data-owned-decor-id'), 10) || null) : null;
        }

        function refreshCount() {
            $editor.find('.housing-count').text(totalPlaced());
            var empty = totalPlaced() === 0 && !currentSlotId('wall') && !currentSlotId('floor');
            var $caption = $stage.nextAll('p.text-muted').first();
            if (!$caption.length) {
                $caption = $('<p class="text-center text-muted mt-2">This room is empty.</p>').insertAfter($stage);
            }
            $caption.toggle(empty);
        }

        function refreshAvailability() {
            $editor.find('.housing-palette-item').each(function() {
                var $item = $(this);
                var id = $item.data('owned-decor-id');
                var owned = parseInt($item.data('owned-count'), 10);
                var placed = $stage.find('.housing-piece[data-owned-decor-id="' + id + '"]').length;
                var left = owned - placed;
                $item.find('.housing-available').text(left);
                $item.toggleClass('disabled', left <= 0).css('opacity', left <= 0 ? 0.4 : 1);
            });
        }

        function selectPiece($piece) {
            if ($selected) {
                $selected.css('outline', '');
            }
            $selected = $piece;
            if ($selected) {
                $selected.css('outline', '2px dashed #007bff');
            }
            $editor.find('.housing-toolbar button').prop('disabled', !$selected);
        }

        function makeInteractive($piece) {
            $piece.css('cursor', 'move');
            $piece.draggable({
                containment: $stage,
                stop: function() {
                    selectPiece($piece);
                }
            });
            $piece.resizable({
                handles: 'se',
                aspectRatio: true,
                containment: $stage,
                stop: function(event, ui) {
                    $piece.css('height', '');
                }
            });
            $piece.on('mousedown', function(e) {
                e.stopPropagation();
                selectPiece($piece);
            });
        }

        $stage.find('.housing-piece').each(function() {
            makeInteractive($(this));
        });

        $editor.on('click', '.housing-palette-item', function() {
            var $item = $(this);
            var id = $item.data('owned-decor-id');
            var layer = $item.data('layer');
            var defaultScale = parseFloat($item.data('default-scale')) || 1;

            if (totalPlaced() >= maxPlacements) {
                alert('This room is full (' + maxPlacements + ' items).');
                return;
            }
            var owned = parseInt($item.data('owned-count'), 10);
            var placed = $stage.find('.housing-piece[data-owned-decor-id="' + id + '"]').length;
            if (owned - placed <= 0) {
                return;
            }

            var $piece = $('#housing-prototypes .housing-proto[data-owned-decor-id="' + id + '"] .housing-piece').clone();
            var natural = naturals[id] || 0;
            var widthPct = natural ? (natural / referenceWidth * 100) * defaultScale : (20 * defaultScale);
            widthPct = clamp(widthPct, minScale, maxScale);

            $piece.css({
                left: '40%',
                top: '40%',
                width: widthPct + '%',
                height: '',
                'z-index': 0,
                transform: 'none'
            });
            $piece.attr('data-z', 0).attr('data-flip', 0);

            $stage.find('.housing-layer-' + layer).append($piece);
            makeInteractive($piece);
            selectPiece($piece);
            refreshAvailability();
            refreshCount();
        });

        $stage.on('mousedown', function() {
            selectPiece(null);
        });

        $editor.on('click', '.housing-slot-option', function() {
            var slot = $(this).data('slot');
            var id = $(this).attr('data-owned-decor-id');
            $stage.find('.housing-backdrop-' + slot).remove();
            if (id) {
                var $bd = $('#housing-backdrop-prototypes .housing-backdrop-proto[data-owned-decor-id="' + id + '"] .housing-backdrop').clone();
                $stage.prepend($bd);
            }
            refreshCount();
        });

        $editor.on('click', '.housing-flip', function() {
            if (!$selected) {
                return;
            }
            var flipped = $selected.attr('data-flip') == '1' ? 0 : 1;
            $selected.attr('data-flip', flipped);
            $selected.css('transform', flipped ? 'scaleX(-1)' : 'none');
        });

        $editor.on('click', '.housing-forward', function() {
            if (!$selected) {
                return;
            }
            var z = parseInt($selected.attr('data-z'), 10) + 1;
            $selected.attr('data-z', z).css('z-index', z);
        });

        $editor.on('click', '.housing-backward', function() {
            if (!$selected) {
                return;
            }
            var z = Math.max(0, parseInt($selected.attr('data-z'), 10) - 1);
            $selected.attr('data-z', z).css('z-index', z);
        });

        $editor.on('click', '.housing-delete', function() {
            if (!$selected) {
                return;
            }
            $selected.remove();
            selectPiece(null);
            refreshAvailability();
            refreshCount();
        });

        $('#housingSaveForm').on('submit', function() {
            var sw = $stage.width();
            var sh = $stage.height();
            var placements = [];
            $stage.find('.housing-piece').each(function() {
                var $piece = $(this);
                var pos = $piece.position();
                placements.push({
                    owned_decor_id: parseInt($piece.attr('data-owned-decor-id'), 10),
                    x: clamp(pos.left / sw * 100, 0, 100),
                    y: clamp(pos.top / sh * 100, 0, 100),
                    scale: clamp($piece.outerWidth() / sw * 100, minScale, maxScale),
                    z: parseInt($piece.attr('data-z'), 10) || 0,
                    flip_x: $piece.attr('data-flip') == '1'
                });
            });
            $('#housingLayoutField').val(JSON.stringify({
                placements: placements,
                wall: currentSlotId('wall'),
                floor: currentSlotId('floor')
            }));
        });

        refreshAvailability();
        refreshCount();
    });
</script>
