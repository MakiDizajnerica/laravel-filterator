<?php

namespace MakiDizajnerica\Filterator;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use InvalidArgumentException;
use MakiDizajnerica\Filterator\Filter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use MakiDizajnerica\Filterator\Contracts\Filterable;

class FilteratorManager
{
    /** @var \Illuminate\Http\Request */
    protected $request;

    /**
     * @param  \Illuminate\Http\Request $request
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Filter model.
     *
     * @param  class-string $model
     * @param  string|null $group
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function filter(string $model, ?string $group = null): Builder
    {
        $query = $this->getQueryBuilderForModel($model);

        $filters = $this->getFiltersForModel($query->getModel(), $group);
        $filtersKeys = $this->extractFiltersKeys($filters);
        $filtersKeysFlattened = $this->flattenFiltersKeys($filtersKeys);

        // We are combining flattened keys, keys without
        // the defined type and actual filter instance.
        $filters = array_combine($filtersKeysFlattened, $filters);

        $params = $this->getQueryParams($filtersKeys);

        foreach ($filters as $key => $filter) {
            $value = $params[$key];

            list('defined' => $defined, 'default' => $default) = get_object_vars($filter);

            if (is_null($value)) {
                if ($default) {
                    call_user_func_array($default, [$query]);
                }
            } else {
                if ($defined) {
                    call_user_func_array($defined, [$query, $value, $params]);
                }
            }
        }

        return $query;
    }

    /**
     * Get query builder for the model.
     *
     * @param  class-string $model
     * @return \Illuminate\Database\Eloquent\Builder
     * 
     * @throws \InvalidArgumentException
     */
    protected function getQueryBuilderForModel(string $model): Builder
    {
        if (! class_exists($model)) {
            throw new InvalidArgumentException("{$model} does not exist.");
        }

        return $model::query();
    }

    /**
     * Get filters for the model.
     * 
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @param  string|null $group
     * @return array<string, \MakiDizajnerica\Filterator\Filter>
     * 
     * @throws \InvalidArgumentException
     */
    protected function getFiltersForModel(Model $model, ?string $group): array
    {
        if (! ($model instanceof Filterable)) {
            $modelClass = get_class($model);

            throw new InvalidArgumentException(
                "{$modelClass} must implement MakiDizajnerica\Filterator\Contracts\Filterable interface."
            );
        }

        $filters = $model->filterator();

        if ($group) {
            $groupFilters = Arr::get($filters, $group, []);

            if (is_array($groupFilters)) {
                return $groupFilters;
            }
        }

        return array_filter($filters, fn ($filter) => $filter instanceof Filter);
    }

    /**
     * Extract filters keys.
     * 
     * @param  array $filters
     * @return array<int, array>
     */
    protected function extractFiltersKeys(array $filters): array
    {
        return array_map(
            function ($key) {
                return array_pad(explode(':', $key, 2), 2, null);
            },
            array_keys($filters)
        );
    }

    /**
     * Flatten filters keys.
     * 
     * @param  array $keys
     * @return array<int, string>
     */
    protected function flattenFiltersKeys(array $keys): array
    {
        // We are going to flatten filters keys to get single dimensional
        // array containing only name of the keys.
        return array_map(fn ($key) => head($key), $keys);
    }

    /**
     * Get query params from the request.
     * 
     * @param  array $keys
     * @return array<string, mixed>
     */
    protected function getQueryParams(array $keys): array
    {
        $params = [];

        foreach ($keys as $key) {
            [$name, $type] = $key;
            [$type, $param1, $param2] = array_pad(explode(',', $type, 3), 3, null);

            $params[$name] = $this->request->has($name)
                ? match ($type) {
                    'string' => $this->request->string($name)->trim()->toString(),
                    'integer' => intval($this->request->query($name)),
                    'float' => number_format(floatval($this->request->query($name)), $param1),
                    'boolean' => $this->request->boolean($name),
                    'date' => $this->request->date($name, $param1, $param2),
                    default => $this->request->query($name),
                }
                : null;
        }

        return $params;
    }
}
