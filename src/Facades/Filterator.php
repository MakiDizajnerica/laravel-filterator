<?php

namespace MakiDizajnerica\Filterator\Facades;

use Illuminate\Support\Facades\Facade;

class Filterator extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'makidizajnerica-filterator';
    }
}
