<?php

namespace MakiDizajnerica\Filterator\Filters;

use Illuminate\Http\Request;
use MakiDizajnerica\Filterator\Filters\Filter;

class StringFilter extends Filter
{
    /**
     * Extract value from request.
     * 
     * @param  \Illuminate\Http\Request $request
     * @param  string $param
     * @return string|null
     */
    public function extractValue(Request $request, string $param): string|null
    {
        if ($request->has($param)) {
            return $request->string($param)->trim()->toString();
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
