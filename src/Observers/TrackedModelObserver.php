<?php


namespace Cbwar\Laravel\ModelTracking\Observers;


use Cbwar\Laravel\ModelTracking\Models\Track;
use Cbwar\Laravel\ModelTracking\Models\TrackedModel;

class TrackedModelObserver
{

    public function created(TrackedModel $model)
    {
        print("TrackedModelObserver:created called\n");

        $t = new Track();
        $t->ref_table = $model->getTable();
        $t->ref_id = $model->id;
        $t->type = 'add';
        $t->description = sprintf("row '%s' added", $model->trackableNameField());
        $t->save();

    }


    public function updated(TrackedModel $model)
    {
        print('TrackedModelObserver:updated called');
        $t = new Track();
        $t->ref_table = $model->getTable();
        $t->ref_id = $model->id;
        $t->type = 'edit';
        $t->description = sprintf("row '%s' updated", $model->trackableNameField());
        $t->save();

    }


    public function deleted(TrackedModel $model)
    {
        print('TrackedModelObserver:deleted called');
        $t = new Track();
        $t->ref_table = $model->getTable();
        $t->ref_id = $model->id;
        $t->type = 'delete';
        $t->description = sprintf("row '%s' deleted", $model->trackableNameField());
        $t->save();
    }


}