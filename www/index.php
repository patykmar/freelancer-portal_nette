<?php

// Uncomment this line if you must temporarily take down your site for maintenance.
// require '.maintenance.php';


$container = require __DIR__ . '/../app/Bootstrap.php';

$container->getService('application')->run();
