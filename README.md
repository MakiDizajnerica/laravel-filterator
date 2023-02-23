# Laravel Filterator

Simple search filtering manager for models.

## Installation

```bash
composer require makidizajnerica/laravel-filterator
```

## Usage

Your model needs to implement `\MakiDizajnerica\Filterator\Contracts\Filterable`. Next define `filterator` method that will return filters for the model:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

use MakiDizajnerica\Filterator\Filter;
use MakiDizajnerica\Filterator\Contracts\Filterable as FilterableContract;

class User extends Model implements FilterableContract
{
    // ...

    /**
     * Get filters for the filterator manager.
     *
     * @return array<string, \MakiDizajnerica\Filterator\Filter>
     */
    public function filterator(): array
    {
        return [
            'name' => Filter::defined(fn (Builder $query, $value) => $query->where('name', 'LIKE', "%{$value}%")),
        ];
    }
}
```

Array key will be the query param name inside the request.

You can also define the type of param:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

use MakiDizajnerica\Filterator\Filter;
use MakiDizajnerica\Filterator\Contracts\Filterable as FilterableContract;

class User extends Model implements FilterableContract
{
    // ...

    /**
     * Get filters for the filterator manager.
     *
     * @return array<string, \MakiDizajnerica\Filterator\Filter>
     */
    public function filterator(): array
    {
        return [
            'name:string' => Filter::defined(fn (Builder $query, string $value) => $query->where('name', 'LIKE', "%{$value}%")),
        ];
    }
}
```

Available types:

| Type    | Definition                           | Example                              |
|:------- |:------------------------------------ |:------------------------------------ |
| string  | '{param}:string'                     | 'name:string'                        |
| integer | '{param}:integer'                    | 'count:integer'                      |
| float   | '{param}:float,{?decimals}'          | 'price:float,2'                      |
| boolean | '{param}:boolean'                    | 'active:boolean'                     |
| date    | '{param}:date,{?format},{?timezone}' | 'born_at:date,Y-m-d,Europe/Belgrade' |

```php
<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

use MakiDizajnerica\Filterator\Filter;
use MakiDizajnerica\Filterator\Contracts\Filterable as FilterableContract;

class User extends Model implements FilterableContract
{
    // ...

    /**
     * Get filters for the filterator manager.
     *
     * @return array<string, \MakiDizajnerica\Filterator\Filter>
     */
    public function filterator(): array
    {
        return [
            'name:string' => Filter::defined(fn (Builder $query, string $name) => $query->where('name', 'LIKE', "%{$name}%")),
            'email' => Filter::defined(fn (Builder $query, $email) => /* ... */),
            'born_at:date,Y-m-d,Europe/Belgrade' => Filter::defined(fn (Builder $query, Carbon $date) => /* ... */),
        ];
    }
}
```

You can also define default closure on the filter by calling `\MakiDizajnerica\Filterator\Filter::make()` method and passing second argument. First argument will represent defined closure, the one that gets called if query param is set. Other one is default, the one that gets called when query param is not set.

```php
<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

use MakiDizajnerica\Filterator\Filter;
use MakiDizajnerica\Filterator\Contracts\Filterable as FilterableContract;

class User extends Model implements FilterableContract
{
    // ...

    /**
     * Get filters for the filterator manager.
     *
     * @return array<string, \MakiDizajnerica\Filterator\Filter>
     */
    public function filterator(): array
    {
        return [
            // ...
            'born_at:date,Y-m-d,Europe/Belgrade' => Filter::make(
                fn (Builder $query, Carbon $date) => $query->whereDate('born_at', $date), // defined
                fn (Builder $query) => $query->whereDate('born_at', '1985-05-05') // default
            ),
        ];
    }
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
    }

    // ...
}
```

Return type of the filterator method is `\Illuminate\Database\Eloquent\Builder` so you can chain other query methods.

## Author

**Nemanja Marijanovic** (<n.marijanovic@hotmail.com>) 

## Licence

Copyright © 2021, Nemanja Marijanovic <n.marijanovic@hotmail.com>

All rights reserved.

For the full copyright and license information, please view the LICENSE 
file that was distributed within the source root of this package.
