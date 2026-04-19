<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TagService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TagController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Admin / Tag Controller
    |--------------------------------------------------------------------------
    |
    | Handles tag sync actions for any model using the ConfiguredTags trait.
    | Model registry and per-slug power requirements live in
    | config/lorekeeper/tags.php.
    |
    */

    public function postSync(Request $request, TagService $service, $slug, $id) {
        $slug = strtolower($slug);
        $requiredPower = config('lorekeeper.tags.powers.'.$slug, 'edit_data');
        if (!Auth::user() || !Auth::user()->hasPower($requiredPower)) {
            abort(403);
        }

        $model = $service->resolveModel($slug, $id);
        if (!$model) {
            abort(404);
        }

        if ($service->syncTags($model, $request->input('tags', []))) {
            flash('Tags updated successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }
}
