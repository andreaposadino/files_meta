<?php

require_once('apps/files_meta/lib_meta.php');

// Add an entry in the app list
OCP\App::register( array(
  'order' => 80,
  'id' => 'files_meta',
  'name' => 'Meta' ));

OCP\Util::addscript('files_meta', 'meta');
OCP\Util::addStyle( 'files_meta', 'meta' );
//OCP\Util::addscript( 'files_latexeditor', 'prettyprint');

// Listen to write signals
//OCP\Util::connectHook(OC_Filesystem::CLASSNAME, OC_Filesystem::signal_post_write, "OCA_Versions\Storage", "write_hook");

?>
