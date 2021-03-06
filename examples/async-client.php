<?php

use Gearman\Async\ClientInterface;
use Gearman\Async\Event\TaskDataEvent;
use Gearman\Async\TaskInterface;
use Gearman\Async\Factory;

require_once __DIR__ . "/../vendor/autoload.php";

// use default options
$factory = new Factory();

$factory->createClient("127.0.0.1", 4730)->then(
    // on successful creation
    function (ClientInterface $client) {
        $client->submit("reverse", "Hallo Welt!")->then(function(TaskInterface $task) {
            printf("Submitted: %s with \"%s\" [handle: %s]\n", 
                $task->getFunction(), 
                $task->getWorkload(), 
                $task->getHandle()
            );
            
            $task->on('complete', function (TaskDataEvent $event, ClientInterface $client) {
                echo "Result: {$event->getData()}\n";
                $client->disconnect();
            });
        });
    },
    // error-handler
    function($error) {
        echo "Error: $error\n";
    }
);

$factory->getEventLoop()->run();

