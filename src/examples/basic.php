<?php

include_once('../src/reservoir.class.php');

$reservoir = new Reservoir();

//check if socket was opened successfully
if(!$reservoir){
	echo "Error code: {$reservoir->error[0]}, Error: {$reservoir->error[1]}";
	exit;
}

//try connecting to the reservoir server through the socket created above
if(!$reservoir->connect('localhost', '3142')){
	echo "Error code: {$reservoir->error[0]}, Error: {$reservoir->error[1]}";
	exit;	
}

//if everything went well above we are ready to send and receive cache data
// set a cache key/item/expiry 
$reservoir->set('coldplay', 'awesome', 300);

//get the cache key
$coldplay = $reservoir->get('coldplay');
echo $coldplay;

//get the time lapsed of the cache item
$time_lapsed_in_seconds = $reservoir->timer('coldplay');
echo $time_lapsed_in_seconds;

// to delete a cache item
$reservoir->delete('coldplay');

//close the reservoir connection
$reservoir->disconnect();


?>