<?php
define('DEBUG',true);
require_once('apps/files_meta/lib_meta.php');

// Add an entry in the app list
/*OCP\App::register( array(
  'order' => 80,
  'id' => 'files_meta',
  'name' => 'Meta' ));
*/
OCP\Util::addScript('files_meta', 'meta');
OCP\Util::addStyle( 'files_meta', 'meta' );
