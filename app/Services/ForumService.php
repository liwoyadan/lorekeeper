<?php

namespace App\Services;

use App\Models\Forum\Forum;
use App\Models\Forum\ForumDecor;
use App\Models\Forum\ForumFlair;
use App\Models\User\UserForumDecor;
use App\Models\User\UserForumFlair;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;

class ForumService extends Service {
    /*
    |--------------------------------------------------------------------------
    | Forum Service
    |--------------------------------------------------------------------------
    |
    | Handles the creation and editing of site forums.
    |
    */

    /**********************************************************************************************

        FORUMS

    **********************************************************************************************/

    /**
     * Creates a site forum.
     *
     * @param array                 $data
     * @param \App\Models\User\User $user
     *
     * @return bool|Forum
     */
    public function createForum($data, $user) {
        DB::beginTransaction();

        try {
            $data = $this->populateData($data);

            $image = null;
            if (isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $data['hash'] = randomString(10);
                $data['extension'] = $data['image']->getClientOriginalExtension();
                $image = $data['image'];
                unset($data['image']);
            } else {
                $data['has_image'] = 0;
            }
            $icon = null;
            if (isset($data['icon']) && $data['icon']) {
                $data['has_icon'] = 1;
                $data['icon_hash'] = randomString(10);
                $data['icon_extension'] = $data['icon']->getClientOriginalExtension();
                $icon = $data['icon'];
                unset($data['icon']);
            } else {
                $data['has_icon'] = 0;
            }

            $forum = Forum::create($data);

            if ($image) {
                $this->handleImage($image, $forum->imagePath, $forum->imageFileName);
            }
            if ($icon) {
                $this->handleImage($icon, $forum->imagePath, $forum->iconFileName);
            }

            return $this->commitReturn($forum);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Updates a site forum.
     *
     * @param Forum                 $forum
     * @param array                 $data
     * @param \App\Models\User\User $user
     *
     * @return bool|Forum
     */
    public function updateForum($forum, $data, $user) {
        DB::beginTransaction();

        try {
            $data = $this->populateData($data, $forum);

            $image = null;
            if (isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $data['hash'] = randomString(10);
                $data['extension'] = $data['image']->getClientOriginalExtension();
                $image = $data['image'];
                unset($data['image']);
            }

            $icon = null;
            if (isset($data['icon']) && $data['icon']) {
                $data['has_icon'] = 1;
                $data['icon_hash'] = randomString(10);
                $data['icon_extension'] = $data['icon']->getClientOriginalExtension();
                $icon = $data['icon'];
                unset($data['icon']);
            }
            $forum->update($data);

            if (!$forum->parent_id && $forum->characters_enabled) {
                $forum->characters_enabled = 0;
                $forum->save();

                flash('Forum categories cannot have characters enabled for posts, you must toggle it on the child forums themselves!')->warning();
            }

            if ($image) {
                $this->handleImage($image, $forum->imagePath, $forum->imageFileName);
            }
            if ($icon) {
                $this->handleImage($icon, $forum->imagePath, $forum->iconFileName);
            }

            return $this->commitReturn($forum);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Deletes a site forum.
     *
     * @param Forum $forum
     * @param mixed $data
     *
     * @return bool
     */
    public function deleteForum($forum, $data) {
        DB::beginTransaction();

        try {
            if (isset($forum->extension)) {
                $this->deleteImage($forum->imagePath, $forum->imageFileName);
            }
            if (isset($data['child_boards']) && $data['child_boards']) {
                if (!$this->recursiveDeletion($forum)) {
                    throw new \Exception('Could not delete children.');
                }
            } else {
                foreach ($forum->children as $child) {
                    $child->update(['parent_id' => $forum->parent_id]);
                }
            }

            $forum->delete();

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**********************************************************************************************

        FORUM FLAIRS

    **********************************************************************************************/

    /**
     * Creates a forum flair.
     *
     * @param array                 $data
     * @param \App\Models\User\User $user
     *
     * @return bool|ForumFlair
     */
    public function createForumFlair($data, $user) {
        DB::beginTransaction();

        try {
            $data = $this->populateFlairData($data);

            $image = null;
            if (isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $data['hash'] = randomString(10);
                $data['extension'] = $data['image']->getClientOriginalExtension();
                $image = $data['image'];
                unset($data['image']);
            } else {
                $data['has_image'] = 0;
            }

            $flair = ForumFlair::create(Arr::only($data, ['name', 'post_requirement', 'description', 'parsed_description', 'color', 'bg_color', 'has_image', 'extension', 'hash', 'staff_only', 'is_default', 'is_visible']));

            if (isset($data['text_shadow_color'])) {
                $flairData = $flair->data;
                $flairData['text_shadow'] = $this->populateTextShadows(Arr::only($data, ['text_shadow_x', 'text_shadow_y', 'text_shadow_blur', 'text_shadow_color']));
                $flair->data = $flairData;
                $flair->save();
            }

            if ($image) {
                $this->handleImage($image, $flair->imagePath, $flair->imageFileName);
            }

            return $this->commitReturn($flair);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Updates a forum flair.
     *
     * @param ForumFlair            $flair
     * @param array                 $data
     * @param \App\Models\User\User $user
     *
     * @return bool|ForumFlair
     */
    public function updateForumFlair($flair, $data, $user) {
        DB::beginTransaction();

        try {
            $data = $this->populateFlairData($data, $flair);

            $image = null;
            if (isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $data['hash'] = randomString(10);
                $data['extension'] = $data['image']->getClientOriginalExtension();
                $image = $data['image'];
                unset($data['image']);
            }

            $flair->update(Arr::only($data, ['name', 'post_requirement', 'description', 'parsed_description', 'color', 'bg_color', 'has_image', 'extension', 'hash', 'staff_only', 'is_default', 'is_visible']));

            if (isset($data['text_shadow_color'])) {
                $flairData = $flair->data;
                $flairData['text_shadow'] = $this->populateTextShadows(Arr::only($data, ['text_shadow_x', 'text_shadow_y', 'text_shadow_blur', 'text_shadow_color']));
                $flair->data = $flairData;
                $flair->save();
            }

            if ($image) {
                $this->handleImage($image, $flair->imagePath, $flair->imageFileName);
            }

            return $this->commitReturn($flair);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Deletes a forum flair.
     *
     * @param ForumFlair $flair
     *
     * @return bool
     */
    public function deleteForumFlair($flair) {
        DB::beginTransaction();

        try {
            if ($flair->has_image && isset($flair->extension)) {
                $this->deleteImage($flair->imagePath, $flair->imageFileName);
            }

            $flair->delete();

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**********************************************************************************************

        FORUM DECORS

    **********************************************************************************************/

    /**
     * Creates a forum decor.
     *
     * @param array                 $data
     * @param \App\Models\User\User $user
     *
     * @return bool|ForumDecor
     */
    public function createForumDecor($data, $user) {
        DB::beginTransaction();

        try {
            $data = $this->populateDecorData($data);

            $image = null;
            if (isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $data['hash'] = randomString(10);
                $data['extension'] = $data['image']->getClientOriginalExtension();
                $image = $data['image'];
                unset($data['image']);
            } else {
                $data['has_image'] = 0;
            }

            $decor = ForumDecor::create($data);

            if ($image) {
                $this->handleImage($image, $decor->imagePath, $decor->imageFileName);
            }

            return $this->commitReturn($decor);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Updates a forum decor.
     *
     * @param ForumDecor            $decor
     * @param array                 $data
     * @param \App\Models\User\User $user
     *
     * @return bool|ForumDecor
     */
    public function updateForumDecor($decor, $data, $user) {
        DB::beginTransaction();

        try {
            $data = $this->populateDecorData($data, $decor);

            $image = null;
            if (isset($data['image']) && $data['image']) {
                $data['has_image'] = 1;
                $data['hash'] = randomString(10);
                $data['extension'] = $data['image']->getClientOriginalExtension();
                $image = $data['image'];
                unset($data['image']);
            }

            $decor->update($data);

            if ($image) {
                $this->handleImage($image, $decor->imagePath, $decor->imageFileName);
            }

            return $this->commitReturn($decor);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Deletes a forum decor.
     *
     * @param ForumDecor $decor
     *
     * @return bool
     */
    public function deleteForumDecor($decor) {
        DB::beginTransaction();

        try {
            if ($decor->has_image && isset($decor->extension)) {
                $this->deleteImage($decor->imagePath, $decor->imageFileName);
            }

            $decor->delete();

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Uploads or removes a user's custom forum post background image.
     *
     * @param \Illuminate\Http\UploadedFile|null $image
     * @param bool                               $remove
     * @param \App\Models\User\User              $user
     * @param mixed|null                         $opacity
     *
     * @return bool
     */
    public function updateForumBackground($image, $remove, $user, $opacity = null) {
        DB::beginTransaction();

        try {
            $config = config('lorekeeper.forums.user_uploads.background');

            if (!$config['enabled']) {
                throw new \Exception('User forum background uploads are not enabled.');
            }

            $profile = $user->profile;

            if ($remove) {
                if ($profile->forum_bg_hash) {
                    $this->deleteImage(public_path($profile->forumBgDirectory), $profile->forumBgFileName);
                }
                $forumDecor = $profile->forum_decor ?? [];
                unset($forumDecor['background']);
                $profile->update([
                    'forum_bg_hash'      => null,
                    'forum_bg_extension' => null,
                    'forum_bg_opacity'   => null,
                    'forum_decor'        => $forumDecor ?: null,
                ]);

                return $this->commitReturn(true);
            }

            if (!$image) {
                throw new \Exception('Please upload a file.');
            }

            $img = Image::make($image);
            $maxDimension = $config['max_dimension'] ?? 1000;

            if ($img->width() > $maxDimension || $img->height() > $maxDimension) {
                throw new \Exception("Image dimensions must not exceed {$maxDimension}px on either side.");
            }

            if ($profile->forum_bg_hash) {
                $this->deleteImage(public_path($profile->forumBgDirectory), $profile->forumBgFileName);
            }

            $hash = randomString(10);
            $extension = $image->getClientOriginalExtension();
            $directory = public_path($profile->forumBgDirectory);

            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            $img->save($directory.'/'.$user->id.$hash.'.'.$extension);

            $maxOpacity = $config['max_opacity'] ?? 100;
            $defaultOpacity = $config['default_opacity'] ?? 15;
            $resolvedOpacity = $opacity !== null ? min($maxOpacity, max(0, (int) $opacity)) : $defaultOpacity;

            $forumDecor = $profile->forum_decor ?? [];
            $forumDecor['background'] = 'custom';
            $profile->update([
                'forum_bg_hash'      => $hash,
                'forum_bg_extension' => $extension,
                'forum_bg_opacity'   => $resolvedOpacity,
                'forum_decor'        => $forumDecor,
            ]);

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Credits a forum flair to a user.
     *
     * @param mixed      $sender
     * @param mixed      $recipient
     * @param mixed      $forumFlair
     * @param mixed|null $logType
     * @param mixed|null $data
     *
     * @return bool
     */
    public function creditFlair($sender, $recipient, $logType = null, $data = null, $forumFlair = null) {
        DB::beginTransaction();

        try {
            if (!$forumFlair) {
                throw new \Exception('Invalid forum flair.');
            }

            if ($recipient->forumFlairs()->where('forum_flair_id', $forumFlair->id)->exists()) {
                throw new \Exception('User already owns this forum flair.');
            }

            UserForumFlair::create([
                'user_id'        => $recipient->id,
                'forum_flair_id' => $forumFlair->id,
            ]);

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Credits a forum decor to a user.
     *
     * @param mixed      $sender
     * @param mixed      $recipient
     * @param mixed      $forumDecor
     * @param mixed|null $logType
     * @param mixed|null $data
     *
     * @return bool
     */
    public function creditDecor($sender, $recipient, $logType = null, $data = null, $forumDecor = null) {
        DB::beginTransaction();

        try {
            if (!$forumDecor) {
                throw new \Exception('Invalid forum decor.');
            }

            if ($recipient->forumDecors()->where('forum_decor_id', $forumDecor->id)->exists()) {
                throw new \Exception('User already owns this forum decor.');
            }

            UserForumDecor::create([
                'user_id'        => $recipient->id,
                'forum_decor_id' => $forumDecor->id,
            ]);

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**********************************************************************************************

        OTHER FUNCTIONS

    **********************************************************************************************/

    /**
     * Processes user input for creating/updating a forum.
     *
     * @param array                 $data
     * @param \App\Models\Item\Item $forum
     *
     * @return array
     */
    private function populateData($data, $forum = null) {
        (isset($data['description']) && $data['description']) ? $data['description'] : $data['description'] = null;
        if (isset($data['description']) && $data['description']) {
            $data['parsed_description'] = parse($data['description']);
        } else {
            $data['parsed_description'] = null;
        }

        if (!isset($data['color'])) {
            $data['color'] = null;
        }
        if (!isset($data['is_active'])) {
            $data['is_active'] = 0;
        }
        if (!isset($data['is_locked'])) {
            $data['is_locked'] = 0;
        }
        if (!isset($data['sort'])) {
            $data['sort'] = 0;
        }
        if (!isset($data['staff_only'])) {
            $data['staff_only'] = 0;
        }
        if (!isset($data['role_limit'])) {
            $data['role_limit'] = null;
        }
        if (!isset($data['parent_id'])) {
            $data['parent_id'] = null;
        }
        if (!isset($data['forum_rules'])) {
            $data['forum_rules'] = null;
        }
        if (!isset($data['forum_styles'])) {
            $data['forum_styles'] = null;
        } else {
            if (!isset($data['forum_styles']['use_board_bg'])) {
                $data['forum_styles']['use_board_bg'] = 0;
            } else {
                if (!isset($data['forum_styles']['board_bg_opacity'])) {
                    $data['forum_styles']['board_bg_opacity'] = 15;
                }
            }
        }

        if (isset($data['remove_image']) && $data['remove_image']) {
            if ($forum && $forum->has_image && $data['remove_image']) {
                $data['has_image'] = 0;
                $this->deleteImage($forum->imagePath, $forum->imageFileName);
            }
            $data['hash'] = null;
            $data['extension'] = null;
            unset($data['remove_image']);
        }
        if (isset($data['remove_icon']) && $data['remove_icon']) {
            if ($forum && $forum->has_icon && $data['remove_icon']) {
                $data['has_icon'] = 0;
                $this->deleteImage($forum->imagePath, $forum->iconFileName);
            }
            $data['icon_hash'] = null;
            $data['icon_extension'] = null;
            unset($data['remove_icon']);
        }

        return $data;
    }

    /**
     * Recursively delete all children.
     *
     * @param mixed $forum
     */
    private function recursiveDeletion($forum) {
        try {
            if (count($forum->children)) {
                foreach ($forum->children as $board) {
                    if (isset($forum->extension)) {
                        $this->deleteImage($forum->imagePath, $forum->imageFileName);
                    }
                    $this->recursiveDeletion($board);
                    $forum->delete();

                    return true;
                }
            } else {
                if (isset($forum->extension)) {
                    $this->deleteImage($forum->imagePath, $forum->imageFileName);
                }
                $forum->delete();

                return true;
            }
        } catch (\Exception $e) {
            return false;
        }

        return false;
    }

    /**
     * Processes user input for creating/updating a forum flair.
     *
     * @param array           $data
     * @param ForumFlair|null $flair
     *
     * @return array
     */
    private function populateFlairData($data, $flair = null) {
        if (isset($data['description']) && $data['description']) {
            $data['parsed_description'] = parse($data['description']);
        } else {
            $data['description'] = null;
            $data['parsed_description'] = null;
        }

        if (!isset($data['post_requirement']) || !$data['post_requirement']) {
            $data['post_requirement'] = null;
        }
        if (!isset($data['color'])) {
            $data['color'] = null;
        }
        if (!isset($data['bg_color'])) {
            $data['bg_color'] = null;
        }

        if (!isset($data['staff_only'])) {
            $data['staff_only'] = 0;
        }
        if (!isset($data['is_default'])) {
            $data['is_default'] = 0;
        }
        if (!isset($data['is_visible'])) {
            $data['is_visible'] = 1;
        }
        if (!isset($data['characters_enabled'])) {
            $data['characters_enabled'] = 0;
        }

        // Handle image removal
        if (isset($data['remove_image']) && $data['remove_image']) {
            if ($flair && $flair->has_image) {
                $data['has_image'] = 0;
                $this->deleteImage($flair->imagePath, $flair->imageFileName);
            }
            unset($data['remove_image']);
        }

        return $data;
    }

    /**
     * Processes user input for forum flair text shadows.
     *
     * @param array $data
     *
     * @return array
     */
    private function populateTextShadows($data) {
        $units = ['px', 'em', 'rem', '%'];
        $unitCheck = '/^[-+]?[0-9]*\.?[0-9]+('.implode('|', $units).')$/i';

        $shadowData = [];
        $c = 0;
        foreach ($data['text_shadow_color'] as $key => $shadow) {
            if (!isset($data['text_shadow_x'][$key])) {
                $shadowData[$c]['offset_x'] = '0px';
            } elseif (!preg_match($unitCheck, $data['text_shadow_x'][$key])) {
                $shadowData[$c]['offset_x'] = filter_var($data['text_shadow_x'][$key], FILTER_SANITIZE_NUMBER_INT).'px';
            } else {
                $shadowData[$c]['offset_x'] = $data['text_shadow_x'][$key];
            }

            if (!isset($data['text_shadow_y'][$key])) {
                $shadowData[$c]['offset_y'] = '0px';
            } elseif (!preg_match($unitCheck, $data['text_shadow_y'][$key])) {
                $shadowData[$c]['offset_y'] = filter_var($data['text_shadow_y'][$key], FILTER_SANITIZE_NUMBER_INT).'px';
            } else {
                $shadowData[$c]['offset_y'] = $data['text_shadow_y'][$key];
            }

            if (!isset($data['text_shadow_blur'][$key])) {
                $shadowData[$c]['blur_radius'] = '0px';
            } elseif (!preg_match($unitCheck, $data['text_shadow_blur'][$key])) {
                $shadowData[$c]['blur_radius'] = filter_var($data['text_shadow_blur'][$key], FILTER_SANITIZE_NUMBER_INT).'px';
            } else {
                $shadowData[$c]['blur_radius'] = $data['text_shadow_blur'][$key];
            }

            $shadowData[$c]['color'] = $data['text_shadow_color'][$key] ?? 'rgba(0, 0, 0,)';
            $c++;
        }

        return $shadowData;
    }

    /**
     * Processes user input for creating/updating a forum decor.
     *
     * @param array           $data
     * @param ForumDecor|null $decor
     *
     * @return array
     */
    private function populateDecorData($data, $decor = null) {
        if (isset($data['description']) && $data['description']) {
            $data['parsed_description'] = parse($data['description']);
        } else {
            $data['description'] = null;
            $data['parsed_description'] = null;
        }

        if (!isset($data['staff_only'])) {
            $data['staff_only'] = 0;
        }
        if (!isset($data['is_default'])) {
            $data['is_default'] = 0;
        }
        if (!isset($data['is_visible'])) {
            $data['is_visible'] = 1;
        }

        // Handle type-specific data fields
        $typeData = [];
        if (isset($data['type']) && ($data['type'] == 'background')) {
            $typeData['opacity'] = isset($data['opacity']) ? max(0, min(100, (int) $data['opacity'])) : 15;
            $typeData['background_repeat'] = isset($data['background_repeat']);
            $typeData['background_size'] = $data['background_size'] ?? 'cover';
        } elseif (isset($data['type']) && ($data['type'] == 'border')) {
            $typeData['border_image_slice'] = $data['border_image_slice'] ?? null;
            $typeData['border_image_width'] = $data['border_image_width'] ?? null;
            $typeData['border_image_outset'] = $data['border_image_outset'] ?? null;
            $typeData['border_image_repeat'] = $data['border_image_repeat'] ?? null;
        }
        $data['data'] = $typeData ?: null;
        unset($data['opacity'], $data['background_repeat'], $data['background_size'], $data['border_image_slice'], $data['border_image_width'], $data['border_image_outset'], $data['border_image_repeat']);

        // Handle image removal
        if (isset($data['remove_image']) && $data['remove_image']) {
            if ($decor && $decor->has_image) {
                $data['has_image'] = 0;
                $this->deleteImage($decor->imagePath, $decor->imageFileName);
            }
            unset($data['remove_image']);
        }

        return $data;
    }
}
