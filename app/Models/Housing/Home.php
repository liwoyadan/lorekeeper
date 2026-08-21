<?php

namespace App\Models\Housing;

use App\Models\Model;

class Home extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'owner_type', 'owner_id', 'layout',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'homes';

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
     * Get the owner of this home (a user or a character).
     */
    public function owner() {
        return $this->morphTo();
    }

    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/

    /**
     * Gets the decoded room layout ({ placements: [...] }).
     *
     * @return array
     */
    public function getLayoutDataAttribute() {
        return $this->layout ? json_decode($this->layout, true) : [];
    }

    /**********************************************************************************************

        OTHER FUNCTIONS

    **********************************************************************************************/

    /**
     * Resolves the saved layout into placements grouped by decor layer, each item
     * pairing the raw placement with its owned decor.
     *
     * @return \Illuminate\Support\Collection
     */
    public function placementsByLayer() {
        $placements = $this->layoutData['placements'] ?? [];
        $ownedIds = collect($placements)->pluck('owned_decor_id')->unique()->all();
        $owned = OwnedDecor::with('decor.zones.patterns')->whereIn('id', $ownedIds)->get()->keyBy('id');

        return collect($placements)->filter(function ($p) use ($owned) {
            return isset($p['owned_decor_id']) && isset($owned[$p['owned_decor_id']]);
        })->map(function ($p) use ($owned) {
            return ['placement' => $p, 'ownedDecor' => $owned[$p['owned_decor_id']]];
        })->groupBy(function ($item) {
            return $item['ownedDecor']->decor->layer;
        });
    }

    /**
     * Resolves the wall and floor slots to their owned decor (or null).
     *
     * @return array
     */
    public function backdrops() {
        $layout = $this->layoutData;
        $ids = collect(['wall' => $layout['wall'] ?? null, 'floor' => $layout['floor'] ?? null])->filter()->values()->all();
        $owned = $ids ? OwnedDecor::with('decor.zones.patterns')->whereIn('id', $ids)->get()->keyBy('id') : collect();

        return [
            'wall'  => $owned[$layout['wall'] ?? null] ?? null,
            'floor' => $owned[$layout['floor'] ?? null] ?? null,
        ];
    }
}
