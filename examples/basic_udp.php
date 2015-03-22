<?php

include_once('../src/reservoir.class.php');

$reservoir = new Reservoir('localhost', 3142, 'UDP');

//check if socket was opened successfully
if(!$reservoir){
    $error = $reservoir->get_error();
    echo "Error code: {$error[0]}, Error: {$error[1]}";
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


?>