<?php

# TODO : usual config files, class oriented and get out of playground into /src

if(!($sock = socket_create(AF_INET, SOCK_STREAM, 0))) {
    $errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode);
     
    die("Couldn't create socket: [$errorcode] $errormsg \n");
}
 
echo "Socket created";

if(!socket_connect($sock , 'localhost' , 3142)) {
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

# TODO : need to research a little more on receiving data back from reservoir over TCP/IP

?>
