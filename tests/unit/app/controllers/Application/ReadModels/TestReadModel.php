<?php

namespace Application\ReadModels;

use Ascmvc\EventSourcing\AggregateImmutableValueObject;
use Ascmvc\EventSourcing\AggregateReadModel;
use Ascmvc\EventSourcing\Event\AggregateEvent;
use Ascmvc\EventSourcing\EventDispatcher;

class TestReadModel extends AggregateReadModel
{
    protected $count;

    protected function __construct(EventDispatcher $eventDispatcher)
    {
        parent::__construct($eventDispatcher);
    }

    public static function getInstance(EventDispatcher $eventDispatcher)
    {
        return new self($eventDispatcher);
    }

    public function __invoke(AggregateEvent $event)
    {
        if (is_null($this->count)) {
            $this->count = 1;
        }

        while ($this->count < 4) {
            $this->count++;
            yield true;
        }

        $aggregateValueObject = new AggregateImmutableValueObject(['testreadmodel' => 'fromreadmodel']);

        $aggregateEvent = new AggregateEvent($aggregateValueObject, $this->aggregateRootName, 'testreadmodelevent');

        $this->eventDispatcher->dispatch($aggregateEvent);

        return $this->count;
    }
}