rajatdemo Readme
	System Requirements:
		1. Linux Server
		2. Apache 2.0+ Server
		3. PHP 5.2+
		4. Enable curl extension

	1. Need to upload all the script file on the server via FTP or any other file uploader.
		a) issue.php
		b) inc/Auth.php
		c) inc/AuthException.php
	2. Open command line and connect to server via host username & host password

	3. Need to trigger below given URL on browser to post repository issue on the github|bitbucket (Please add your github|bitbucket login credentials in place of username and passowrd)
	e.g. php issue.php username password "https://github.com/malikrajat/rajatdemo/" "Issue Title" "Issue Description"
	e.g. php issue.php username password "https://bitbucket.org/malikrajat/rajatdemo/" "Issue Title" "Issue Description"


