<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    2.1.0
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0.
 * @since      2.0.0
 */

namespace Ascmvc\Mvc;

use Ascmvc\AbstractModelObject;
use Ascmvc\Proxy\AtlasORM;
use Atlas\Orm\Transaction\AutoTransact;

/**
 * Class Atlas
 *
 * The Atlas class extends the AbstractModelObject and uses the atlas/orm library.
 */
class Atlas extends AbstractModelObject
{
    // @codeCoverageIgnoreStart
    /**
     * Atlas constructor.
     */
    protected function __construct()
    {
    }

    /**
     * Atlas clone method.
     */
    protected function __clone()
    {
    }
    // @codeCoverageIgnoreEnd

    /**
     * Gets a Singleton instance of the Atlas class.
     *
     * @param string $connType
     * @param string $connName
     * @param array $params
     *
     * @return \Atlas\Orm\Atlas|bool
     */
    public static function getInstance($connType, $connName, Array $params)
    {
        if (empty($connType) || empty($connName) || empty($params)) {
            return false;
        }

        if (!isset(self::$modelInstance[$connName]) || !self::$modelInstance[$connName]) {
            if ($connType == 'ORM') {
                self::$modelInstance[$connName] = AtlasORM::create(
                    "{$params['driver']}:host={$params['host']};dbname={$params['dbname']}",
                    $params['user'],
                    $params['password'],
                    AutoTransact::CLASS
                );
            } else {
                return false;
            }
        }

        return self::$modelInstance[$connName];
    }
}
