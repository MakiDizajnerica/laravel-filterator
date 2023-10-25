<?php

namespace MakiDizajnerica\Filterator\Contracts;

interface Filterable
{
    /**
     * Get filters for the filterator manager.
     *
     * @return array<string, \MakiDizajnerica\Filterator\Filters\Filter>
     */
    public function filters(): array;
}
