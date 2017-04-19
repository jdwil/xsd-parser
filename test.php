<?php

require __DIR__ . '/vendor/autoload.php';

$xsd = \JDWil\Xsd\Xsd::forFile('./test-data/sml.xsd');

$xsd->dumpInfo();
