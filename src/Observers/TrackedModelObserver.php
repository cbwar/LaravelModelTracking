<?php


namespace Cbwar\Laravel\ModelTracking\Observers;


use Cbwar\Laravel\ModelTracking\Models\Track;
use Cbwar\Laravel\ModelTracking\Models\TrackedModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class TrackedModelObserver
{

    private static $typeVerbs = [
        'add' => 'added',
        'edit' => 'updated',
        'delete' => 'deleted'
    ];

    /**
     * @param TrackedModel $model
     * @param string $type
     */
    private function addFromTrackedModel(TrackedModel $model, $type)
    {
        $user = Auth::user();
        $sentence = self::getSentence($model, $type);

        if ($sentence) {
            $t = new Track();
            $t->ref_model = get_class($model);
            $t->ref_id = $model->id;
            $t->ref_title = $model->trackedTitleField();
            $t->type = $type;
            if ($user !== null) {
                $t->user_id = $user->id;
            }
            $t->description = $sentence;
            $t->save();
        }
    }

    /**
     * @param array $old Old values
     * @param array $new New values
     * @param array $fields Tracked fields
     */
    private function modelDiff(TrackedModel $model)
    {
        $old = $model->getOriginal();
        $new = $model->getAttributes();
        $tracked = $model->getTracked();

        $string = '';
        foreach ($old as $key => $value) {
            $new_value = $new[$key];

            if ($value != $new_value && in_array($key, $tracked)) {
                $column_type = Schema::getColumnType($model->getTable(), $key);

                if ($column_type === 'string' || $column_type === 'text') {
                    $value = Str::limit($value, 40);
                    $new_value = Str::limit($new_value, 40);
                }

                $string .= sprintf("%s : %s => %s\n", $key, $value, $new_value);
            }
        }
        return $string;
    }

    /**
     * Check if tracked field is modified
     * @param TrackedModel $model
     * @return bool
     */
    private function isModified(TrackedModel $model)
    {
        foreach ($model->getTracked() as $field) {
            if ($model->getOriginal($field) != $model->getAttribute($field)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get sentence for description
     * @param TrackedModel $model
     * @param string $type
     * @return string
     */
    private function getSentence(TrackedModel $model, $type)
    {
        if (isset($model->trackedSentences()[$type])) {
            $sentence = $model->trackedSentences()[$type];
        } else {
            $sentence = sprintf("The item was %s", static::$typeVerbs[$type]);
        }

        if ($type === 'edit' && $this->isModified($model)) {
            // Show diff
            $sentence .= '<div>' . $this->modelDiff($model) . '</div>';
        }

        return $sentence;
    }

    /**
     * Create model event
     * @param TrackedModel $model
     */
    public function created(TrackedModel $model)
    {
        $this->addFromTrackedModel($model, 'add');
    }


    /**
     * Edit model event
     * @param TrackedModel $model
     */
    public function updated(TrackedModel $model)
    {
        if ($this->isModified($model)) {
            $this->addFromTrackedModel($model, 'edit');
        }
    }

    /**
     * Delete model event
     * @param TrackedModel $model
     */
    public function deleted(TrackedModel $model)
    {
        $reflexion = new \ReflectionClass($model);

        if ($reflexion->hasMethod('restore') && $model->forceDeleting === false) {
            // Soft delete
            $this->addFromTrackedModel($model, 'delete');
        }
    }


}