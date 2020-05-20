<?php

namespace Application\Controllers;

use Ascmvc\AscmvcControllerFactoryInterface;
use Ascmvc\EventSourcing\AggregateImmutableValueObject;
use Ascmvc\EventSourcing\Event\AggregateEvent;
use Ascmvc\EventSourcing\Event\Event;
use Ascmvc\EventSourcing\EventDispatcher;
use Ascmvc\Mvc\AscmvcEvent;
use Ascmvc\Mvc\Controller;
use Pimple\Container;
use Laminas\Diactoros\Response;

class FakeeventController extends Controller implements AscmvcControllerFactoryInterface
{
    public static function factory(array &$baseConfig, EventDispatcher &$eventDispatcher, Container &$serviceManager, &$viewObject)
    {
        // Setting the identifiers of this Event Dispatcher (event bus).
        // Subscribing this controller (Aggregate Root).
        $eventDispatcher->setIdentifiers(
            [
                FakeeventController::class,
            ]
        );

        $controller = new FakeeventController($baseConfig, $eventDispatcher);

        $sharedEventManager = $eventDispatcher->getSharedManager();

        // Attaching this controller's listener method to the shared event manager's
        // corresponding identifier (see above).
        $sharedEventManager->attach(
            FakeeventController::class,
            '*',
            [$controller, 'updatePostActionControllerOutput']
        );

        return $controller;
    }

    public function indexAction($vars = null)
    {
        $aggregateValue = new AggregateImmutableValueObject(['testkey' => 'testaddedvalue']);

        $event = new AggregateEvent($aggregateValue, FakeeventController::class, 'testevent');

        $this->eventDispatcher->dispatch($event);

        $this->view['templatefile'] = 'fakeevent_index';

        $this->view['results'] = 'FakeeventControllerSTDOUT';

        return $this->view;
    }

    /**
     * Updates the Controller's output at the dispatch event if needed (listener method).
     *
     * @param Event $event
     */
    public function updatePostActionControllerOutput(Event $event)
    {
        $app = $event->getApplication();

        $eventOutput = $event->getAggregateValueObject()->getProperties();

        $controllerOutput = $app->getControllerOutput();

        if (is_null($controllerOutput)) {
            $controllerOutput = $eventOutput;
        } else {
            $controllerOutput = array_merge($eventOutput, $controllerOutput);
        }

        $app->setControllerOutput($controllerOutput);
    }
}