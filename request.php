<?php
/**
 * Add issue in specific repository based on repository url.
 * @author Arjun Dev <arjundevdev@gmail.com>
 * @version   $id: repoapi V0.01
 * @created   2014-12-01
 */

//disable error messages
ini_set('display_errors', 0);

//include Auth class file for making request
require_once('inc/Auth.php');

//object creation for addIssue class
$addIssue = new addIssue($argv);

//Send request to create issue
$addIssue->sendRequest();

class addIssue {

    /**
     * Api username
     * @var string
     */
    private $username = "";

    /**
     * Api password
     * @var string
     */
    private $password = "";

    /**
     * Api repository url
     * @var string
     */
    private $repo_url = "";

    /**
     * Repository issue title
     * @var string
     */
    private $title = "";

    /**
     * Repository issue description
     * @var string
     */
    private $description = "";

    /**
     * Instance of Auth
     * @var string
     */
    private $Auth = null;

    /**
     * Default constructor, paramters takes 
     * @param $api_url (String), $username (String), $password (String), $optValue (Array)
     */

    public function __construct(array $argv = array()) {
            
        // Assign passed arguments into variables
        list($filename, $this->username, $this->password, $this->repo_url, $this->title, $this->desc) = $argv;

        if($this->Auth == null) {
            //create object of Auth class
            if(class_exists('Auth')) {
                $this->auth = new Auth($this->repo_url, $this->username, $this->password);
            } else {
                die( 'Auth class not exists'. PHP_EOL );
            }
        }
    }

    /**
	 * Validate required parameters
     * Send a request for github|bitbucket Api's Library
     * @Show error|success message based on returned web response
     */
	 public function sendRequest() {
			if($this->inputValidation()) {
				//Send request to create repository issue on github|bitbucket
				$response = $this->auth->post(array('title' => $this->title, 'desc' => $this->desc));

				//show message based on return response from Api's
				if(isset($response->title)) {
					echo ucwords($this->auth->getOption('api_type')) .' Issue posted successfully'. PHP_EOL;
				} else {    
					echo $response."<br>".ucwords($this->auth->getOption('api_type')) .' Issue not posted successfully'. PHP_EOL;
				}
			}
		}

	/**
	* Required parameters Validation
	*/
	protected function inputValidation() {
		if($this->username == "") {
			echo PHP_EOL .'Username Required'. PHP_EOL;
			return false;
		} else if($this->password == "") {
			echo PHP_EOL .'Password Required'. PHP_EOL;
			return false;
		} else if($this->repo_url == "") {
			echo PHP_EOL .'Repository url Required'. PHP_EOL;
			return false;
		} else if($this->title == "") {
			echo PHP_EOL .'Title Required'. PHP_EOL;
			return false;
		}
			return true;
		}   
}
