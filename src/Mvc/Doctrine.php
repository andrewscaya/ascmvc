<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    4.0.0
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0.
 * @since      1.0.0
 */

namespace Ascmvc\Mvc;

use Ascmvc\AbstractModelObject;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationException;

/**
 * Class Doctrine
 *
 * The Doctrine class extends the AbstractModelObject and uses the doctrine/dbal and doctrine/orm libraries.
 */
class Doctrine extends AbstractModelObject
{
    // @codeCoverageIgnoreStart
    /**
     * Doctrine constructor.
     */
    protected function __construct()
    {
    }

    /**
     * Doctrine clone method.
     */
    protected function __clone()
    {
    }
    // @codeCoverageIgnoreEnd

    /**
     * Gets a Singleton instance of the Doctrine class.
     *
     * @param string $connType
     * @param string $connName
     * @param array $params
     *
     * @return Connection|EntityManager|bool
     *
     * @throws DBALException
     * @throws ORMException
     * @throws AnnotationException
     */
    public static function getInstance($connType, $connName, Array $params)
    {
        if (empty($connType) || empty($connName) || empty($params)) {
            return false;
        }

        if (!isset(self::$modelInstance[$connName]) || !self::$modelInstance[$connName]) {
            if ($connType == 'DBAL') {
                $config = new \Doctrine\DBAL\Configuration();

                self::$modelInstance[$connName] = \Doctrine\DBAL\DriverManager::getConnection($params, $config);
            } elseif ($connType == 'ORM') {
                $isDevMode = false;

                $paths = array(
                    BASEDIR
                    . DIRECTORY_SEPARATOR
                    . 'vendor'
                    . DIRECTORY_SEPARATOR
                    . 'doctrine'
                    . DIRECTORY_SEPARATOR
                    . 'orm'
                    . DIRECTORY_SEPARATOR
                    . 'lib'
                    . DIRECTORY_SEPARATOR
                    . 'Doctrine'
                    . DIRECTORY_SEPARATOR
                    . 'ORM',
                    BASEDIR
                    . DIRECTORY_SEPARATOR
                    . 'models'
                    . DIRECTORY_SEPARATOR
                    . 'Application'
                    . DIRECTORY_SEPARATOR
                    . 'Models'
                    . DIRECTORY_SEPARATOR
                    . 'Entity',
                    /* add more as needed */
                );

                $driver = new AnnotationDriver(new AnnotationReader(), $paths);

                // NOTE: use "createXMLMetadataConfiguration()" for XML source
                //       use "createYAMLMetadataConfiguration()" for YAML source
                // NOTE: if the flag is set TRUE caching is done in memory
                //       if set to FALSE, will try to use APC, Xcache, Memcache or Redis caching
                //       see: http://docs.doctrine-project.org/en/latest/reference/advanced-configuration.html

                //$config = \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
                $config   = Setup::createConfiguration(true);

                $config->setMetadataDriverImpl($driver);

                self::$modelInstance[$connName] = \Doctrine\ORM\EntityManager::create($params, $config);
            } else {
                return false;
            }
        }

        return self::$modelInstance[$connName];
    }
}
