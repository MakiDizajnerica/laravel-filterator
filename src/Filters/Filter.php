<?php

namespace MakiDizajnerica\Filterator\Filters;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

abstract class Filter
{
    /**
     * Filter closure.
     * 
     * @var \Closure
     */
    public Closure $closure;

    /**
     * Default filter closure.
     * 
     * @var \Closure|null
     */
    public Closure|null $default = null;

    /**
     * @param  \Closure $closure
     * @return void
     */
    protected function __construct(Closure $closure)
    {
        $this->closure = $closure;
    }

    /**
     * Get new filter instance.
     * 
     * @param  \Closure $closure
     * @return static
     */
    public static function make(Closure $closure): static
    {
        return new static($closure);
    }

    /**
     * Set default filter closure.
     * 
     * @param  \Closure $default
     * @return self
     */
    public function default(Closure $default): self
    {
        $this->default = $default;

        return $this;
    }

    /**
     * Extract value from request.
     * 
     * @param  \Illuminate\Http\Request $request
     * @param  string $param
     * @return mixed
     */
    abstract public function extractValue(Request $request, string $param): mixed;

    /**
     * Check if filter should be applied.
     * 
     * @param  mixed $value
     * @return bool
     */
    abstract public function shouldApply(mixed $value): bool;

    /**
     * Apply filter.
     * 
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  mixed $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Builder $query, mixed $value): Builder
    {
        return $this->shouldApply($value)
            ? $this->applyClosure($query, $value)
            : $this->applyDefaultClosure($query);
    }

    /**
     * Apply filter closure.
     * 
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  mixed $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function applyClosure(Builder $query, mixed $value): Builder
    {
        call_user_func_array($this->closure, [$query, $value]);

        return $query;
    }

    /**
     * Apply default filter closure.
     * 
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function applyDefaultClosure(Builder $query): Builder
    {
        if ($this->default) {
            call_user_func_array($this->default, [$query]);
        }

        return $query;
    }
}
