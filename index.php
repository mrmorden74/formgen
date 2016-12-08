<?php
require_once ('app/lib/base.php');
$f3 = Base::instance();

$f3->config('config.ini');
$f3->config('config_db.ini');
// $f3->config('config_db_bak.ini');
$f3->config('routes.ini');
	echo $init;
 if ($init === 'init') {
	echo "INITIALISIEREN";
	include ('initialize.php');
	exit;
 }

new Session();

$f3->run();
echo 'TEST';
 
