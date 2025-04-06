<?php

use Illuminate\Contracts\Database\Eloquent\Builder;

if (! function_exists('filterator')) {
    /**
     * Filter model.
     *
     * @param  \Illuminate\Contracts\Database\Eloquent\Builder|class-string $model
     * @param  string|null $group
     * @return \Illuminate\Contracts\Database\Eloquent\Builder
     */
    function filterator(Builder|string $model, string|null $group = null): Builder
    {
        return app('makidizajnerica-filterator')->filter($model, $group);
    }
}
