<?php

namespace Mhytry\Silex\SimpleBus\CommandBus;


use Exception;

class CommandHandlerNotFoundException extends Exception
{

    /**
     * CommandHandlerNotFoundException constructor.
     * @param string $serviceId
     */
    public function __construct($serviceId)
    {
        parent::__construct(sprintf("Service \"%s\" not found", $serviceId));
    }
}