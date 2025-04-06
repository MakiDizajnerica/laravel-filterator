<?php

use Illuminate\Database\Eloquent\Builder;

if (! function_exists('filterator')) {
    /**
     * Filter model.
     *
     * @param  Illuminate\Database\Eloquent\Builder|class-string $model
     * @param  string|null $group
     * @return \Illuminate\Database\Eloquent\Builder
     */
    function filterator(Builder|string $model, string|null $group = null): Builder
    {
        return app('makidizajnerica-filterator')->filter($model, $group);
    }
}
