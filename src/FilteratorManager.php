<?php

namespace MakiDizajnerica\Filterator;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Illuminate\Database\Eloquent\Model;
use MakiDizajnerica\Filterator\Contracts\Filterable;

class FilteratorManager
{
    /** @var \Illuminate\Http\Request */
    protected $request;

    /**
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Filter model.
     *
     * @param \Illuminate\Database\Eloquent\Builder|class-string $model
     * @param \Closure $closure
     * @param string $group
     * @return mixed
     */
    public function filter($model, ?Closure $closure = null, $group = null)
    {
        $query = $this->getQueryBuilderFromModel($model);

        $unsortedFilters = $this->getFiltersFromModel($query->getModel(), $group);
        $unsortedFiltersKeys = $this->getKeysFromFilters($unsortedFilters);

        $params = $this->getQueryParams($unsortedFiltersKeys);

        if ($closure) {
            call_user_func_array($closure, [$query, $params]);

            return $query;
        }

        // We are combining flattened keys, keys without
        // the defined type and closures.
        $filters = array_combine(
            $this->flattenFiltersKeys($unsortedFiltersKeys),
            $unsortedFilters
        );

        foreach ($filters as $key => $closure) {
            $value = $params[$key];

            if (! blank($value) && $closure instanceof Closure) {
                call_user_func_array($closure, [$query, $value, $params]);
            }
        }

        return $query;
    }

    /**
     * Get model query for the model.
     *
     * @param \Illuminate\Database\Eloquent\Builder|class-string $model
     * @return \Illuminate\Database\Eloquent\Builder
     * 
     * @throws \InvalidArgumentException
     */
    protected function getQueryBuilderFromModel($model)
    {
        if (is_string($model) && ! class_exists($model)) {
            throw new InvalidArgumentException("{$model} does not exist.");
        }

        return is_string($model) ? $model::query() : $model;
    }

    /**
     * Get filters for the model.
     * 
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $group
     * @return array<string, Closure>
     * 
     * @throws \InvalidArgumentException
     */
    protected function getFiltersFromModel(Model $model, $group): array
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

        return $filters;
    }

    /**
     * Extract keys from the filters.
     * 
     * @param array $filters
     * @return array<int, array>
     */
    protected function getKeysFromFilters(array $filters): array
    {
        $keys = [];

        // We are going to explode the key for each filter
        // in order to get the name and type, if it is defined.
        foreach ($filters as $key => $closure) {
            array_push($keys, array_pad(
                explode(':', is_numeric($key) ? $closure : $key, 2),
                2,
                null
            ));
        }

        return $keys;
    }

    /**
     * Flatten filters keys.
     * 
     * @param array $filtersKeys
     * @return array<int, string>
     */
    protected function flattenFiltersKeys(array $filtersKeys): array
    {
        // We are going to flatten filters keys to get single dimensional
        // array containing only name of the keys.
        return array_map(fn ($key) => head($key), $filtersKeys);
    }

    /**
     * Get query params from the request.
     * 
     * @param array $filtersKeys
     * @return array<string, mixed>
     */
    protected function getQueryParams(array $filtersKeys): array
    {
        $params = [];

        foreach ($filtersKeys as $key) {
            [$name, $type] = $key;

            $params[$name] = $this->getQueryParamByType($name, $type);
        }

        return $params;
    }

    /**
     * Get query param by type.
     * 
     * @param string $name
     * @param string $type
     * @return mixed
     */
    protected function getQueryParamByType($name, $type)
    {
        switch ($type) {
            case 'string': return $this->request->string($name)->trim()->toString();
            case 'integer': return intval($this->request->string($name)->trim()->toString());
            case 'boolean': return $this->request->boolean($name);
            case null: return $this->request->query($name);
            default:
                if (Str::startsWith($type, 'float')) {
                    [$type, $decimals] = array_pad(explode(',', $type, 2), 2, 2);
        
                    return number_format(
                        floatval($this->request->string($name)->trim()->toString()),
                        intval($decimals)
                    );
                }

                if (Str::startsWith($type, 'date')) {
                    [$type, $format, $timezone] = array_pad(explode(',', $type, 3), 3, null);
        
                    return $this->request->date($name, $format, $timezone);
                }

                return $this->request->query($name);
        }
    }
}
