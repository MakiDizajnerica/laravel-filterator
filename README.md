# Laravel Filterator

Simple search filtering manager for models.

## Installation

```bash
composer require makidizajnerica/laravel-filterator
```

## Usage

Your model needs to implement `\MakiDizajnerica\Filterator\Contracts\Filterable`. Next define `filters` method that will return filters for the model:

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
     * @return array<string, \MakiDizajnerica\Filterator\Filters\Filter>
     */
    public function filters(): array
    {
        return [
            'name' => Filter::string(fn (Builder $query, string $name) => $query->where('name', 'LIKE', "%{$name}%")),
        ];
    }
}
```

Array key will be the query param name inside the request.

There is also a couple of available filters:

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
     * @return array<string, \MakiDizajnerica\Filterator\Filters\Filter>
     */
    public function filters(): array
    {
        return [
            'is_admin' => Filter::boolean(fn (Builder $query, bool $isAdmin) => $query->where('is_admin', $isAdmin)),

            'created_at' => Filter::date(fn (Builder $query, Carbon $createdAt) => $query->whereDate('created_at', $createdAt))
                ->format('Y-m-d') // optionally define format
                ->timezone('Europe/Belgrade'), // optionally define timezone

            'credit' => Filter::float(fn (Builder $query, float $credit) => $query->where('credit', $credit))
                ->decimals(2), // optionally define number of decimals

            'age' => Filter::integer(fn (Builder $query, int $age) => $query->where('age', $age)),

            'name' => Filter::string(fn (Builder $query, string $name) => $query->where('name', 'LIKE', "%{$name}%")),
        ];
    }
}
```

You can also define default closure for the filter. Default closure gets called when query param is not set.

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
     * @return array<string, \MakiDizajnerica\Filterator\Filters\Filter>
     */
    public function filters(): array
    {
        return [
            // ...

            'created_at' => Filter::date(fn (Builder $query, Carbon $createdAt) => $query->whereDate('created_at', $createdAt))
                ->default(fn (Builder $query) => $query->whereDate('created_at', '1985-05-05')),

            // ...
        ];
    }
}
```

Next, when you want to filter your model, you can do it like so:

```php
<?php

use App\Models\User;
use MakiDizajnerica\Filterator\Facades\Filterator;

$users = filterator(User::class)->get();
// or
$users = Filterator::filter(User::class)->get();
```

Return type of the filterator method is `\Illuminate\Database\Eloquent\Builder` so you can chain other query methods.

## Adding new filters

Todo

## Author

**Nemanja Marijanovic** (<n.marijanovic@hotmail.com>) 

## Licence

Copyright © 2023, Nemanja Marijanovic <n.marijanovic@hotmail.com>

All rights reserved.

For the full copyright and license information, please view the LICENSE 
file that was distributed within the source root of this package.
