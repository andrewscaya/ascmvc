<?php
/**
 * ASC LightMVC
 *
 * @package    ASC LightMVC
 * @author     Andrew Caya
 * @link       https://github.com/andrewscaya
 * @version    1.0.0
 * @license    http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 */

namespace Ascmvc\Mvc;

// external namespaces to reference
use Ascmvc\AbstractModelObject;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;


class Doctrine extends AbstractModelObject {

    protected function __construct()
    {

    }

    protected function __clone()
    {

    }

    public static function getInstance($connType, $connName, Array $params)
    {
        if (!isset($connType) || !isset($connName) || !isset($params)) {
            
            return false;
            
        }
        
        if(!self::$doctrineInstance[$connName]) {
            
            if ($connType == 'DBAL') {
                
                $config = new \Doctrine\DBAL\Configuration();
                
                self::$doctrineInstance[$connName] = \Doctrine\DBAL\DriverManager::getConnection($params, $config);
                
            }
            elseif ($connType == 'ORM') {
                
                $isDevMode = false;
                
                $paths = array(
                    BASEDIR . DIRECTORY_SEPARATOR . 'libs' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'doctrine' . DIRECTORY_SEPARATOR . 'orm' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'Doctrine' . DIRECTORY_SEPARATOR . 'ORM',
                    BASEDIR . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'Application' . DIRECTORY_SEPARATOR . 'Models' . DIRECTORY_SEPARATOR . 'Entity',
                /* add more as needed */ );
                
                $driver = new AnnotationDriver(new AnnotationReader(), $paths);
                
                AnnotationRegistry::registerLoader('class_exists');
                
                // NOTE: use "createXMLMetadataConfiguration()" for XML source
                //       use "createYAMLMetadataConfiguration()" for YAML source
                // NOTE: if the flag is set TRUE caching is done in memory
                //       if set to FALSE, will try to use APC, Xcache, Memcache or Redis caching
                //       see: http://docs.doctrine-project.org/en/latest/reference/advanced-configuration.html
                
                //$config = \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
                $config   = Setup::createConfiguration(TRUE);
                
                $config->setMetadataDriverImpl($driver);
            
                self::$doctrineInstance[$connName] = \Doctrine\ORM\EntityManager::create($params, $config);

            }
            else {
                
                return FALSE;
                
            }
            
        }

        return self::$doctrineInstance[$connName];
    }

}