<?php

if (! function_exists('filterator')) {
    /**
     * Filter model.
     *
     * @param class-string $modelClass
     * @param \Closure $closure
     * @return \Illuminate\Database\Eloquent\Builder
     */
    function filterator($modelClass, ?Closure $closure = null)
    {
        return app('makidizajnerica-filterator')->filter($modelClass, $closure);
    }
}
