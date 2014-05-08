<?php namespace AnasT\Caracal\Facades;

use Illuminate\Support\Facades\Facade;

class Caracal extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'caracal';
    }

}
