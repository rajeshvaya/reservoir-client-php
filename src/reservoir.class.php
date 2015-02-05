<?php
/**
 * Reservoir client library for PHP
 */
class Reservoir{
    /**
     * The socket connection object
     * @var Object
     */
    private $socket;

    /**
     * Last error occured on the socket object
     * @var Array
     */
    public $error;    
    
    /**
     * Initliaze the socket with TCP/IP
     * @param String $protocl           [TCP or UDP]
     * @return boolean
     */
    function __construct($protocol='TCP'){
        if(!$this->create_socket($protocol))
            return false;
    }

    /**
     * Connect to the reservoir server over socket created
     * @param  String $host            [host name or host ip]
     * @param  Int $port               [Port default is 3142]
     * @param  Array $optional_params  [additional configurations]
     * @return Boolean                  
     */
    function connect($host, $port, $optional_params=array()){
        return $this->socket->connect($host, $port, $optional_params);
    }

    # TODO : need to set configs for default values like expiry
    /**
     * Set a cache item on reservoir
     * @param String  $key    
     * @param String  $value  
     * @param Int $expiry 
     * @return Boolean
     */
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

    /**
     * Get the value of the cache key from reservoir
     * @param  String $key 
     * @return Mixed
     */
    function get($key){
        $data = "GET {$key}";
        $response = $this->send($data);
        return $response;
    }

    /**
     * Set a cache item on reservoir dependent on another item
     * @param String  $key    
     * @param String  $value  
     * @param Int $expiry 
     * @return Boolean
     */
    function set_dependent($parent_key, $key, $value, $expiry=0){
        $data = "DEP {$expiry} {$parent_key}::{$key} {$value}";
        $result = $this->send($data);
        if($result){
            $result = explode(' '. $result);
            if($result[0] == 200)
                return true;
        }
        return false;
    }

    /**
     * Delete a cache item from reservoir
     * @param String $key 
     * @return Boolean
     */
    function delete($key){
        $data = "DEL {$key}";
        $response = $this->send($data);
        if($result){
            $result = explode(' '. $result);
            if($result[0] == 200)
                return true;
        }
        return false;
    }

    /**
     * Reservoir will increment the value by 1 if it is an integer and if the key doesnt exist it will initialize it
     * @param  String $key 
     * @return Boolean
     */
    function increment($key){
        $data = "ICR {$key}";
        $response = $this->send($data);
        return !!$response;
    }

    /**
     * Reservoir will decrement the value by 1 if it is an integer and if the key doesnt exist it will initialize it
     * @param  String $key 
     * @return Boolean
     */
    function decrement($key){
        $data = "DCR {$key}";
        $response = $this->send($data);
        return !!$response;
    }

    /**
     * Return the number of seconds elapsed since creation time of the cache key
     * @param  String $key 
     * @return Int
     */
    function timer($key){
        $data = "TMR {$key}";
        $response = $this->send($data);
        return $response;
    }

    /**
     * Set a cache item on reservoir for ONE TIME ACCESS only, after the first access it will deleted (kind of like notification)
     * @param String  $key    
     * @param String  $value  
     * @param Int $expiry 
     * @return Boolean
     */
    function one_time_access($key, $value, $expiry=0){
        $data = "OTA {$expiry} {$key} {$value}";
        $result = $this->send($data);
        if($result){
            $result = explode(' '. $result);
            if($result[0] == 200)
                return true;
        }
        
        return false;
    }

    /**
     * Set a immutable cache item on reservoir (it can only expire or be deleted)
     * @param String  $key    
     * @param String  $value  
     * @param Int $expiry 
     * @return Boolean
     */
    function set_immutable($key, $value, $expiry=0){
        $data = "TPL {$expiry} {$key} {$value}";
        $result = $this->send($data);
        if($result){
            $result = explode(' '. $result);
            if($result[0] == 200)
                return true;
        }
        
        return false;
    }
    
    /**
     * GET cache item and it if doesnt exist create it with the new value (it will always return the 'value')
     * @param String  $key    
     * @param String  $value  
     * @param Int $expiry 
     * @return Boolean
     */
    function get_or_set($key, $value, $expiry=0){
        $data = "GOS {$expiry} {$key} {$value}";
        $result = $this->send($data);
        return $result ? $result : false;
    }
    
    /**
     * function to ping the server for connectivity (useful for long running scripts and re-initializing the socket from client)
     * @return Boolean
     */
    function ping(){
        $data = "PING";
        $response = $this->send($data);
        return $response == 1 ? true : false;
    }

    /**
     * Disconnect the socket connection
     * @return Boolean
     */
    function disconnect(){
        return $this->socket->disconnect();
    }

    /**
     * Create a socket using the ReservoirSocket object
     * @param  [type] $protocol [description]
     * @return [type]           [description]
     */
    private function create_socket($protocol){
        $this->socket = new ReservoirSocket($protocol);
        return !!$this->socket;
    }

    /**
     * Send data to reservoir in a specific format. Use this function only to send custom data which is not defined in the class
     * @param  String  $data          [FORMAT: <CACHE-PROTOCOL> <EXPIRY> <KEY> <VALUE>]
     * @param  Boolean $expect_return [Should the class wait for the data return, if yes, call the recv function from within]
     * @return Mixed
     */
    private function send($data, $expect_return=true){
        return $this->socket->send($data, $expect_return);
    }

    /**
     * Get the error details on the socket connectin
     * @return Array [code, message]
     */
    private function get_error(){
        $this->socket->get_last_socket_error();
        return $this->socket->error;
    }

}

?>
