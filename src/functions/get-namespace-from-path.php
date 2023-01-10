<?php
/**
 * LightMVC/ASCMVC
 *
 * @package    LightMVC/ASCMVC
 * @author     Andrew Caya
 * @link       https://github.com/lightmvc/ascmvc
 * @version    5.0.0
 * @license    http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0.
 * @since      2.0.1
 */

namespace Ascmvc;

/**
 * Convenience function for extracting a controller namespace from a path.
 *
 * Usage:
 *
 * <code>
 * use function Ascmvc\getNamespaceFromPath;
 * // [...]
 * $namespaceName = getNamespaceFromPath('/foo/bar/baz');
 * </code>
 *
 * @param string $path
 * @param string $separator
 *
 * @return string
 */
function getNamespaceFromPath(string $path, string $separator = DIRECTORY_SEPARATOR) : string
{
    $pathArray = explode($separator, $path);
    $filePathArray['fileName'] = array_pop($pathArray);
    array_pop($pathArray);
    $filePathArray['domainName'] = array_pop($pathArray);
    $namespaceName = ucfirst($filePathArray['domainName']) . '\\Controllers\\' . ucfirst(substr($filePathArray['fileName'], 0, strlen($filePathArray['fileName']) - 4));

    return $namespaceName;
}
