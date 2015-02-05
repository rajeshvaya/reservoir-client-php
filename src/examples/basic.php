<?php

include_once('../src/reservoir.class.php');

$reservoir = new Reservoir();

//check if socket was opened successfully
if(!$reservoir){
	$error = $reservoir->get_error();
	echo "Error code: {$error[0]}, Error: {$error[1]}";
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

//setting a dependent key (parent_key, key, value, expiry)
// This entry will be dependent on the existance of the parent entry and will auto expire/dele if the parent is expired or deleted (it can also have its own expiry time)
$reservoir->set_dependent('coldplay', 'votes', 8871, 0);

//simple incrementing/decrementing numeric values
$reservoir->increment('votes'); // results to 8872

//get the cache key
$coldplay = $reservoir->get('coldplay');
echo $coldplay;

//get the cache key
$coldplay_votes = $reservoir->get('votes');
echo $coldplay_votes; // results to 8872

//get the time lapsed of the cache item
$time_lapsed_in_seconds = $reservoir->timer('coldplay');
echo $time_lapsed_in_seconds;

// to delete a cache item - this will also delete the dependants
$reservoir->delete('coldplay');

//accessing the child cache item when the parent is deleted (will always return false)
$coldplay_votes = $reservoir->get('votes');
echo $coldplay_votes; //results to false

//close the reservoir connection
$reservoir->disconnect();


?>