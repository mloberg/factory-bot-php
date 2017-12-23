<?php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;

require_once __DIR__.'/../vendor/autoload.php';

function _env(string $key, $default = null)
{
    if (false === $env = getenv($key)) {
        return $default;
    }

    return $env;
}

$dbParams = [
    'driver' => 'pdo_mysql',
    'host' => _env('DB_HOST', '127.0.0.1'),
    'user' => _env('DB_USER', 'root'),
    'password' => _env('DB_PASSWORD', ''),
    'dbname' => _env('DB_NAME', 'test'),
];

$config = Setup::createAnnotationMetadataConfiguration([__DIR__.'/Model/'], true, null, null, false);
$entityManager = EntityManager::create($dbParams, $config);

$GLOBALS['entityManager'] = $entityManager;

return $entityManager;
