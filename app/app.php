<?php

use Composer\Autoload\ClassLoader;
use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Request;

/**
 * @var ClassLoader $loader
 */
$loader = require __DIR__ . '/autoload.php';

$env = getenv('SYMFONY_ENV') ?: 'prod';
$debug = getenv('SYMFONY_DEBUG') === '1' && $env !== 'prod';

include_once __DIR__ . '/../var/bootstrap.php.cache';

if ($debug) {
    Debug::enable();
}

$kernel = new AppKernel($env, $debug);
$kernel->loadClassCache();
//$kernel = new AppCache($kernel);

// When using the HttpCache, you need to call the method in your front controller instead of relying on the configuration parameter
//Request::enableHttpMethodParameterOverride();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
