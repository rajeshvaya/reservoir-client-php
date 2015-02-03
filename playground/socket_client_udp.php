<?php

$server = 'localhost';
$port = 3142;
 
if(!($sock = socket_create(AF_INET, SOCK_DGRAM, 0))){
    $errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode);
    die("Couldn't create socket: [$errorcode] $errormsg \n");
}
echo "Socket created\n";
 
while(1)
{
    //prompt
    echo "\nEnter cache key for reservoir server : ";
    $input = fgets(STDIN);

    //send request
    if(!socket_sendto($sock, $input , strlen($input) , 0 , $server , $port)){
        $errorcode = socket_last_error();
        $errormsg = socket_strerror($errorcode);
        die("Could not send data: [$errorcode] $errormsg \n");
    }
         
    //Now receive reply from server and print it
    if(socket_recv($sock , $buffer , 2045 , MSG_PEEK) === FALSE){
        $errorcode = socket_last_error();
        $errormsg = socket_strerror($errorcode);
        die("Could not receive data: [$errorcode] $errormsg \n");
    }

    echo "Reply: $buffer\n";
}