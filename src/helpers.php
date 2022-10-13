<?php

use Illuminate\Database\Eloquent\Builder;

if (! function_exists('filterator')) {
    /**
     * Filter model.
     *
     * @param \Illuminate\Database\Eloquent\Builder|class-string $model
     * @param \Closure $closure
     * @return \Illuminate\Database\Eloquent\Builder
     */
    function filterator($model, ?Closure $closure = null): Builder
    {
        return app('makidizajnerica-filterator')->filter($model, $closure);
    }
}
