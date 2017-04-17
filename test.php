<?php

require __DIR__ . '/vendor/autoload.php';

$xsd = \JDWil\Xsd\Xsd::forFile('/home/jd/Documents/OOXML/schema/sml.xsd');

$xsd->dumpInfo();
