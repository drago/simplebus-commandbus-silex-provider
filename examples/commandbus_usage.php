<?php

use Mhytry\Silex\SimpleBus\CommandBus\CommandBusServiceProvider;
use Silex\Application;

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Application();
$app->register(new CommandBusServiceProvider(), [
    'simplebus.commandbus.command_to_handler_map' => [
        AddCommentCommand::class => new AddCommentCommandHandler()
    ]
]);


class AddCommentCommand
{

    private $author;

    private $content;

    private $resource;

    public function __construct($author, $content, $resource)
    {
        $this->author = $author;
        $this->content = $content;
        $this->resource = $resource;
    }

    public function author()
    {
        return $this->author;
    }

    public function content()
    {
        return $this->content;
    }

    public function resource()
    {
        return $this->resource;
    }

}

class AddCommentCommandHandler
{

    public function __invoke(AddCommentCommand $command)
    {
        echo implode(PHP_EOL, [
            $command->author(),
            $command->content(),
            $command->resource()
        ]);
    }

}


$app['simplebus.commandbus']->handle(new AddCommentCommand("Michał Hytry", "Lorem ipsum...", 2));
