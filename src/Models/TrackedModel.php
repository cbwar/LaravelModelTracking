<?php


namespace Cbwar\Laravel\ModelTracking\Models;

use Cbwar\Laravel\ModelTracking\Observers\TrackedModelObserver;
use Illuminate\Database\Eloquent\Model;

abstract class TrackedModel extends Model
{
    protected $events = [
        'created' => TrackedModelObserver::class,
        'updated' => TrackedModelObserver::class,
        'deleted' => TrackedModelObserver::class,
    ];

    public static function boot()
    {
        self::observe(TrackedModelObserver::class);
    }

    abstract public function trackableNameField();
}