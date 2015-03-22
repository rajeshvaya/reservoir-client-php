<?php

class ReservoirSocket{
	/**
	 * Protocol to be used for socket connection [TCP] or [UDP]
	 * @var [type]
	 */
	private $protocol;

	/**
	 * The socket object for the server connection based on the protocol set
	 * @var ReservoirSocket
	 */
	public $socket;

	/**
	 * Reservoir server host
	 * @var String
	 */
	public $host;

	/**
	 * [$port description]
	 * @var Int
	 */
	public $port;

	/**
	 * last socket error
	 * @var Array
	 */
	public $error;

	 /**
     * Initliaze the socket with TCP/IP
     * @return boolean
     */
	function __construct($host, $port, $protocol='TCP'){
        $this->host = $host;
        $this->port = $port;
		$this->protocol = in_array($protocol, ['TCP', 'UDP']) ? $protocol : 'TCP';
		return $this->create_socket();
	}

	/**
     * Connect to the reservoir server over socket created
     * @param  String $host            [host name or host ip]
     * @param  Int $port               [Port default is 3142]
     * @param  Array $optional_params  [additional configurations]
     * @return Boolean                  
     */
    function connect($optional_params=array()){
    	
    	// open reliable connection
    	if($this->protocol == 'TCP'){
	        if(!socket_connect($this->socket, $this->host, $this->port)){
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
     * @return Mixed
     */
    function send($data, $expect_return=true, $raw_response=true){
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
            //something unexpected happened
            if(!$response)
                return false;
            // if response failed from the server side
            if($response == 'None')
                return 'None';
            // got proper response from the server
            if($raw_response)
                return json_decode($response);
            else
                return json_decode($response)['data'];
        }

        return true;
    }

    /**
     * Receive data from the server. Do not wait on endless connection - MSG_PEEK
     * @return Mixed
     */
    function receive(){
        if(socket_recv($this->socket, $response, 1024, MSG_PEEK) == FALSE){
            $this->get_last_socket_error();
            return false;
        }

        if(!trim($response))
            return false;

        return $response;
    }

    /**
     * Disconnect from the socket
     * @return Boolean
     */
    function disconnect(){
        return socket_close($this->socket);
    }

    /**
     * Create a socket based on the protocol set.
     * @return Boolean
     */
    private function create_socket(){
    	if($this->protocol =='TCP')
    		return $this->create_tcp_socket();
    	if($this->protocol == 'UDP')
    		return $this->create_upd_socket();
    }

    /**
     * Create a TCP socket
     * @return Boolean
     */
    private function create_tcp_socket(){
    	if(!($this->socket = socket_create(AF_INET, SOCK_STREAM, 0))){
    		$this->get_last_socket_error();
    		return false;
    	}
    	return $this->socket;
    }

    /**
     * Create a UDP socket
     * @return Boolean
     */
    private function create_upd_socket(){
    	if(!($this->socket = socket_create(AF_INET, SOCK_DGRAM, 0))){
    		$this->get_last_socket_error();
    		return false;
    	}
    	return $this->socket;
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