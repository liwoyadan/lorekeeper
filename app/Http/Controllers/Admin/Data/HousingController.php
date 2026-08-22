<?php

namespace App\Http\Controllers\Admin\Data;

use App\Http\Controllers\Controller;
use App\Models\Housing\HousingDecor;
use App\Models\Housing\HousingPattern;
use App\Services\HousingService;
use Auth;
use Illuminate\Http\Request;

class HousingController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Admin / Housing Controller
    |--------------------------------------------------------------------------
    |
    | Handles creation/editing of housing decor and patterns.
    |
    */

    /**
     * Shows the housing decor index.
     */
    public function getIndex() {
        return view('admin.housing.housing', [
            'decors' => HousingDecor::orderBy('sort', 'DESC')->get(),
        ]);
    }

    /**
     * Shows the create decor page.
     */
    public function getCreateHousing() {
        return view('admin.housing.create_edit_housing', [
            'decor' => new HousingDecor,
        ]);
    }

    /**
     * Shows the edit decor page.
     *
     * @param mixed $id
     */
    public function getEditHousing($id) {
        $decor = HousingDecor::with(['zones.patterns', 'zones.colors'])->find($id);
        if (!$decor) {
            abort(404);
        }

        return view('admin.housing.create_edit_housing', [
            'decor'    => $decor,
            'patterns' => HousingPattern::orderBy('name')->get(),
        ]);
    }

    /**
     * Creates or edits a housing decor.
     *
     * @param mixed|null $id
     */
    public function postCreateEditHousing(Request $request, HousingService $service, $id = null) {
        $id ? $request->validate(HousingDecor::$updateRules) : $request->validate(HousingDecor::$createRules);
        $data = $request->only([
            'name', 'kind', 'render_mode', 'layer', 'description', 'default_scale', 'is_visible', 'image', 'svg_file', 'remove_image',
            'zone_id', 'zone_name', 'zone_selector', 'zone_free_color', 'zone_colors', 'zone_patterns', 'zone_mask',
        ]);
        if ($id && $service->updateDecor(HousingDecor::find($id), $data, Auth::user())) {
            flash('Decor updated successfully.')->success();
        } elseif (!$id && $decor = $service->createDecor($data, Auth::user())) {
            flash('Decor created successfully.')->success();

            return redirect()->to('admin/data/housing/edit/'.$decor->id);
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Gets the decor deletion modal.
     *
     * @param mixed $id
     */
    public function getDeleteHousing($id) {
        $decor = HousingDecor::find($id);

        return view('admin.housing._delete_housing', [
            'decor' => $decor,
        ]);
    }

    /**
     * Deletes a housing decor.
     *
     * @param mixed $id
     */
    public function postDeleteHousing(Request $request, HousingService $service, $id) {
        if ($id && $service->deleteDecor(HousingDecor::find($id))) {
            flash('Decor deleted successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->to('admin/data/housing');
    }

    /**
     * Sorts housing decor.
     */
    public function postSortHousing(Request $request, HousingService $service) {
        if ($service->sortDecor($request->get('sort'))) {
            flash('Decor order updated successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Shows the housing pattern index.
     */
    public function getPatternIndex() {
        return view('admin.housing.patterns', [
            'patterns' => HousingPattern::orderBy('sort', 'DESC')->get(),
        ]);
    }

    /**
     * Shows the create pattern page.
     */
    public function getCreatePattern() {
        return view('admin.housing.create_edit_pattern', [
            'pattern' => new HousingPattern,
        ]);
    }

    /**
     * Shows the edit pattern page.
     *
     * @param mixed $id
     */
    public function getEditPattern($id) {
        $pattern = HousingPattern::find($id);
        if (!$pattern) {
            abort(404);
        }

        return view('admin.housing.create_edit_pattern', [
            'pattern' => $pattern,
        ]);
    }

    /**
     * Creates or edits a housing pattern.
     *
     * @param mixed|null $id
     */
    public function postCreateEditPattern(Request $request, HousingService $service, $id = null) {
        $id ? $request->validate(HousingPattern::$updateRules) : $request->validate(HousingPattern::$createRules);
        $data = $request->only([
            'name', 'is_visible', 'image', 'remove_image',
        ]);
        if ($id && $service->updatePattern(HousingPattern::find($id), $data, Auth::user())) {
            flash('Pattern updated successfully.')->success();
        } elseif (!$id && $pattern = $service->createPattern($data, Auth::user())) {
            flash('Pattern created successfully.')->success();

            return redirect()->to('admin/data/housing-patterns/edit/'.$pattern->id);
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Gets the pattern deletion modal.
     *
     * @param mixed $id
     */
    public function getDeletePattern($id) {
        $pattern = HousingPattern::find($id);

        return view('admin.housing._delete_pattern', [
            'pattern' => $pattern,
        ]);
    }

    /**
     * Deletes a housing pattern.
     *
     * @param mixed $id
     */
    public function postDeletePattern(Request $request, HousingService $service, $id) {
        if ($id && $service->deletePattern(HousingPattern::find($id))) {
            flash('Pattern deleted successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->to('admin/data/housing-patterns');
    }

    /**
     * Sorts housing patterns.
     */
    public function postSortPattern(Request $request, HousingService $service) {
        if ($service->sortPattern($request->get('sort'))) {
            flash('Pattern order updated successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }
}
