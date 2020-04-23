<?php

namespace Stijlgenoten\CheckMyCamels\Facades;

use Illuminate\Support\Facades\Facade;

class CheckMyCamels extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'CheckMyCamels';
    }
}
