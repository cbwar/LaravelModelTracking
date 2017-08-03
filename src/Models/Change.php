<?php

namespace Cbwar\Laravel\ModelChanges\Models;

use Cbwar\Laravel\ModelChanges\Errors\TrackableError;
use Illuminate\Database\Eloquent\Model;

class Change extends Model
{
    protected $guarded = [];


    public function __call($method, $parameters)
    {
        if ($method === 'user') {

            if (($className = config('modelchanges.user_class')) !== null) {
                return self::hasOne($className, 'id', 'user_id');
            }
            throw new TrackableError('user_class key not defined in config');
        }

        return parent::__call($method, $parameters);
    }
}
