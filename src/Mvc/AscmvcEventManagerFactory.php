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

use Zend\EventManager\SharedEventManager;

class AscmvcEventManagerFactory implements FactoryInterface {
	
	public function factory()
	{
		$shared = new SharedEventManager();
		return new AscmvcEventManager($shared);
	}
	
}
