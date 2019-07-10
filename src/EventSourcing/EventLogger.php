<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    3.2.0
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0.
 * @since      3.0.0
 */

namespace Ascmvc\EventSourcing;

use Ascmvc\AbstractApp;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\EntityRepository;
use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;

/**
 * Class EventLogger
 *
 * The LightMVC Auditor class logs aggregate events from a domain model.
 *
 * @package Ascmvc\EventSourcing
 */
class EventLogger
{
    /**
     * Contains an instance of the AbstractApp object.
     *
     * @var AbstractApp
     */
    protected $app;

    /**
     * Contains an instance of EventManagerInterface.
     *
     * @var EventManagerInterface
     */
    protected $eventManager;

    /**
     * Contains an instance of EntityManager.
     *
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * Contains a string signifying the name of the entity that is to be used.
     *
     * @var string
     */
    protected $entityName;

    /**
     * Contains an instance of  EntityRepository.
     *
     * @var EntityRepository
     */
    protected $entityRepository;

    /**
     * Contains an array of logging parameters.
     *
     * @var array
     */
    protected $logParams;

    /**
     * EventLogger constructor.
     *
     * @param AbstractApp $app
     * @param EventManagerInterface $eventManager
     * @param array $logParams
     */
    public function __construct(AbstractApp $app, EventManagerInterface &$eventManager, array $logParams)
    {
        $this->app = $app;

        $this->eventManager = $eventManager;

        $this->logParams = $logParams;

        $this->initialize();
    }

    // @codeCoverageIgnoreStart
    /**
     * EventLogger destructor.
     */
    public function __destruct()
    {
        try {
            $this->entityRepository->commit();
        } catch (\Exception $e) {
            throw new \Exception('Commit Failed');
        }
    }
    // @codeCoverageIgnoreEnd

    /**
     *  Initializes the EventLogger object.
     */
    public function initialize()
    {
        if (isset($this->logParams['doctrine']) && !empty($this->logParams['doctrine'])) {
            $connName = $this->logParams['doctrine']['log_conn_name'];

            $this->entityManager = $this->app->getServiceManager()[$connName];

            $this->entityName = $this->logParams['doctrine']['entity_name'];

            $entityRepositoryName = $this->entityName . 'Repository';

            $entityRepositoryName = str_replace('Entity', 'Repository', $entityRepositoryName);

            $this->entityRepository = new $entityRepositoryName(
                $this->entityManager,
                new ClassMetadata($this->entityName)
            );
        }

        $sharedEventManager = $this->eventManager->getSharedManager();

        $sharedEventManager->attach(EventLogger::class, '*', [$this, 'logEvent'], -1);
    }

    /**
     * EventLogger listener method.
     *
     * @param EventInterface $event
     * @throws \Exception
     */
    public function logEvent(EventInterface $event)
    {
        $logFlag = true;

        if (!empty($this->logParams['log_event_type']['whitelist'])) {
            $logFlag = false;

            foreach ($this->logParams['log_event_type']['whitelist'] as $whiteListedClassName) {
                if ($event instanceof $whiteListedClassName) {
                    $logFlag = true;
                }
            }
        }

        if (!empty($this->logParams['log_event_type']['blacklist'])) {
            foreach ($this->logParams['log_event_type']['blacklist'] as $blackListedClassName) {
                if ($event instanceof $blackListedClassName) {
                    $logFlag = false;
                }
            }
        }

        if ($logFlag) {
            $eventLogArray['name'] = $event->getName();
            $eventLogArray['parameters'] = $event->getAggregateValueObject()->getProperties();
            $eventLogArray['occurred'] = 'now';

            try {
                $this->entityRepository->commitDeferred($eventLogArray);
            } catch (\Exception $e) {
                throw new \Exception('Commit Deferred Failed');
            }
        }
    }
}
