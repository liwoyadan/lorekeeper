<?php

namespace App\Traits;

use Spatie\Tags\HasTags;
use Spatie\Tags\Tag;

trait ConfiguredTags {
    use HasTags;

    public function tagType() {
        $key    = class_basename($this);
        $config = config('lorekeeper.tags.types.'.$key);

        if (is_string($config) && $config != '') {
            return $config;
        }

        if ($config) {
            return 'global';
        }

        return strtolower($key);
    }

    public function tagSlug() {
        foreach (config('lorekeeper.tags.models', []) as $slug => $class) {
            if ($class == static::class) {
                return $slug;
            }
        }

        return strtolower(class_basename($this));
    }

    public function syncConfiguredTags($tags) {
        return $this->syncTagsWithType($tags ?: [], $this->tagType());
    }

    public function configuredTags() {
        return $this->tagsWithType($this->tagType());
    }

    public function availableTagOptions() {
        return Tag::getWithType($this->tagType())->pluck('name')->all();
    }
}
