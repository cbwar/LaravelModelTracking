# Laravel Model Changes

[![Build Status](https://travis-ci.org/cbwar/laravel-modelchanges.svg?branch=master)](https://travis-ci.org/cbwar/laravel-modelchanges)

Track models changes made by users and save them to a table in database.

## Installation

1. Install with composer :

```
composer require cbwar/laravel-modelchanges
```

2. Open ```config/app.php``` and add the following to the ```providers``` array :

```
Cbwar\Laravel\ModelChanges\ServiceProvider::class,
```


3. After you set your database parameters in your ```.env``` file run :
```
php artisan migrate
```

## Usage

```php
<?php

namespace App;

use Cbwar\Laravel\ModelChanges\TrackedModel;

class Article extends TrackedModel
{
    protected $fillable = ['title', 'content', 'categorie_id', 'image', 'slug', 'published'];

    protected $sentences = [
        'add' => 'L\'article a été ajouté.',
        'edit' => 'L\'article a été modifié.',
        'delete' => 'L\'article a été supprimé.',
    ];

    protected $tracked = ['title', 'content', 'published', 'categorie_id'];

    [...]
}

```

## TODO
- Add restore change type (soft delete)
