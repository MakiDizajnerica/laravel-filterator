<?php

namespace MakiDizajnerica\Filterator\Filters;

use Illuminate\Http\Request;
use MakiDizajnerica\Filterator\Filters\Filter;

class ArrayFilter extends Filter
{
    /**
     * Extract value from request.
     * 
     * @param  \Illuminate\Http\Request $request
     * @param  string $param
     * @return array|null
     */
    public function extractValue(Request $request, string $param): array|null
    {
        if ($request->has($param)) {
            return $request->input($param);
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
        return ! empty($value);
    }
}
