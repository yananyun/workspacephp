<?php
echo 'index php intel online11<br/>';
define ( "STARTIME", microtimeFloat () );
define ( "ENDTIME", microtime ( true ) );
$time = microtime ( true );
echo 'intel online2<br/>';
require_once 'common/base.php';

ini_set ( 'gd.jpeg_ignore_warning', 1 );
$system = new System ();
$system->init ( $argv );
function microtimeFloat() {
	list ( $usec, $sec ) = explode ( " ", microtime () );
	echo "intel online3<br/>";
	return (( float ) $usec + ( float ) $sec);
}