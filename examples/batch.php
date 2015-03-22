<?php

include_once('../src/reservoir.class.php');

$reservoir = new Reservoir('localhost', '3142'); // defaults to TCP

//check if socket was opened successfully
if(!$reservoir){
    $error = $reservoir->get_error();
    echo "Error code: {$error[0]}, Error: {$error[1]}";
    exit;
}

//try connecting to the reservoir server through the socket created above
if(!$reservoir->connect()){
    echo "Error code: {$reservoir->error[0]}, Error: {$reservoir->error[1]}";
    exit;   
}


// set a cache key/value/expiry 
$data = array();
$data[] = array('key' => 'coldplay', 'value' => 'Best band', 'expiry' => 3600);
$data[] = array('key' => 'coldplay-singer', 'value' => 'Chris Martin', 'expiry' => 3600);
$data[] = array('key' => 'coldplay-guitar', 'value' => 'Johny Buckland', 'expiry' => 3600);
$data[] = array('key' => 'coldplay-base', 'value' => 'Guy Berrymen', 'expiry' => 3600);
$data[] = array('key' => 'coldplay-drums', 'value' => 'Will Champion', 'expiry' => 3600);

$return_data = $reservoir->set_batch($data);

//get the cache keys
$data = array('coldplay', 'coldplay-singer', 'coldplay-guitar', 'coldplay-base', 'coldplay-drums');
$return_data = $reservoir->get('coldplay');
var_dump($return_data);

//delete cache keys
$data = array('coldplay', 'coldplay-singer', 'coldplay-guitar', 'coldplay-base', 'coldplay-drums');
$return_data = $reservoir->delete($data);
var_dump($return_data);

//close the reservoir connection
$reservoir->disconnect();


?>