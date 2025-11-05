<?php

namespace MakiDizajnerica\Filterator\Filters;

use Throwable;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use MakiDizajnerica\Filterator\Filters\Filter;

class DateFilter extends Filter
{
    /**
     * @var string|null
     */
    protected string|null $format = null;

    /**
     * @var string|null
     */
    protected string|null $timezone = null;

    /**
     * Set date format.
     * 
     * @param  string $format
     * @return self
     */
    public function format(string $format): self
    {
        $this->format = $format;

        return $this;
    }

    /**
     * Set timezone.
     * 
     * @param  string $timezone
     * @return self
     */
    public function timezone(string $timezone): self
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * Extract value from request.
     * 
     * @param  \Illuminate\Http\Request $request
     * @param  string $param
     * @return \Illuminate\Support\Carbon|null
     */
    public function extractValue(Request $request, string $param): Carbon|null
    {
        if ($request->has($param)) {
            try {
                return $request->date($param, $this->format, $this->timezone);
            } catch (Throwable $e) {
                return null;
            }
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
