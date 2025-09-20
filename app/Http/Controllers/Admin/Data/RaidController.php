<?php

namespace App\Http\Controllers\Admin\Data;

use App\Http\Controllers\Controller;
use App\Models\Raid\Raid;
use App\Models\Raid\RaidBoss;
use App\Services\RaidService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RaidController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Admin / Raid Controller
    |--------------------------------------------------------------------------
    |
    | Handles creation/editing of raids.
    |
    */

    /**********************************************************************************************

        RAIDS

    **********************************************************************************************/

    /**
     * Shows the raid category index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getRaidIndex(Request $request) {
        $query = Raid::orderBy('id', 'DESC');
        $data = $request->only(['name']);
        if (isset($data['name'])) {
            $query->where('name', 'LIKE', '%'.$data['name'].'%');
        }

        return view('admin.raids.raids', [
            'raids'    => $query->paginate(20)->appends($request->query()),
        ]);
    }

    /**
     * Shows the create raid page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateRaid() {
        return view('admin.raids.create_edit_raid', [
            'raid'     => new Raid,
        ]);
    }

    /**
     * Shows the edit raid page.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditRaid($id) {
        $raid = Raid::find($id);
        if (!$raid) {
            abort(404);
        }

        return view('admin.raids.create_edit_raid', [
            'raid'     => $raid,
        ]);
    }

    /**
     * Creates or edits a raid.
     *
     * @param App\Services\RaidService $service
     * @param int|null                   $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditRaid(Request $request, RaidService $service, $id = null) {
        $id ? $request->validate(Raid::$updateRules) : $request->validate(Raid::$createRules);
        $data = $request->only([
            'name', 'description', 'start_at', 'end_at', 'is_visible', 'rewardable_type', 'rewardable_id', 'quantity', 'image', 'remove_image',
        ]);
        if ($id && $service->updateRaid(Raid::find($id), $data, Auth::user())) {
            flash('Raid updated successfully.')->success();
        } elseif (!$id && $raid = $service->createRaid($data, Auth::user())) {
            flash('Raid created successfully.')->success();

            return redirect()->to('admin/data/raids/edit/'.$raid->id);
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Gets the raid deletion modal.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteRaid($id) {
        $raid = Raid::find($id);

        return view('admin.raids._delete_raid', [
            'raid' => $raid,
        ]);
    }

    /**
     * Deletes a raid.
     *
     * @param App\Services\RaidService $service
     * @param int                        $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteRaid(Request $request, RaidService $service, $id) {
        if ($id && $service->deleteRaid(Raid::find($id))) {
            flash('Raid deleted successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->to('admin/data/raids');
    }

    /**********************************************************************************************

        RAID BOSSES

    **********************************************************************************************/

    /**
     * Shows the raid category index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getRaidBossIndex(Request $request) {
        $query = RaidBoss::orderBy('id', 'DESC');
        $data = $request->only(['name', 'raid_id']);
        if (isset($data['name'])) {
            $query->where('name', 'LIKE', '%'.$data['name'].'%');
        }
        if (isset($data['raid_id']) && $data['raid_id'] != 'any') {
            $query->where('raid_id', $data['raid_id']);
        }

        return view('admin.raids.raid_bosses', [
            'bosses'    => $query->paginate(20)->appends($request->query()),
            'raids' => Raid::orderBy('id', 'DESC')->get(),
        ]);
    }

    /**
     * Shows the create raid page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateRaidBoss($id) {
        $raid = Raid::find($id);
        if (!$raid) {
            abort(404);
        }

        return view('admin.raids.create_edit_raid_boss', [
            'raidBoss'     => new RaidBoss,
            'raid' => $raid,
        ]);
    }

    /**
     * Shows the edit raid page.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditRaidBoss($id) {
        $raidBoss = RaidBoss::find($id);
        if (!$raidBoss) {
            abort(404);
        }

        return view('admin.raids.create_edit_raid_boss', [
            'raidBoss'     => $raidBoss,
        ]);
    }

    /**
     * Creates or edits a raid.
     *
     * @param App\Services\RaidService $service
     * @param int|null                   $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateRaidBoss(Request $request, RaidService $service, $id) {
        $request->validate(RaidBoss::$createRules);
        $data = $request->only([
            'name', 'raid_id', 'description', 'is_visible', 'health',
        ]);
        if ($boss = $service->createRaidBoss($data, Auth::user())) {
            flash('Raid Boss created successfully.')->success();

            return redirect()->to('admin/data/raids/bosses/edit/'.$boss->id);
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Creates or edits a raid.
     *
     * @param App\Services\RaidService $service
     * @param int|null                   $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postEditRaidBoss(Request $request, RaidService $service, $id) {
        $request->validate(RaidBoss::$updateRules);
        $data = $request->only([
            'name', 'raid_id', 'description', 'is_visible', 'health', 'threshold', 'threshold_color',
        ]);
        if ($id && $service->updateRaidBoss(RaidBoss::find($id), $data, Auth::user())) {
            flash('Raid Boss updated successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Gets the raid deletion modal.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteRaidBoss($id) {
        $raidBoss = RaidBoss::find($id);

        return view('admin.raids._delete_raid_boss', [
            'raidBoss' => $raidBoss,
        ]);
    }

    /**
     * Deletes a raid.
     *
     * @param App\Services\RaidService $service
     * @param int                        $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteRaidBoss(Request $request, RaidService $service, $id) {
        if ($id && $service->deleteRaidBoss(RaidBoss::find($id))) {
            flash('Raid Boss deleted successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->to('admin/data/raids/bosses');
    }
}
