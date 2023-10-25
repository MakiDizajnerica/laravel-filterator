<?php

use Illuminate\Database\Eloquent\Builder;

if (! function_exists('filterator')) {
    /**
     * Filter model.
     *
     * @param  class-string $modelClass
     * @param  string|null $group
     * @return \Illuminate\Database\Eloquent\Builder
     */
    function filterator(string $modelClass, string|null $group = null): Builder
    {
        return app('makidizajnerica-filterator')->filter($modelClass, $group);
    }
}
