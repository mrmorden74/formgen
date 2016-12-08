<?php
require_once ('app/lib/base.php');
include ('app/inc/db_functions.inc.php');
$f3 = Base::instance();
$filename = 'config_db.ini';

if (file_exists($filename)) {
} else {
	include ('initialize.php');
	exit;
}

$f3->config('config.ini');
$f3->config('config_db.ini');
// $f3->config('config_db_bak.ini');
$f3->config('routes.ini');

new Session();

$f3->run();
 
