<?php


namespace Cbwar\Laravel\ModelTracking\Models;

use Cbwar\Laravel\ModelTracking\Errors\TrackableError;
use Cbwar\Laravel\ModelTracking\Observers\TrackedModelObserver;
use Cbwar\Laravel\ModelTracking\Traits\TrackableFields;
use Illuminate\Database\Eloquent\Model;

abstract class TrackedModel extends Model
{
    use TrackableFields;

    protected $events = [
        'created' => TrackedModelObserver::class,
        'updated' => TrackedModelObserver::class,
        'deleted' => TrackedModelObserver::class,
    ];

    public static function boot()
    {
        parent::boot();

        // Bind observer
        self::observe(TrackedModelObserver::class);

        // Remove tracks
        static::deleting(function (TrackedModel $model) {
            $model->tracks()->delete();
        });
    }

    /**
     * @return string
     * @throws TrackableError
     */
    public function trackedTitleField()
    {
        if (!isset($this->attributes['title'])) {
            throw new TrackableError('title attribute not defined in model ' . static::class . ', override trackableTitleField method.');
        }
        return $this->attributes['title'];
    }

    /**
     * @return array
     */
    abstract public function trackedSentences();

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function tracks()
    {
        return $this->morphMany(Track::class, null, 'ref_model', 'ref_id');
    }
}