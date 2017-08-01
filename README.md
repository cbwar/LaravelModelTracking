# LaravelModelTracking

Track model changes made by users into database.

## Installation

1. Install with composer :

```
composer require cbwar/laravel-modeltracking
```

2. Open ```config/app.php``` and add the following to the ```providers``` array :

```
Cbwar\Laravel\ModelTracking\ServiceProvider::class,
```


3. After you set your database parameters in your ```.env``` file run :
```
php artisan migrate
```

## Usage

```php
<?php

namespace App;

use Cbwar\Laravel\ModelTracking\Models\TrackedModel;

class Project extends TrackedModel
{
    protected $guarded = [];

    public function trackableNameField()
    {
        return $this->attributes['title'];
    }

}

```
