<?php

namespace Cbwar\Laravel\ModelChanges;

use Adaptive\Diff\Diff;
use Adaptive\Diff\Renderer\Html\SideBySide;
use Cbwar\Laravel\ModelChanges\Models\Change;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class TrackedModelObserver
{
    /**
     * @param TrackedModel $model
     * @param string $type
     */
    private function addFromTrackedModel(TrackedModel $model, $type)
    {
        $user = Auth::user();
        $title = self::getEventName($model, $type);

        $description = '';
        if ($type === 'edit' && $this->isModified($model)) {
            // Show diff
            $description .= '<div>' . $this->modelDiff($model) . '</div>';
        }


        if ($title) {
            $t = new Change();
            $t->ref_model = get_class($model);
            $t->ref_id = $model->id;
            $t->ref_title = $model->trackedTitleField();
            $t->type = $type;
            if ($user !== null) {
                $t->user_id = $user->id;
            }
            $t->title = $title;
            $t->description = $description;
            if (($parent = $model->trackedParentField()) !== null) {
                $t->parent_ref_model = get_class($parent);
                $t->parent_ref_id = $parent->id;
            }
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
            if ((string)$value !== (string)$new_value && in_array($key, $tracked, true)) {
                $column_type = Schema::getColumnType($model->getTable(), $key);

                if ($column_type === 'string' || $column_type === 'text') {
                    $diff = new Diff(
                        explode("\n", strip_tags($value)),
                        explode("\n", strip_tags($new_value)),
                        ['ignoreWhitespace' => true]
                    );
                    $string .= sprintf(
                        "<div class=\"tracks-field\">%s</div><div class=\"tracks-diff\">%s</div>\n",
                        $key,
                        $diff->Render(new SideBySide(['showEquals' => false]))
                    );
                } else {
                    $string .= sprintf('<div class="tracks-field">%s</div>', $key);
                    $string .= sprintf("<div class=\"tracks-diff\">%s => %s</div>\n", $value, $new_value);
                }
            }
        }

        return $string;
    }

    /**
     * Check if tracked field is modified.
     *
     * @param TrackedModel $model
     *
     * @return bool
     */
    private function isModified(TrackedModel $model)
    {
        foreach ($model->getTracked() as $field) {
            if ($model->getOriginal($field) !== $model->getAttribute($field)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get event name for description.
     *
     * @param TrackedModel $model
     * @param string $type
     * @return string
     * @throws TrackableError
     */
    private function getEventName(TrackedModel $model, $type)
    {
        $events = array_merge(TrackedModel::$tracking_event_names_default, $model->getEventNames());

        if (!isset($events[$type])) {
            throw new TrackableError('no event name defined for type ' . $type);
        }

        return $events[$type];
    }

    /**
     * Create model event.
     *
     * @param TrackedModel $model
     */
    public function created(TrackedModel $model)
    {
        $this->addFromTrackedModel($model, 'add');
    }

    /**
     * Edit model event.
     *
     * @param TrackedModel $model
     */
    public function updated(TrackedModel $model)
    {
        if ($this->isModified($model)) {
            $this->addFromTrackedModel($model, 'edit');
        }
    }

    /**
     * Delete model event.
     *
     * @param TrackedModel $model
     */
    public function deleted(TrackedModel $model)
    {
        if (method_exists($model, 'isForceDeleting')
            && false === $model->isForceDeleting()
            || config('modelchanges.keep_deleted_items_changes') === true
        ) {
            // Soft delete
            $this->addFromTrackedModel($model, 'delete');
        }
    }
}
