# Laravel Filterator

Simple search filtering manager for models.

## Installation

```bash
composer require makidizajnerica/laravel-filterator
```

## Usage

Your model needs to implement `MakiDizajnerica\Filterator\Contracts\Filterable`. Next define `filterator` method that will return filters for the model:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use MakiDizajnerica\Filterator\Contracts\Filterable as FilterableContract;

class User extends Model implements FilterableContract
{
    /**
     * Get filters for the filterator manager.
     *
     * @return array<string, Closure>
     */
    public function filterator(): array
    {
        return [
            'name' => fn (Builder $query, $value) => $query->where('name', 'LIKE', "%{$value}%"),
        ];
    }

    // ...
}
```

Array key will be the query param name inside the request.

You can also define the type of param:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use MakiDizajnerica\Filterator\Contracts\Filterable as FilterableContract;

class User extends Model implements FilterableContract
{
    /**
     * Get filters for the filterator manager.
     *
     * @return array<string, Closure>
     */
    public function filterator(): array
    {
        return [
            'name:string' => fn (Builder $query, $value) => $query->where('name', 'LIKE', "%{$value}%"),
        ];
    }

    // ...
}
```

Available types:

| Type    | Definition                         | Example                              |
|:------- |:---------------------------------- |:------------------------------------ |
| string  | '{param}:string'                   | 'name:string'                        |
| integer | '{param}:integer'                  | 'count:integer'                      |
| float   | '{param}:float,{decimals}'         | 'price:float,2'                      |
| boolean | '{param}:boolean'                  | 'active:boolean'                     |
| date    | '{param}:date,{format},{timezone}' | 'born_at:date,Y-m-d,Europe/Belgrade' |

Filter closure also has a third argument `$queryParams` that contains values of all params.

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use MakiDizajnerica\Filterator\Contracts\Filterable as FilterableContract;

class User extends Model implements FilterableContract
{
    /**
     * Get filters for the filterator manager.
     *
     * @return array<string, Closure>
     */
    public function filterator(): array
    {
        return [
            'name:string' => fn (Builder $query, $value, array $queryParams) => $query->where('name', 'LIKE', "%{$value}%"),
            'email' => fn (Builder $query, $value) => /* ... */,
            'born_at:date,Y-m-d,Europe/Belgrade' => fn (Builder $query, $value) => /* ... */,
        ];
    }

    // ...
}
```

Next inside your controller you can filter your model like so:

```php
<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Controllers\Controller;
use MakiDizajnerica\Filterator\Facades\Filterator;

class UserController extends Controller
{
    public function index()
    {
        $users = filterator(User::class)->get();
        // or
        $users = Filterator::filter(User::class)->get();

        // You can also pass Builder instance.
        $users = filterator(User::query())->get();
    }

    // ...
}
```

Or you can pass closure to the `filterator` as a second argument like so:

```php
<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use MakiDizajnerica\Filterator\Facades\Filterator;

class UserController extends Controller
{
    public function index()
    {
        $users = filterator(
            User::class,
            function (Builder $query, array $queryParams) {
                $query->when($queryParams['email'], function ($query, $email) {
                    $query->where('email', $email);
                });

                //
            }
        )->get();
        // or
        $users = Filterator::filter(User::class, /* closure */)->get();
    }

    // ...
}
```

When closure is passed, other closures defined inside models `filterator` method will not be called, but the `$queryParams` argument will have all defined params.

Return type of the filterator method is `Illuminate\Database\Eloquent\Builder` so you can chain other query methods.

## Author

**Nemanja Marijanovic** (<n.marijanovic@hotmail.com>) 

## Licence

Copyright © 2021, Nemanja Marijanovic <n.marijanovic@hotmail.com>

All rights reserved.

For the full copyright and license information, please view the LICENSE 
file that was distributed within the source root of this package.
