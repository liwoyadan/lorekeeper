<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class TagService extends Service {
    /*
    |--------------------------------------------------------------------------
    | Tag Service
    |--------------------------------------------------------------------------
    |
    | Syncs tags on taggable models via the Spatie\Tags package.
    | Model resolution is driven by config/lorekeeper/tags.php.
    |
    */

    public function resolveModel($slug, $id) {
        $class = config('lorekeeper.tags.models.'.$slug);
        if (!$class) {
            return null;
        }

        return $class::find($id);
    }

    public function syncTags($model, $tags) {
        DB::beginTransaction();

        try {
            if (!$model) {
                throw new \Exception('The selected model does not exist or is not taggable.');
            }

            $tags = is_array($tags) ? array_filter(array_map('trim', $tags)) : [];
            $model->syncConfiguredTags($tags);

            return $this->commitReturn($model);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }
}
