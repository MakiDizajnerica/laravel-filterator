<?php

namespace MakiDizajnerica\Filterator\Filters;

use Illuminate\Http\Request;
use MakiDizajnerica\Filterator\Filters\Filter;

final class IntegerFilter extends Filter
{
    /**
     * Extract value from request.
     * 
     * @param  \Illuminate\Http\Request $request
     * @param  string $param
     * @return int|null
     */
    public function extractValue(Request $request, string $param): int|null
    {
        if ($request->has($param)) {
            return intval($request->query($param));
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
        return is_int($value);
    }
}
