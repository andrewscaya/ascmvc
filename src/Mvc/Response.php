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
use Ascmvc\AbstractResponse;


class Response extends AbstractResponse {

    /**
     * Initializes this class by assigning some content to the $response property.
     *
     * @param strin $content.
     *
     * @return void.
     */
    public function __construct(string $header, string $content = null)
    {
        $this->header = $header;

        if (isset($content)) {
            $this->response = $content;
        }
    }

    public function __toString()
    {
        return $this->response;
    }

    /**
     * @return string
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @return string
     */
    public function getResponse()
    {
        return $this->response;
    }
    
}
