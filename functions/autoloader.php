<?php

/**
 * ================================================
 * Spool Style Autoloader
 * Following PSR0
 * ================================================
 */

function fabric_autoloader($className)
{
    $classNameParts = explode('\\', $className);

    if($classNameParts[0] != 'Fabric')
        return;

    $fileName = array_pop($classNameParts) . '.php';

    require_once FABRIC_CONTROLLERS . $fileName;
}
spl_autoload_register('fabric_autoloader');