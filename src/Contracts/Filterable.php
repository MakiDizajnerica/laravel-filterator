<?php

namespace MakiDizajnerica\Filterator\Contracts;

interface Filterable
{
    /**
     * Get filters for the filterator manager.
     *
     * @return array<string, \MakiDizajnerica\Filterator\Filter>
     */
    public function filterator(): array;
}
