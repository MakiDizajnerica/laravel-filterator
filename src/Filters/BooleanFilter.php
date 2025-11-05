<?php

namespace MakiDizajnerica\Filterator\Filters;

use Illuminate\Http\Request;
use MakiDizajnerica\Filterator\Filters\Filter;

class BooleanFilter extends Filter
{
    /**
     * Extract value from request.
     * 
     * @param  \Illuminate\Http\Request $request
     * @param  string $param
     * @return bool|null
     */
    public function extractValue(Request $request, string $param): bool|null
    {
        if ($request->has($param)) {
            return $request->boolean($param);
        }

        return null;
    }

    /**
     * Check if filter should be applied.
     * 
     * @param  mixed $value
     * @return bool
     */
    public function shouldApply(mixed $value): bool
    {
        return is_bool($value);
    }
}
