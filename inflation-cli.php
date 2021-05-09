<?php
require_once(realpath(__DIR__.'/inflation.php'));
$inflation = new inflation();

if( count($argv) < 3 ){
	echo 'Expected at least arguments; price and then date but recieved '.(count($argv)-1).'.';
	exit;
}
if( count($argv) > 4 ){
	echo 'Expected no more than 4 arguments; price, original date, and now date but recieved '.(count($argv)-1).'.';
	exit;
}

if( count($argv) === 3 ){
	echo $inflation->adjust($argv[1],$argv[2]);	
}else if(count($argv) === 4 ){
	echo $inflation->adjust($argv[1],$argv[2],$argv[3]);	
}else{
	echo 'Somethig went dreadfully wrong.';
}
?>