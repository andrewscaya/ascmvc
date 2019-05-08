<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    3.1.1
 * @license    Apache License, Version 2.0, see above
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 * @since      2.1.0
 */

namespace AscmvcTest\Session\Cache;

use Ascmvc\Session\Cache\DoctrineInvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class DoctrineInvalidArgumentExceptionTest extends TestCase
{
    public function testDoctrineInvalidArgumentExceptionConstructor()
    {
        $doctrineInvalidArgumentException = new DoctrineInvalidArgumentException('1a1a1a');

        $this->assertInstanceOf(DoctrineInvalidArgumentException::class, $doctrineInvalidArgumentException);

        $this->assertSame('1a1a1a', $doctrineInvalidArgumentException->getArgument());
    }
}
