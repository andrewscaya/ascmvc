<?php

namespace Application\Controllers;

use Application\ReadModels\TestarraylistenersReadModel;
use Application\ReadModels\TestReadModel;
use Ascmvc\EventSourcing\AggregateEventListenerInterface;
use Ascmvc\EventSourcing\AggregateImmutableValueObject;
use Ascmvc\EventSourcing\AggregateRootController;
use Ascmvc\EventSourcing\Event\AggregateEvent;
use Ascmvc\EventSourcing\Event\Event;

class FakeaggregatearraylistenersController extends AggregateRootController implements AggregateEventListenerInterface
{
    // Define the Aggregate's invokable listeners.
    protected $aggregateListenerNames = [
        ['testevent' => TestReadModel::class],
        ['testevent' => TestarraylistenersReadModel::class],
    ];

    public function onAggregateEvent(AggregateEvent $event)
    {
        $eventName = $event->getName();

        if ($eventName === 'testevent' || $eventName === 'testreadmodelevent'){
            $this->values[] = $event->getAggregateValueObject()->getProperties();
        }
    }

    public function onEvent(Event $event)
    {
        return;
    }

    public function preIndexAction($vars = null)
    {
        $aggregateValue = new AggregateImmutableValueObject(['testkey' => 'testaddedvalue']);

        $event = new AggregateEvent($aggregateValue, FakeaggregatearraylistenersController::class, 'testevent');

        $this->eventDispatcher->dispatch($event);

        $this->view['preindex'] = 'PreIndexData';

        return $this->view;
    }

    public function indexAction($vars = null)
    {
        $this->view['results'] = 'FakeaggregatearraylistenersControllerSTDOUT';

        $this->view['values'] = $this->values;

        $this->view['templatefile'] = 'fakeaggregatearraylisteners_index';

        return $this->view;
    }
}