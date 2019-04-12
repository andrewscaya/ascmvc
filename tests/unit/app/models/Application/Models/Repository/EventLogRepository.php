<?php

namespace Application\Models\Repository;

use Application\Models\Entity\EventLog;
use Doctrine\ORM\EntityRepository;

class EventLogRepository extends EntityRepository
{
    protected $eventLog;

    public function commitDeferred(array $eventLogArray, EventLog $eventLog = null)
    {
        throw new \Exception('Commit Deferred Called');
    }

    public function commit()
    {
        return;
    }
}
