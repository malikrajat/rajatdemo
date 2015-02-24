<?php
 /**
 * Error handler for github and bitbucket.
 * @author Arjun Dev <arjundevdev@gmail.com>
 * @version   $id: repoapi V0.01
 * @created   2014-12-01
 */


 /**
 * Extend the Exeption class for handling error for github and bitbucket
 */
class AuthException extends Exception {
    
    protected static $statusCodes = array(
          0 => 'OK',
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported'
    );

    /**
     *  Acceptable HTTP header codes and custom messages
     * @var string[int]
     */
    public static $acceptableCodes = array(
        0 => '',
        200 => '',
        201 => '',
        204 => '',
    );

    /**
     * Custom error function to display error.
	 */  
	public function errorMessage($code) {
	
	if(array_key_exists( (int)$code, self::$statusCodes))
	{
    //error message
    $message = $this->getMessage();
    }
	return $message;
  }

}