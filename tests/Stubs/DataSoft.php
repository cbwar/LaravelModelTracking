<?php

namespace Tests\Stubs;

use Cbwar\Laravel\ModelChanges\TrackedModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class DataSoft extends TrackedModel
{
    use SoftDeletes;

    protected $dates = ['updated_at', 'created_at', 'deleted_at'];

    protected $tracked = ['tracked1', 'tracked2'];

    public function trackedTitleField()
    {
        return $this->attributes['tracked1'];
    }
}
