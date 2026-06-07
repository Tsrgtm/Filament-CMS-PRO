<?php

namespace Nepal360\FilamentCmsPro\Support;

use Illuminate\Support\Facades\Facade;

class CmsFacade extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'cms-engine';
    }
}
