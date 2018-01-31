<?php

namespace Cbwar\Laravel\ModelChanges;

use Cbwar\Laravel\ModelChanges\Models\Change;
use Illuminate\Database\Eloquent\Model;

abstract class TrackedModel extends Model
{
    use TrackableFieldsTrait;

    /**
     * Registered events.
     *
     * @var array
     */
    protected $events = [
        'created' => TrackedModelObserver::class,
        'updated' => TrackedModelObserver::class,
        'deleted' => TrackedModelObserver::class,
    ];

    /**
     * Default event names for change log.
     *
     * @var array
     */
    public static $tracking_event_names_default = [];

    public static function boot()
    {
        parent::boot();

        // Bind observer
        self::observe(TrackedModelObserver::class);

        // Remove tracks
        static::deleting(function (TrackedModel $model) {
            if (config('modelchanges.keep_deleted_items_changes') === true) {
                // Always keep changes rows
                return;
            }

            if (!method_exists($model, 'isForceDeleting') || true === $model->isForceDeleting()) {
                // Model is not using soft deletion
                $model->tracks()->delete();
            }
        });

        static::$tracking_event_names_default = [
            'add' => __('modelchanges::default.event_name.add'),
            'edit' => __('modelchanges::default.event_name.edit'),
            'delete' => __('modelchanges::default.event_name.delete'),
            'show' => __('modelchanges::default.event_name.show'),
            'archive' => __('modelchanges::default.event_name.archive'),
        ];
    }

    /**
     * @throws TrackableError
     *
     * @return string
     */
    public function trackedTitleField()
    {
        if (!isset($this->attributes['title'])) {
            throw new TrackableError(
                'title attribute not defined in model ' . static::class .
                ', override trackedTitleField method.'
            );
        }

        return $this->attributes['title'];
    }

    public function trackedParentField()
    {
        return null;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function tracks()
    {
        return $this->morphMany(Change::class, null, 'ref_model', 'ref_id');
    }


    /**
     * @param string $type
     * @param User|null $user
     * @param string $description
     * @throws TrackableError
     */
    public function addTrack($type = 'show', $user = null, $description = '')
    {
        $t = new Change();
        $t->ref_model = get_class($this);
        $t->ref_id = $this->id;
        $t->ref_title = $this->trackedTitleField();
        $t->type = $type;
        if ($user !== null) {
            $t->user_id = $user->id;
        }
        $t->description = '';
        if ($description !== '') {
            $t->description = $description;
        } else {
            if (isset(static::$tracking_event_names_default[$type])) {
                $t->description = static::$tracking_event_names_default[$type];
            }
        }
        $t->save();
        return $t;
    }
}
