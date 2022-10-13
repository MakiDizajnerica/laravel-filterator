<?php

use Illuminate\Database\Eloquent\Builder;

if (! function_exists('filterator')) {
    /**
     * Filter model.
     *
     * @param class-string $modelClass
     * @param \Closure $closure
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    function filterator(
        $modelClass, ?Closure $closure = null, ?Builder $query = null
    ): Builder
    {
        return app('makidizajnerica-filterator')->filter($modelClass, $closure, $query);
    }
}
