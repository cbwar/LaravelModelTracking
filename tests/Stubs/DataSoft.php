<?php

namespace Tests\Stubs;

use Illuminate\Database\Eloquent\SoftDeletes;

class DataSoft extends Data
{
    use SoftDeletes;
}
