<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    3.3.0
 * @license    Apache License, Version 2.0, see above
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 * @since      2.1.0
 */

namespace AscmvcTest\Session;

use Ascmvc\Session\Http;
use Ascmvc\Session\Random;
use PHPUnit\Framework\TestCase;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class RandomTest extends TestCase
{
    public function testRandStrReturnsStringWithSpecifiedLength()
    {
        $this->assertEquals(32, strlen(Random::randStr(32)));
    }

    public function testRandNumStrReturnsStringWithSpecifiedLength()
    {
        $this->assertEquals(32, strlen(Random::randNumStr(32)));
    }
}
