<?php

namespace Cbwar\Laravel\ModelChanges;

trait TrackableFieldsTrait
{
    /**
     * Tracked fields.
     *
     * @var array
     */
    protected $tracked = ['*'];

    /**
     * Custom sentences.
     *
     * @var array
     */
    protected $sentences = [];

    /**
     * Get tracked fields.
     *
     * @return array
     */
    public function getTracked()
    {
        return ($this->tracked === ['*']) ? array_keys($this->attributes) : $this->tracked;
    }

    /**
     * Determine if the given key is tracked.
     *
     * @param string $key
     *
     * @return bool
     */
    public function isTracked($key)
    {
        return in_array($key, $this->getTracked(), true) || $this->getTracked() === ['*'];
    }

    /**
     * Get sentences.
     *
     * @return array
     */
    public function getSentences()
    {
        return $this->sentences;
    }
}
