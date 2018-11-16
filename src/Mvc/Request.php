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

use Ascmvc\AbstractApp;
use Ascmvc\AbstractRequest;


class Request extends AbstractRequest {
    
    /**
     * Initializes this class by assigning the elements of the URI to the
     * array $requestURI.
     *
     * @param Object:App &$app.
     *
     * @return void.
     */
    public function __construct(AbstractApp &$app)
    {
        $this->app = $app;
        
        $this->baseConfig = $this->app->getBaseConfig();

        $this->requestURI['scheme'] = $_SERVER['REQUEST_SCHEME'];
        
        $this->requestURI['host'] = $_SERVER['HTTP_HOST'];
        
        $tempURI = ltrim($_SERVER['REQUEST_URI']);
        
        $tempURI = (string) urldecode($tempURI);
        
        $tempURI = substr($tempURI, 0, 140);
        
        $tempURI = (filter_var($tempURI, FILTER_VALIDATE_URL)) ? $tempURI : filter_var($tempURI, FILTER_SANITIZE_URL);
        
        $tempURI = preg_replace('/[^\w]\.{1,2}/', '', $tempURI);
        
        $requestUriArrayTemp = $tempURI;
        
        $tempURI = (strpos($tempURI, '?') !== FALSE) ? explode('?', $tempURI) : FALSE;
        
        $this->requestURI['getparams'] = ($tempURI) ?: NULL;
        
        $requestUriArrayTemp = ($tempURI) ? explode('/', $tempURI[0]) : explode('/', $requestUriArrayTemp);
        
        $requestUriArrayCount = count($requestUriArrayTemp) - 1;
        
        
        if (isset($this->baseConfig['appFolder'])
            && in_array($this->baseConfig['appFolder'], $requestUriArrayTemp)) {
        
            $requestUriArrayElements = $requestUriArrayCount -3;
        
            $arrayBaseIndex = 4;
        
        }
        elseif (in_array('public', $requestUriArrayTemp)) {
        
            $requestUriArrayElements = $requestUriArrayCount -2;
        
            $arrayBaseIndex = 3;
        
        }
        else {
        
            $requestUriArrayElements = $requestUriArrayCount - 1;
        
            $arrayBaseIndex = 2;
        
        }
        
        
        if ($requestUriArrayElements >= 2
            && !empty($requestUriArrayTemp[$arrayBaseIndex])) {
                
                $this->requestURI['controller'] = ucfirst($requestUriArrayTemp[$arrayBaseIndex]);
        
                $this->requestURI['method'] = $requestUriArrayTemp[$arrayBaseIndex + 1];
        
        }
        elseif ($requestUriArrayElements == 1 && $requestUriArrayTemp[$arrayBaseIndex] != 'index') {
        
                $this->requestURI['controller'] = ucfirst($requestUriArrayTemp[$arrayBaseIndex]);
        
        }
    }
    
    /**
     * @return array|null $requestURI
     */
    public function getRequestURI()
    {
        return $this->requestURI;
    }
    
}