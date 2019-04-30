<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    3.1.0
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0.
 * @since      2.0.0
 *
 * @see       https://github.com/zendframework/zend-expressive for the canonical source repository
 * @copyright Copyright (c) 2018 Zend Technologies USA Inc. (https://www.zend.com)
 * @license   https://github.com/zendframework/zend-expressive/blob/master/LICENSE.md New BSD License
 */

namespace Ascmvc\Middleware;

use Pimple\Container;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Stratigility\Middleware\CallableMiddlewareDecorator;
use Zend\Stratigility\Middleware\RequestHandlerMiddleware;
use Zend\Stratigility\MiddlewarePipe;

use function array_shift;
use function count;
use function is_array;
use function is_callable;
use function is_string;

/**
 * Class MiddlewareFactory
 *
 * Marshal middleware for use in the application.
 *
 * This class provides a number of methods for preparing and returning
 * middleware for use within an application.
 *
 * If any middleware provided is already a MiddlewareInterface, it can be used
 * verbatim or decorated as-is. Other middleware types acceptable are:
 *
 * - PSR-15 RequestHandlerInterface instances; these will be decorated as
 *   RequestHandlerMiddleware instances.
 * - string service names resolving to middleware
 * - arrays of service names and/or MiddlewareInterface instances
 * - PHP callables that follow the PSR-15 signature
 *
 * Additionally, the class provides the following decorator/utility methods:
 *
 * - callableMiddleware() will decorate the callable middleware passed to it using
 *   CallableMiddlewareDecorator.
 * - handlerMiddleware() will decorate the request handler passed to it using
 *   RequestHandlerMiddleware.
 * - lazyMiddleware() will decorate the string service name passed to it, along with the
 *   factory instance, as a LazyLoadingMiddleware instance.
 * - pipeline() will create a MiddlewarePipe instance from the array of
 *   middleware passed to it, after passing each first to prepare().
 *
 */
class MiddlewareFactory
{
    /**
     * Contains an instance of the Pimple\Container class.
     *
     * @var Container
     */
    protected $container;

    /**
     * MiddlewareFactory constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Prepares the middleware according to type.
     *
     * @param string|array|callable|MiddlewareInterface|RequestHandlerInterface $middleware
     *
     * @return MiddlewareInterface
     *
     * @throws \Exception if argument is not one of the specified types.
     */
    public function prepare($middleware) : MiddlewareInterface
    {
        if ($middleware instanceof MiddlewareInterface) {
            return $middleware;
        }

        if ($middleware instanceof RequestHandlerInterface) {
            return $this->handlerMiddleware($middleware);
        }

        if (is_callable($middleware)) {
            return $this->callableMiddleware($middleware);
        }

        if (is_array($middleware)) {
            return $this->pipeline(...$middleware);
        }

        if (! is_string($middleware) || $middleware === '') {
            throw \Exception('Invalid Middleware:' . $middleware);
        }

        return $this->lazyMiddleware($middleware);
    }

    /**
     * Decorate callable standards-signature middleware via a CallableMiddlewareDecorator.
     *
     * @param callable $middleware
     *
     * @return CallableMiddlewareDecorator
     */
    public function callableMiddleware(callable $middleware) : CallableMiddlewareDecorator
    {
        return new CallableMiddlewareDecorator($middleware);
    }

    /**
     * Decorate a RequestHandlerInterface as middleware via RequestHandlerMiddleware.
     *
     * @param RequestHandlerInterface $handler
     *
     * @return RequestHandlerMiddleware
     */
    public function handlerMiddleware(RequestHandlerInterface $handler) : RequestHandlerMiddleware
    {
        return new RequestHandlerMiddleware($handler);
    }

    /**
     * Create lazy loading middleware based on a service name.
     *
     * @param string $middleware
     *
     * @return LazyLoadingMiddleware
     */
    public function lazyMiddleware(string $middleware) : LazyLoadingMiddleware
    {
        return new LazyLoadingMiddleware($this->container, $middleware);
    }

    /**
     * Create a middleware pipeline from an array of middleware.
     *
     * This method allows passing an array of middleware as either:
     *
     * - discrete arguments
     * - an array of middleware, using the splat operator: pipeline(...$array)
     * - an array of middleware as the sole argument: pipeline($array)
     *
     * Each item is passed to prepare() before being passed to the
     * MiddlewarePipe instance the method returns.
     *
     * @param string|array|MiddlewarePipe $middleware
     *
     * @return MiddlewarePipe
     *
     * @throws \Exception
     */
    public function pipeline(...$middleware) : MiddlewarePipe
    {
        // Allow passing arrays of middleware or individual lists of middleware
        if (is_array($middleware[0])
            && count($middleware) === 1
        ) {
            $middleware = array_shift($middleware);
        }
        $pipeline = new MiddlewarePipe();
        foreach ($middleware as $m) {
            $pipeline->pipe($this->prepare($m));
        }
        return $pipeline;
    }
}
