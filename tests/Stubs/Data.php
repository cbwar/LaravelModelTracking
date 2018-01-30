<?php

namespace Tests\Stubs;

use Cbwar\Laravel\ModelChanges\TrackedModel;

class Data extends TrackedModel
{
    protected $tracked = ['tracked1', 'tracked2', 'tracked3'];

    protected $fillable = ['tracked1', 'tracked2', 'tracked3'];

    public function getDates()
    {
        return ['created_at', 'updated_at', 'tracked3'];
    }

    public function trackedTitleField()
    {
        return $this->attributes['tracked1'];
    }
}
