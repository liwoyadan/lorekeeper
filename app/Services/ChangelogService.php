<?php

namespace App\Services;

use App\Models\Changelog;
use Illuminate\Support\Facades\DB;

class ChangelogService extends Service {
    /*
    |--------------------------------------------------------------------------
    | Changelog Service
    |--------------------------------------------------------------------------
    |
    | Handles the creation and editing of changelog entries.
    |
    */

    /**
     * Creates a new changelog entry.
     *
     * @param array                 $data
     * @param \App\Models\User\User $user
     *
     * @return bool|Changelog
     */
    public function createChangelog($data, $user) {
        DB::beginTransaction();

        try {
            $data = $this->populateData($data);
            $data['staff_id'] = $user->id;

            $changelog = Changelog::create($data);

            if (!$this->logAdminAction($user, 'Created Changelog', 'Created '.$changelog->displayName)) {
                throw new \Exception('Failed to log admin action.');
            }

            return $this->commitReturn($changelog);
        } catch (\Throwable $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Updates a changelog entry.
     *
     * @param Changelog             $changelog
     * @param array                 $data
     * @param \App\Models\User\User $user
     *
     * @return bool|Changelog
     */
    public function updateChangelog($changelog, $data, $user) {
        DB::beginTransaction();

        try {
            $data = $this->populateData($data);

            $changelog->update($data);

            if (!$this->logAdminAction($user, 'Updated Changelog', 'Updated '.$changelog->displayName)) {
                throw new \Exception('Failed to log admin action.');
            }

            return $this->commitReturn($changelog);
        } catch (\Throwable $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Deletes a changelog entry.
     *
     * @param Changelog $changelog
     * @param mixed     $user
     *
     * @return bool
     */
    public function deleteChangelog($changelog, $user) {
        DB::beginTransaction();

        try {
            $displayName = $changelog->displayName;

            $changelog->delete();

            if (!$this->logAdminAction($user, 'Deleted Changelog', 'Deleted '.$displayName)) {
                throw new \Exception('Failed to log admin action.');
            }

            return $this->commitReturn(true);
        } catch (\Throwable $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Processes user input for creating/updating a changelog entry.
     * Does not set staff_id; createChangelog assigns that once and
     * updates leave the original author intact.
     *
     * @param array $data
     *
     * @return array
     */
    private function populateData($data) {
        if (!isset($data['type_id']) || !$data['type_id']) {
            $data['type_id'] = null;
        }
        $data['staff_only'] = isset($data['staff_only']) && $data['staff_only'] ? 1 : 0;
        if (array_key_exists('text', $data)) {
            $data['parsed_text'] = parse($data['text']);
        }

        return $data;
    }
}
