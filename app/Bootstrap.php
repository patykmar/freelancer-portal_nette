<?php

require_once __DIR__ . '/../vendor/autoload.php';

$configurator = new Nette\Configurator;

//$configurator->setDebugMode('23.75.345.200'); // enable for your remote IP
$configurator->setDebugMode(true); // enable for your remote IP
$configurator->enableDebugger(__DIR__ . '/../log');

$configurator->setTempDirectory(__DIR__ . '/../temp');

$configurator->createRobotLoader()
    ->addDirectory(__DIR__)
    ->register();

$configurator->addConfig(__DIR__ . '/../config/common.neon');
$configurator->addConfig(__DIR__ . '/../config/services.neon');
$configurator->addConfig(__DIR__ . '/../config/local.neon');

return $configurator->createContainer();
