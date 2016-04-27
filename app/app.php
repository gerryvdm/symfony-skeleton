<?php

use Composer\Autoload\ClassLoader;
use Symfony\Component\ClassLoader\ApcClassLoader;
use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Request;

/**
 * @var ClassLoader
 */
$loader = require __DIR__ . '/autoload.php';
include_once __DIR__ . '/../var/bootstrap.php.cache';

$env = getenv('SYMFONY_ENV') ?: 'prod';
$debug = getenv('SYMFONY_DEBUG') === '1' && $env !== 'prod';

if ($debug) {
    Debug::enable();
}

// You should change the ApcClassLoader first argument to a unique prefix
// in order to prevent cache key conflicts with other applications
// also using APC.
$apcLoader = new ApcClassLoader(sha1(__FILE__), $loader);
$loader->unregister();
$apcLoader->register(true);

$kernel = new AppKernel($env, $debug);
$kernel->loadClassCache();
//$kernel = new AppCache($kernel);

// When using the HttpCache, you need to call the method in your front controller instead of relying on the configuration parameter
//Request::enableHttpMethodParameterOverride();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
