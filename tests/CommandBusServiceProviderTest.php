<?php

namespace Mhytry\Silex\SimpleBus\CommandBus\Tests;

use Mhytry\Silex\SimpleBus\CommandBus\CommandBusServiceProvider;
use Mhytry\Silex\SimpleBus\CommandBus\CommandHandlerNotFoundException;
use PHPUnit\Framework\TestCase;
use Silex\Application;
use SimpleBus\Message\Bus\Middleware\MessageBusSupportingMiddleware;

class CommandBusServiceProviderTest extends TestCase
{

    private $app;

    public function setUp()
    {

        $this->app = new Application();
        $this->app->register(new CommandBusServiceProvider());
    }

    public function testCommandBusDeclaration()
    {
        $this->assertInstanceOf(MessageBusSupportingMiddleware::class, $this->app['simplebus.commandbus']);
    }

    public function testDefaultCommandHandlerLocator()
    {

        $definedService = 'app.command_handlers.example_command_handler';
        $notDefinedService = 'not-defined-service';

        $commandHandler = new ExampleCommandHandler();

        $this->setAsAService($definedService, $commandHandler);

        $this->assertEquals($commandHandler,
            $this->app['simplebus.commandbus.command_handler_locator']($definedService));

        $this->expectException(CommandHandlerNotFoundException::class);
        $this->app['simplebus.commandbus.command_handler_locator']($notDefinedService);

    }

    public function testCommandBusFlow()
    {

        $exampleCommand = new ExampleCommand(1);
        $exampleCommandHandlerMock = $this->getMockBuilder(ExampleCommandHandler::class)->getMock();

        $definedService = 'app.command_handlers.example_command_handler';

        $this->setAsAService($definedService, $exampleCommandHandlerMock);

        $this->app['simplebus.commandbus.command_to_handler_map'] = [
            ExampleCommand::class => $definedService
        ];

        $exampleCommandHandlerMock->expects($this->once())
            ->method('__invoke')
            ->with($this->equalTo($exampleCommand));

        $this->app['simplebus.commandbus']->handle($exampleCommand);

    }

    private function setAsAService($serviceId, $service)
    {
        $this->app[$serviceId] = function () use ($service) {
            return $service;
        };
    }

}
