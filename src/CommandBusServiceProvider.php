<?php
/**
 * Created by PhpStorm.
 * User: mhytry
 * Date: 04.08.2017
 * Time: 11:15
 */

namespace Mhytry\Silex\SimpleBus\CommandBus;


use Pimple\Container;
use Pimple\ServiceProviderInterface;
use SimpleBus\Message\Bus\Middleware\FinishesHandlingMessageBeforeHandlingNext;
use SimpleBus\Message\Bus\Middleware\MessageBusSupportingMiddleware;
use SimpleBus\Message\CallableResolver\CallableMap;
use SimpleBus\Message\CallableResolver\ServiceLocatorAwareCallableResolver;
use SimpleBus\Message\Handler\DelegatesToMessageHandlerMiddleware;
use SimpleBus\Message\Handler\Resolver\NameBasedMessageHandlerResolver;
use SimpleBus\Message\Name\ClassBasedNameResolver;

class CommandBusServiceProvider implements ServiceProviderInterface
{

    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $app A container instance
     */
    public function register(Container $app)
    {


        $app['simplebus.commandbus.command_to_handler_map'] = [];

        $app['simplebus.commandbus.command_handler_locator'] = $app->protect(function ($serviceId) use ($app) {

            if ($app->offsetExists($serviceId)) {
                return $app[$serviceId];
            }

            throw new CommandHandlerNotFoundException($serviceId);
        });


        $app['simplebus.commandbus.command_name_resolver'] = function () {

            return new ClassBasedNameResolver();
        };

        $app['simplebus.commandbus.command_handler_resolver'] = function (Container $app) {

            return new NameBasedMessageHandlerResolver(
                $app['simplebus.commandbus.command_name_resolver'],
                new CallableMap(
                    $app['simplebus.commandbus.command_to_handler_map'],
                    new ServiceLocatorAwareCallableResolver($app['simplebus.commandbus.command_handler_locator'])
                )
            );
        };

        $app['simplebus.commandbus'] = function (Container $app) {

            $commandBus = new MessageBusSupportingMiddleware();

            $commandBus->appendMiddleware(new FinishesHandlingMessageBeforeHandlingNext());
            $commandBus->appendMiddleware(
                new DelegatesToMessageHandlerMiddleware(
                    $app['simplebus.commandbus.command_handler_resolver']
                )
            );

            return $commandBus;
        };
    }

}