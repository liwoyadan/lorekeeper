<?php

namespace App\Models\Housing;

use App\Models\Model;
use App\Models\User\User;

class OwnedDecor extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'decor_id', 'customization', 'count',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'owned_decors';

    /**
     * Whether the model contains timestamps to be saved and updated.
     *
     * @var bool
     */
    public $timestamps = true;

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the user who owns this decor stack.
     */
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the decor catalog entry this stack is an instance of.
     */
    public function decor() {
        return $this->belongsTo(HousingDecor::class, 'decor_id');
    }

    /**
     * Scope: the user's owned stacks of a given decor kind that still have count.
     */
    public function scopeOwnedFor($query, $userId, $kind) {
        return $query->where('user_id', $userId)->where('count', '>', 0)->with('decor.zones.patterns')->whereHas('decor', function ($q) use ($kind) {
            $q->where('kind', $kind);
        });
    }

    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/

    /**
     * Gets the locked customization as a decoded array of zoneId => [type, value].
     */
    public function getCustomizationDataAttribute() {
        return $this->customization ? json_decode($this->customization, true) : [];
    }

    /**********************************************************************************************

        OTHER FUNCTIONS

    **********************************************************************************************/

    /**
     * Builds the CSS background declaration for a zone's locked fill (color or
     * pattern), or an empty string when the zone has no applicable fill.
     */
    public function zoneFill($zone) {
        $cust = $this->customizationData;
        if (!isset($cust[$zone->id]) || !$zone->has_mask) {
            return '';
        }

        $fill = $cust[$zone->id];
        if ($fill['type'] == 'pattern') {
            $pattern = $zone->patterns->find($fill['value']);

            return $pattern && $pattern->patternImageUrl ? 'background-image: url('.$pattern->patternImageUrl.'); background-repeat: repeat;' : '';
        }

        return 'background-color: #'.$fill['value'].';';
    }

    /**
     * Resolves per-zone SVG fills for svg render mode: each entry has the zone's
     * selector, the CSS fill value, and (for pattern fills) the injected def id
     * and pattern image URL.
     */
    public function svgFills() {
        $cust = $this->customizationData;
        $out = [];
        foreach ($this->decor->zones as $zone) {
            if (!$zone->svg_selector || !isset($cust[$zone->id])) {
                continue;
            }

            $fill = $cust[$zone->id];
            $selector = $this->namespaceSelectorIds($zone->svg_selector);
            if ($fill['type'] == 'pattern') {
                $pattern = $zone->patterns->find($fill['value']);
                if (!$pattern || !$pattern->patternImageUrl) {
                    continue;
                }
                $defId = 'pat-'.$this->id.'-'.$zone->id;
                $out[] = ['selector' => $selector, 'fill' => 'url(#'.$defId.')', 'patternDefId' => $defId, 'patternUrl' => $pattern->patternImageUrl];
            } else {
                $out[] = ['selector' => $selector, 'fill' => '#'.$fill['value'], 'patternDefId' => null, 'patternUrl' => null];
            }
        }

        return $out;
    }

    /**
     * The subset of svgFills() backed by an injected SVG pattern def.
     */
    public function svgPatternFills($fills = null) {
        $fills = $fills ?? $this->svgFills();

        return array_filter($fills, function ($f) {
            return $f['patternDefId'];
        });
    }

    /**
     * The decor's SVG markup with its internal ids (and every reference to them)
     * prefixed for this owned decor, so distinct decors sharing an id name can't
     * collide when several are inlined on one page.
     */
    public function svgMarkup() {
        $svg = $this->decor->svgContents;
        if (!$svg) {
            return '';
        }

        $ns = $this->svgIdPrefix();
        $svg = preg_replace('/\bid="([^"]+)"/', 'id="'.$ns.'$1"', $svg);
        $svg = preg_replace('/url\(#([^)]+)\)/', 'url(#'.$ns.'$1)', $svg);
        $svg = preg_replace('/\b(xlink:href|href)="#([^"]+)"/', '$1="#'.$ns.'$2"', $svg);

        return $svg;
    }

    /**
     * Rewrites id references (#name) in a zone selector to match the namespaced
     * markup, leaving class and element selectors untouched.
     */
    private function namespaceSelectorIds($selector) {
        return preg_replace('/#([A-Za-z][\w-]*)/', '#'.$this->svgIdPrefix().'$1', $selector);
    }

    /**
     * The id-namespace prefix that scopes this owned decor's inlined SVG.
     */
    private function svgIdPrefix() {
        return 'd'.$this->id.'-';
    }
}
