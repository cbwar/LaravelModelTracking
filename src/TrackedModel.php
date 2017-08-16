<?php


namespace Cbwar\Laravel\ModelChanges;

use Illuminate\Database\Eloquent\Model;

abstract class TrackedModel extends Model
{
    use TrackableFieldsTrait;

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

            if (config('modelchanges.keep_deleted_items_changes') === true) {
                return;
            }

            if (!method_exists($model, 'isForceDeleting') || true === $model->isForceDeleting()) {
                $model->tracks()->delete();
            }
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