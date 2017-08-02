<?php

namespace Cbwar\Laravel\ModelTracking\Traits;

trait TrackableFields
{

    /**
     * Tracked fields
     * @var array
     */
    protected $tracked = ['*'];


    /**
     * Get tracked fields
     * @return array
     */
    public function getTracked()
    {
        return ($this->tracked == ['*']) ? array_keys($this->attributes) : $this->tracked;
    }

    /**
     * Determine if the given key is tracked.
     *
     * @param  string $key
     * @return bool
     */
    public function isTracked($key)
    {
        return in_array($key, $this->getTracked()) || $this->getTracked() == ['*'];
    }

}