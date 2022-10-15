<?php

if (! function_exists('filterator')) {
    /**
     * Filter model.
     *
     * @param \Illuminate\Database\Eloquent\Builder|class-string $model
     * @param \Closure $closure
     * @param string $group
     * @return mixed
     */
    function filterator($model, ?Closure $closure = null, $group = null)
    {
        return app('makidizajnerica-filterator')->filter($model, $closure, $group);
    }
}
