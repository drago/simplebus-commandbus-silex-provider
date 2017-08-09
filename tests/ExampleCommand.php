<?php
/**
 * Created by PhpStorm.
 * User: mhytry
 * Date: 04.08.2017
 * Time: 14:28
 */

namespace Mhytry\Silex\SimpleBus\CommandBus\Tests;


class ExampleCommand
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function id()
    {
        return $this->id;
    }
}