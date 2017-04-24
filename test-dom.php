<?php

require __DIR__ . '/vendor/autoload.php';

$workbook = new \JDWil\Xsd\Test\Element\Workbook();
$workbook->addFileRecoveryPr(new \JDWil\Xsd\Test\ComplexType\CT_FileRecoveryPr());
$stream = \JDWil\Xsd\Test\Stream\OutputStream::streamedTo('./workbook.xml');
$workbook->writeXMLTo($stream);
