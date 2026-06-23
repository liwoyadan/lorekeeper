<?php

namespace App\Models;

use App\Facades\Settings;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;

class Changelog extends Model {
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type', 'type_id', 'text', 'parsed_text', 'staff_only', 'staff_id',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'site_changelogs';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'staff_only' => 'boolean',
    ];

    /**
     * Whether the model contains timestamps to be saved and updated.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * Validation rules. Create and update share the same rules.
     *
     * @var array
     */
    public static $rules = [
        'type'    => 'required|string',
        'type_id' => 'nullable|integer',
        'text'    => 'required',
    ];

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the associated subject (polymorphic).
     * The 'type' column stores the model class path when
     * set to be a changelog for a specific model/object of a model.
     */
    public function subject() {
        return $this->morphTo(null, 'type', 'type_id');
    }

    /**
     * Get the staff member who created the changelog.
     */
    public function staff() {
        return $this->belongsTo(User::class, 'staff_id');
    }

    /**********************************************************************************************

        SCOPES

    **********************************************************************************************/

    /**
     * Scope a query to changelogs for a specific subject or subject type.
     * Either passes a subject (specific object) or a type
     * (which fetches all changelogs of that model).
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param object|null                           $subject
     * @param string|null                           $subjectType
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSubjectLogs($query, $subject = null, $subjectType = null) {
        if ($subject) {
            return $query->where('type', get_class($subject))->where('type_id', $subject->id);
        }
        if ($subjectType) {
            return $query->where('type', $subjectType);
        }

        return $query;
    }

    /**
     * Scope a query to filter staff only entries.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsStaff($query, $user = null, $staffOnly = null) {
        if (!$user || $user && !$user->isStaff) {
            return $query->where('staff_only', 0);
        }
        if ($staffOnly) {
            return $query->where('staff_only', 1);
        }

        return $query;
    }

    /**********************************************************************************************

        ACCESSORS

    **********************************************************************************************/

    /**
     * Get the human-readable label for the changelog's type.
     */
    public function getTypeLabelAttribute() {
        return config('lorekeeper.changelogs.subject_types.'.$this->type, $this->type);
    }

    /**
     * Get the display name for the changelog entry itself.
     */
    public function getDisplayNameAttribute() {
        if ($this->type_id && self::typeIsModel($this->type)) {
            $subject = $this->subject;
            if ($subject && isset($subject->displayName)) {
                return '('.$subject->displayName.add_help($this->typeLabel).')';
            }
        }

        return '('.$this->typeLabel.')';
    }

    /**
     * Check if the changelog is recent.
     */
    public function getIsRecentAttribute() {
        $days = Settings::get('recent_changelog_days') ?? 7;
        
        return $this->created_at->diffInDays(now()) <= $days;
    }

    /**
     * Whether the given type string identifies a loadable Eloquent model
     * (and therefore the subject() morphTo can be safely accessed).
     */
    public static function typeIsModel($type) {
        return $type && is_subclass_of($type, Model::class);
    }
}
