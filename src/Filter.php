<?php

namespace MakiDizajnerica\Filterator;

class Filter
{
    /** @var callable */
    public $defined;

    /** @var callable */
    public $default;

    /**
     * Create a new filter.
     * 
     * @param  callable|null $defined
     * @param  callable|null $default
     * @return void
     */
    public function __construct(callable $defined = null, callable $default = null)
    {
        $this->defined = $defined;
        $this->default = $default;
    }

    /**
     * Create a new filter.
     * 
     * @param  callable|null $defined
     * @param  callable|null $default
     * @return static
     */
    public static function make(callable $defined = null, callable $default = null): static
    {
        return new static($defined, $default);
    }

    /**
     * Create a new defined filter.
     *
     * @param  callable $defined
     * @return static
     */
    public static function defined(callable $defined)
    {
        return new static($defined);
    }
}
