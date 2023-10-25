<?php

namespace MakiDizajnerica\Filterator;

use InvalidArgumentException;
use MakiDizajnerica\Filterator\Filters\DateFilter;
use MakiDizajnerica\Filterator\Filters\FloatFilter;
use MakiDizajnerica\Filterator\Filters\StringFilter;
use MakiDizajnerica\Filterator\Filters\BooleanFilter;
use MakiDizajnerica\Filterator\Filters\IntegerFilter;
use MakiDizajnerica\Filterator\Filters\Filter as AbstractFilter;

final class Filter
{
    /**
     * @var array
     */
    protected static array $filters = [
        'boolean' => BooleanFilter::class,
        'date' => DateFilter::class,
        'float' => FloatFilter::class,
        'integer' => IntegerFilter::class,
        'string' => StringFilter::class,
    ];

    /**
     * Register new filter.
     * 
     * @param  string $name
     * @param  class-string $filter
     * @param  bool $overwrite
     * @return void
     * 
     * @throws \InvalidArgumentException
     */
    public static function register(string $name, string $filter, bool $overwrite = false): void
    {
        if (! is_subclass_of($filter, AbstractFilter::class)) {
            throw new InvalidArgumentException(
                sprintf('Filter class [%s] must be instance of [%s].', $filter, AbstractFilter::class)
            );
        }

        if (isset(static::$filters[$name]) && ! $overwrite) {
            throw new InvalidArgumentException(sprintf('Filter [%s] already defined.', $name));
        }

        static::$filters[$name] = $filter;
    }

    /**
     * Get filter instance.
     * 
     * @param  string $name
     * @param  array $arguments
     * @return \MakiDizajnerica\Filterator\Filters\Filter
     * 
     * @throws \InvalidArgumentException
     */
    public static function __callStatic(string $name, array $arguments): AbstractFilter
    {
        if (! in_array($name, array_keys(static::$filters))) {
            throw new InvalidArgumentException(sprintf('Filter [%s] not defined.', $name));
        }

        $filter = static::$filters[$name];

        return $filter::make(...$arguments);
    }
}
