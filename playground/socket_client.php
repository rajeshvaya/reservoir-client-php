<?php

$reservoir_server = '192.168.1.153';
$reservoir_port = '3142';


# TODO : usual config files, class oriented and get out of playground into /src

if(!($sock = socket_create(AF_INET, SOCK_STREAM, 0))) {
    $errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode);
     
    die("Couldn't create socket: [$errorcode] $errormsg \n");
}
 
echo "Socket created";

if(!socket_connect($sock , $reservoir_server , $reservoir_port)) {
    $errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode);
     
    die("Could not connect: [$errorcode] $errormsg \n");
}
 
echo "Connection established \n";

# Send data to the reservoir
$data = 'GET pk_movie';
echo "Sending data - '$data'";
if(!socket_send ($sock, $data, strlen($data), 0)) {
    $errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode);
     
    die("Could not send data: [$errorcode] $errormsg \n");
}

# Receive data from reservoir
if(socket_recv ($sock, $buffer, 1024, MSG_PEEK) === FALSE) {
    $errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode);
     
    die("Could not receive data: [$errorcode] $errormsg \n");
}
echo $buffer;



?>
