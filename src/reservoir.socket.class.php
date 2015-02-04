<?php

class ReservoirSocket{
	public $protocol;
	public $socket;
	public $host;
	public $port;

	 /**
     * Initliaze the socket with TCP/IP
     * @return boolean
     */
	function __construct($protocol='TCP'){
		if(!in_array($protocol, ['TCP', 'UDP']))
			$protocol = 'TCP';
		
		$this->protocol = $protocol;
	}

	/**
     * Connect to the reservoir server over socket created
     * @param  String $host            [host name or host ip]
     * @param  Int $port               [Port default is 3142]
     * @param  Array $optional_params  [additional configurations]
     * @return Boolean                  
     */
    function connect($host, $port, $optional_params=array()){
    	$this->host = $host;
    	$this->port = $port;

    	// open reliable connection
    	if($this->protocol == 'TCP'){
	        if(!socket_connect($this->socket, $host, $port)){
	            $this->get_last_socket_error();
	            return false;
	        }else{
	            return true;
	        }
	    }else{
	    	// there is no open connection required for UDP
	    	return true;
	    }
    }

    /**
     * Send data to reservoir in a specific format. Use this function only to send custom data which is not defined in the class
     * @param  String  $data          [FORMAT: <CACHE-PROTOCOL> <EXPIRY> <KEY> <VALUE>]
     * @param  Boolean $expect_return [Should the class wait for the data return, if yes, call the recv function from within]
     * @return Boolean
     */
    private function send($data, $expect_return=true){
    	if($this->protocol == 'TCP'){
	        if(!socket_send($this->socket, $data, strlen($data), 0)){
	            $this->get_last_socket_error();
	            return false;
	        }
	    }else{
	    	if(!socket_sendto($this->socket, $data, strlen($data), 0, $this->host, $this->port)){
	    	    $this->get_last_socket_error();
	    	    return false;
	    	}
	    }

	    if($expect_return){
            $response = $this->receive();
            if(!$response)
                return false;
            return $response;
        }

        
        return true;
    }

    /**
     * Receive data from the server. Do not wait on endless connection - MSG_PEEK
     * @return Mixed
     */
    private function receive(){
        if(socket_recv($this->socket, $response, 1024, MSG_PEEK) == FALSE){
            $this->get_last_socket_error();
            return false;
        }

        if(!trim(response))
            return false;

        return $response;
    }

    /**
     * Get the error details on the socket connectin
     * @return Array [code, message]
     */
    private function get_last_socket_error(){
        $this->error['code'] = socket_last_error();
        $this->error['message'] = socket_strerror($this->error['code']);
    }

}

?>