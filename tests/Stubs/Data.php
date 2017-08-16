<?php


namespace Tests\Stubs;


use Cbwar\Laravel\ModelChanges\TrackedModel;

class Data extends TrackedModel
{

    protected $tracked = ['tracked1', 'tracked2'];

    public function trackedTitleField()
    {
        return $this->attributes['tracked1'];
    }


}