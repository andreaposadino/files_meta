<?php
OCP\JSON::checkAppEnabled('files_meta');

require_once('apps/files_meta/lib_meta.php');

$userDirectory = "/".OCP\USER::getUser()."/files";


$source = $_POST['item'];
$source = strip_tags( $source );
$d = $_POST['description'];
$d = strip_tags( $d );

 $realpath = '/' . OCP\USER::getUser() . '/files' . $source;

OC_FilesMeta::setByKey($realpath, 'description', $d);

OCP\JSON::success();
