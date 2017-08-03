<?php


namespace Cbwar\Laravel\ModelChanges\Models;

use Cbwar\Laravel\ModelChanges\Errors\TrackableError;
use Cbwar\Laravel\ModelChanges\Observers\TrackedModelObserver;
use Cbwar\Laravel\ModelChanges\Traits\TrackableFields;
use Illuminate\Database\Eloquent\Model;

abstract class TrackedModel extends Model
{
    use TrackableFields;

    /**
     * Registered events
     * @var array
     */
    protected $events = [
        'created' => TrackedModelObserver::class,
        'updated' => TrackedModelObserver::class,
        'deleted' => TrackedModelObserver::class,
    ];

    /**
     * Default sentences for change log
     * @var array
     */
    public static $tracking_sentences_default = [];

    /**
     *
     */
    public static function boot()
    {
        parent::boot();

        // Bind observer
        self::observe(TrackedModelObserver::class);

        // Remove tracks
        static::deleting(function (TrackedModel $model) {
            $model->tracks()->delete();
        });

        static::$tracking_sentences_default = [
            'add' => __('modelchanges::default.sentences.add'),
            'edit' => __('modelchanges::default.sentences.edit'),
            'delete' => __('modelchanges::default.sentences.delete'),
        ];
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
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function tracks()
    {
        return $this->morphMany(Change::class, null, 'ref_model', 'ref_id');
    }
}