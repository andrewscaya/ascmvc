<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    4.0.0
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0.
 * @since      2.0.0
 *
 * @see       https://github.com/laminas/laminas-stratigility for the canonical source repository
 * @copyright https://github.com/laminas/laminas-stratigility/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-stratigility/blob/master/LICENSE.md New BSD License
 */

namespace Ascmvc\Middleware;

use Pimple\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class LazyLoadingMiddleware
 *
 * This class stores given middleware inside a Container factory in order to load them only when needed.
 *
 */
class LazyLoadingMiddleware implements MiddlewareInterface
{
    /**
     * Contains an instance of Pimple\Container.
     *
     * @var Container
     */
    protected $container;

    /**
     * Contains a string which is the name of the middleware.
     *
     * @var string
     */
    protected $middlewareName;

    /**
     * LazyLoadingMiddleware constructor.
     *
     * @param Container $container
     * @param string $middlewareName
     */
    public function __construct(
        Container $container,
        string $middlewareName
    ) {
        $container[$middlewareName] = $container->factory(function ($container) use ($middlewareName) {
            return new $middlewareName;
        });

        $this->container = $container;
        $this->middlewareName = $middlewareName;
    }

    /**
     * Middleware interface method.
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $middleware = $this->container[$this->middlewareName];
        return $middleware->process($request, $handler);
    }
}
