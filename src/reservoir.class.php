<?php

class Reservoir{
    
    private $socket;
    public $error;    
    
    function __construct(){
        if(!($this->socket = socket_create(AF_INET, SOCK_STREAM, 0))){
            $this->get_last_socket_error();
            return false;
        }
    }

    function connect($host, $port, $optional_params=array()){
        if(!socket_connect($this->socket, $host, $port)){
            $this->get_last_socket_error();
            return false;
        }else{
            return true;
        }
    }

    function get($key){
        $data = "GET {$key}";
        $response = $this->send($data);
        return $response;
    }
    
    # TODO : need to set configs for default values like expiry
    function set($key, $value, $expiry=0){
        $data = "SET {$expiry} {$key} {$value}";
        $result = $this->send($data);
        if($result){
            $result = explode(' '. $result);
            if($result[0] == 200)
                return true;
        }
        
        return false;
    }

    private function send($data, $expect_return=true){
        if(!socket_send($this->socket, $data, strlen($data), 0)){
            $this->get_last_socket_error();
            return false;
        }

        if($expect_return){
            $response = $this->receive();
            if(!$response)
                return false;
            return $response;
        }

        return true;
    }

    private function receive(){
        if(socket_recv($this->socket, $response, 1024, MSG_PEEK) == FALSE){
            $this->get_last_socket_error();
            return false;
        }

        if(!trim(response))
            return false;

        return $response;
    }

    private function get_last_socket_error(){
        $this->error['code'] = socket_last_error();
        $this->error['message'] = socket_strerror($this->error['code']);
    }

}

?>
