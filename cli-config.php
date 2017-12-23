<?php

$entityManager = require __DIR__.'/tests/bootstrap.php';

return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($entityManager);
