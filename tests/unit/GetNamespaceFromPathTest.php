<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    2.0.2
 * @license    Apache License, Version 2.0, see above
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 * @since      2.0.1
 */

namespace AscmvcTest;

use PHPUnit\Framework\TestCase;

use function Ascmvc\getNamespaceFromPath;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class GetNamespaceFromPathTest extends TestCase
{
    public function testGetNamespaceFromPathWithUnixPath()
    {
        $path = '/this/is/an/absolute/unix/path.php';

        $filePathArray = getNamespaceFromPath($path);

        $this->assertTrue(is_array($filePathArray));

        $this->assertSame('absolute', $filePathArray['domainName']);

        $this->assertSame('path.php', $filePathArray['fileName']);
    }

    public function testGetNamespaceFromPathWithWindowsPath()
    {
        $path = 'C:\this\is\an\absolute\windows\path.php';

        $filePathArray = getNamespaceFromPath($path, '\\');

        $this->assertTrue(is_array($filePathArray));

        $this->assertSame('absolute', $filePathArray['domainName']);

        $this->assertSame('path.php', $filePathArray['fileName']);
    }
}
