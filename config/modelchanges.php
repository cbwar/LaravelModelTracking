<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Laravel Model Changes
    |--------------------------------------------------------------------------
    |
    */

    // User class to link changes made by logged user
    'user_class' => null,

    // Do not delete rows from changes table even when soft delete is disabled
    'keep_deleted_items_changes' => false,
];
