<?php

namespace Ammly\Jengahq\Facades;

use Illuminate\Support\Facades\Facade;

class Jengahq extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'jengahq';
    }
}
