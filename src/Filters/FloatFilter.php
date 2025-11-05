<?php

namespace MakiDizajnerica\Filterator\Filters;

use Illuminate\Http\Request;
use MakiDizajnerica\Filterator\Filters\Filter;

class FloatFilter extends Filter
{
    /**
     * @var int
     */
    protected int $decimals = 2;

    /**
     * Set decimals.
     * 
     * @param  int $decimals
     * @return self
     */
    public function decimals(int $decimals): self
    {
        $this->decimals = abs($decimals);

        return $this;
    }

    /**
     * Extract value from request.
     * 
     * @param  \Illuminate\Http\Request $request
     * @param  string $param
     * @return float|null
     */
    public function extractValue(Request $request, string $param): float|null
    {
        if ($request->has($param)) {
            return round($request->float($param), $this->decimals);
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
        return is_float($value);
    }
}
