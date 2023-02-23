<?php

use Illuminate\Database\Eloquent\Builder;

if (! function_exists('filterator')) {
    /**
     * Filter model.
     *
     * @param class-string $model
     * @param string|null $group
     * @return \Illuminate\Database\Eloquent\Builder
     */
    function filterator(string $model, ?string $group = null): Builder
    {
        return app('makidizajnerica-filterator')->filter($model, $group);
    }
}
