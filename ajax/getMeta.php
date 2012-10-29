<?php

OCP\JSON::checkAppEnabled('files_meta');

require_once('apps/files_meta/lib_meta.php');

$userDirectory = "/".OCP\USER::getUser()."/files";


$source = $_GET['item'];
$source = strip_tags( $source );

$description = OC_FilesMeta::getDescription($source);

OCP\JSON::encodedPrint($description);
