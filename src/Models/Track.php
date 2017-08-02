<?php

namespace Cbwar\Laravel\ModelTracking\Models;

use Cbwar\Laravel\ModelTracking\Errors\TrackableError;
use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    protected $guarded = [];


    public function __call($method, $parameters)
    {
        if ($method === 'user') {

            if (($className = config('modeltracking.user_class')) !== null) {
                return self::hasOne($className, 'id', 'user_id');
            }
            throw new TrackableError('user_class key not defined in config');
        }

        return parent::__call($method, $parameters);
    }
}
