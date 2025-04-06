<?php

namespace MakiDizajnerica\Filterator;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use MakiDizajnerica\Filterator\Filters\Filter;
use MakiDizajnerica\Filterator\Contracts\Filterable;

final class FilteratorManager
{
    /**
     * @var \Illuminate\Http\Request
     */
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
     * @param  Illuminate\Database\Eloquent\Builder|class-string $model
     * @param  string|null $group
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function filter(Builder|string $model, string|null $group = null): Builder
    {
        $query = is_string($model)
            ? $this->getQueryBuilderForModel($model)
            : $model;

        $filters = $this->getFiltersForModel($query->getModel(), $group);

        $this->applyFilters($filters, $query);

        return $query;
    }

    /**
     * Get query builder for the model.
     * 
     * @param  class-string $modelClass
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function getQueryBuilderForModel(string $modelClass): Builder
    {
        if (! is_subclass_of($modelClass, Model::class)) {
            throw new InvalidArgumentException(
                sprintf('Model class [%s] must be instance of [%s].', $modelClass, Model::class)
            );
        }

        return $modelClass::query();
    }

    /**
     * Get filters for the model.
     * 
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @param  string|null $group
     * @return array<string, \MakiDizajnerica\Filterator\Filters\Filter>
     * 
     * @throws \InvalidArgumentException
     */
    protected function getFiltersForModel(Model $model, ?string $group): array
    {
        if (! is_subclass_of($model, Filterable::class)) {
            throw new InvalidArgumentException(
                sprintf('Model class [%s] must implement [%s] interface.', get_class($model), Filterable::class)
            );
        }

        $filters = $model->filters();

        if ($group) {
            $groupFilters = Arr::get($filters, $group, []);

            $filters = is_array($groupFilters)
                ? $groupFilters
                : [];
        }

        return array_filter($filters, fn ($filter) => is_subclass_of($filter, Filter::class));
    }

    /**
     * Apply filters to the query.
     * 
     * @param  array<string, \MakiDizajnerica\Filterator\Filters\Filter> $filters
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return void
     */
    protected function applyFilters(array $filters, Builder $query): void
    {
        foreach ($filters as $param => $filter) {
            $filter->apply(
                $query, $filter->extractValue($this->request, $param)
            );
        }
    }
}
