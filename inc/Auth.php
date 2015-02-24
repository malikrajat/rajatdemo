<?php
/**
 * Add issue in specific repository based on repository url.
 * @author Arjun Dev <arjundevdev@gmail.com>
 * @version   $id: repoapi V0.01
 * @created   2014-12-01
 */

//include Exeption file for handling error
require_once('AuthException.php');

/**
 * Auth class contain function of cUrl request for github and bitbucket.
 */
class Auth {

    /**
     * Default options/settings
     * @var string[string]
     */
    public $options = array    (
        'protocol' => 'https',
        'github_url' => ':protocol://api.github.com/:path',
        'bitbucket_url' => ':protocol://api.bitbucket.org/1.0/:path',
        'user_agent' => 'tan-tan.:apitype-api',
        'timeout' => 10,
        'api_url' => null,
        'api_path' => null,
        'api_type' => null,
        'username' => null,
        'password' => null,
        'custom_errors' => array()
    );

    /**
     * Returned http header code
     * @var string
     */
    public $http_code;

    /**
     * History of the request class, for cache purposes
     * @var array
     */
    protected static $history = array();

    /**
     * Content Type to make the request
     * @var string
     */
    private $content_type = "";

   /**
     * Default constructor, paramters takes 
     * @param $api_url (String), $username (String), $password (String)
     */
	 
    public function __construct($api_url = null, $username = null, $password = null) {
        $options = array(
            'api_url' => $api_url,
            'username' => $username,
            'password' => $password
        );

        //configurationApi api options
        $this->configurationApi( $options );

        //validate api credentials
        $this->validateApiCredentials();
    }

    /**
     * configurationApi function used of configure the API path , which one api will be trigger github/bitbucket
     *@param $option contain api credetials (Username, Password, Api Url)
     */
    public function configurationApi(array $options) {
	$this->options = $options + $this->options;

        //Verify Api's domain
        $pieces = parse_url($this->options['api_url']);
        $domain = isset($pieces['host']) ? $pieces['host'] : '';
        $api_domain = '';
        if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
            $api_domain = $regs['domain'];
        }

        //Set api path
        if($pieces['path'][0] == '/') {
            $pieces['path'] = substr($pieces['path'], 1);
        }
        if($pieces['path'][strlen($pieces['path'])-1] == '/') {
            $pieces['path'] = substr($pieces['path'], 0, -1);
        }


        //Set api type
        if($api_domain == 'github.com') {
            $this->setOption('api_type', 'github');
            $this->setOption('api_path', 'repos/'.trim($pieces['path']).'/issues');
        } else if($api_domain == 'bitbucket.org') {
            $this->setOption('api_type', 'bitbucket');
            $this->setOption('api_path', 'repositories/'.trim($pieces['path']).'/issues');
        } else {
            $this->error_handler( 'Method Not Allowed' );
        }

        //Set user agent based on Api's
        if( !empty($this->options['api_type']) ) {
            $user_agent = str_replace(':apitype', $this->options['api_type'], $this->options['user_agent']);
            $this->setOption('user_agent', $user_agent);
        }
		
		    }

 	/**
     * Set an option 
     */
	 
  	public function setOption($name, $value) {
        $this->options[$name] = $value;
        return $this;
    }
	
	/**
     * Get an option 
     */
	 
	 public function getOption($name, $default = null) {
        return isset( $this->options[$name] ) ? $this->options[$name] : $default;
    }
	
	/**
     * Set http request via post method 
     */
	 
	public function post($parameters = array(), array $options = array()) {
        return $this->sendRequest( $parameters, 'POST', $options );
    }
	
	/**
     * Send http request to github/bitbucket via post method 
     */
	 
	public function sendRequest($parameters = array(), $method = 'POST', array $options = array()) {
        $initialOptions = null;
        $response = null;

        if ( ! empty( $options ) ) {
            $initialOptions = $this->options;
            $this->configurationApi( $options );
        }

		
		
		//Call Api Url function
		$url = $this->ApiUrl($parameters,$this->options);
		//print_r($this->options); die;
		
		
		 $curl = curl_init();
		 curl_setopt($curl, CURLOPT_URL,$url);
		 curl_setopt($curl, CURLOPT_USERPWD, $this->options['username'].":".$this->options['password']);
		 curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		 curl_setopt($curl, CURLOPT_USERAGENT, $this->options['user_agent']);
		
		if($this->options['api_type'] == 'bitbucket') {
		 
		 curl_setopt($curl, CURLOPT_HEADER, false);	
		 $data = utf8_encode( http_build_query( $parameters, '', '&' ) );
		 
		}
		 else if($this->options['api_type'] == 'github') {
	
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT,$this->options['timeout']);
		
		$data = json_encode($parameters); 
		 
		}
         curl_setopt($curl, CURLOPT_RETURNTRANSFER,1); 
	     curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST"); 
		 curl_setopt($curl, CURLOPT_POSTFIELDS, $data); 
		
		 $content = curl_exec($curl);
		
		 $content = json_decode($content);
		
		if(!isset($content->title))
			{
				if(empty($content->message) && !isset($content->message))
				{
					$response = "Bad Request";
				}
				else
				{
					$response = $content->message;
				}
			}
		else
			{
				$response = $content;
			}
		  
        if ( isset( $initialOptions ) ) {
            $this->options = $initialOptions;
        }
		
        return $response;
    }
	
	/**
	* Generate Api Url For github/bitbucket
	*/
	
	protected function ApiUrl(&$parameters, $currentOptions) {
	
        // Set Api's url based on passed api type
        if($this->options['api_type'] == 'github') {
            $opt_url = $this->options['github_url'];
			
            if(isset($parameters['desc'])) {
                $parameters['body'] = $parameters['desc'];
                unset($parameters['desc']);
            }
			
        } else if($this->options['api_type'] == 'bitbucket') {
            $opt_url = $this->options['bitbucket_url'];
			
			if(isset($parameters['desc'])) {
                $parameters['content'] = $parameters['desc'];
                unset($parameters['desc']);
            }
            
        } else {
            $this->error_handler( 'Method Not Allowed', (int)$headers['http_code'] );
        }

        // Set Api's full url along with path and format
        $url = strtr( $opt_url, array(
                                        ':protocol' => $this->options['protocol'],
                                        ':path' => trim(implode( "/", array_map( 'urlencode', explode( "/", $this->options['api_path'] ) ) ), '/') . (substr($this->options['api_path'], -1) == '/' ? '/' : '')
                                       ) );
									  
        return $url;
    }
	
    /**
     * Parameter validation using error handler
     */
	 
    function validateApiCredentials() {        
        if( $this->options['api_type'] == "" || $this->options['api_path'] == "" ) {
            $this->error_handler( 'Repository url not valid' );
        } elseif($this->options['username'] == "") {
            $this->error_handler( 'Username Required' );
        } else if($this->options['password'] == "") {
            $this->error_handler( 'Password Required' );
        }
    }


    /**
     * Error Handler Exeption
     */
    public function error_handler($message = null, $code = null) {
        try {
            try {
                throw new AuthException($message, $code);
            } catch (AuthException $e) {
                // rethrow it
                throw $e;
            }
        } catch (Exception $e) {
            echo PHP_EOL . PHP_EOL ."Error: {$e->errorMessage(".$message.",".$code.")}". PHP_EOL . PHP_EOL;
            exit;
        }
    }
}
