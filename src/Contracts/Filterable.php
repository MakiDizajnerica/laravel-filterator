<?php

namespace MakiDizajnerica\Filterator\Contracts;

interface Filterable
{
    /**
     * Get filters for the filterator manager.
     *
     * @return array<string, Closure>
     */
    public static function filterator(): array;
}
