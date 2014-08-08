<?php
/* ==========================================================
 * DynECT Email Delivery RESTful API PHP Toolkit v 2.5.10
 * Revised: March 5, 2013
 *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *
 * Dyn, Inc. provides this sample merely as an example of
 * how to use the DynECT Email Delivery REST API via PHP.
 * While we will endeavor to keep the sample updated as the
 * API is enhanced, it is provided "as-is," without express
 * or implied warranty. In no event shall Dyn, Inc. be held
 * liable for any damages arising from the use of this code.
 *
 * ======================================================= */
class DynECTEmailRestClient
{
	//private variables
    private $url = 'http://emailapi.dynect.net/rest/';
	private $apikey;
	private $loc;
	private $method;
	private $requestBody;
	private $returnType;
	private $returnTypes = array('xml' => 'text/xml', 'json' => 'application/json', 'html' => 'text/html' );

	/**
	 * responseInfo
	 * @var Array An array containing the details of REST response.
	 */
	public $responseInfo;

	/**
	 * responseBody
	 * @var String A string representing the returned response formatted by returnType;
	 */
	public $responseBody;

	/**
	* __construct
	* DynECTEmailRestClient Constructor
	* Set the apikey for future calls and initializes the client
	*
	* @param String $apikey A string to set your application key. This is required for every API call.
	* @param String $returnType (optional) If set will declare the expected return type for the selected call. Avaiable options are: xml, json and html.
	* @param String $url (optional) If set will change the location to make the API request. Default is: http://emailapi.dynect.net/rest/
	*
	* @return void
	*/
	public function __construct ($apikey, $returnType = 'json', $url = '')
	{
		if(!isset($apikey) && strlen($apikey) == 40)
			throw new InvalidArgumentException('Missing apikey');

		if(!empty($url))
			$this->url = $url;

		$this->apikey = $apikey;

		if(!isset($this->returnTypes[$returnType]))
			throw new InvalidArgumentException('Invalid return type. Please use json, xml or html');

		$this->returnType = $returnType;
	}

	/**
	* setReturnType
	* Set the expected return type for the selected call. Avaiable options are: xml, json and html.
	*
	* @param String $type The return type. Avaiable options are: xml, json and html.
	*
	* @return String an expanded version of the return type.
	*/
	public function setReturnType($type='json')
	{
		if(!isset($this->returnTypes[$returntype]))
			throw new InvalidArgumentException('Invalid return type. Please use json, xml or html');

		$this->returnType = $returntype;

		return $this->returnTypes[$this->returnType];
	}

	/**
	* getReturnType
	* Get the expected return type for the selected call.
	*
	* @return String an expanded version of the return type.
	*/
	public function getReturnType()
	{
		return $this->returnTypes[$this->returnType];
	}

	/**
	* getReturnTypes
	* Get a list of return types and their expanded names
	*
	* @return Array A list of return types and their expanded names
	*/
	public function getReturnTypes()
	{
		return $this->returnTypes;
	}

	/**
	* getApikey
	* Get the provided APIKey declared in the constructor.
	*
	* @return String the apikey.
	*/
	public function getApikey()
	{
		return $this->apikey;
	}

	/**
	* CreateAccount
	* Create an administration account.
	* When creating an account if the username exists the existing account will be edited instead.
	*
	* @param String $Username The desired username for this administration account This must be an email address.
	* @param String $Password The desired password for this administration account.
	* @param String $CompanyName The company name for this administration account.
	* @param String $Phone The phone number for this administration account.
	* @param String $Address (optional) The company name for this administration account.
	* @param String $City (optional) The city for this administration account.
	* @param String $State (optional) The state for this administration account.
	* @param String $ZipCode (optional) The zip code for this administration account.
	* @param Float $TimeZone (optional) The time zone offset for this account. This must be a valid timezone between -12.00 and 13.00
	* @param String $BounceURL (optional) The bounce url for this administration account. This must begin with either http:// or https://
	* @param String $SpamURL (optional) The spam url for this administration account. This must begin with either http:// or https://
	* @param Boolean $TrackOpens (optional) Toggle open tracking.
	* @param Boolean $TrackLinks (optional) Toggle click tracking.
	* @param Boolean $TrackUnsub (optional) Toggle List-Unsubscribe tracking.
	* @param String $UnsubURL (optional) The list-unsubscribe url for this administration account. This must begin with either http:// or https://
	*
	* @return Array containing the apikey of the created account.
	*/
	public function CreateAccount($Username, $Password, $CompanyName, $Phone, $Address='', $City='', $State='', $ZipCode='', $Country='', $TimeZone='', $BounceURL='', $SpamURL='', $TrackOpens='', $TrackLinks='', $ForcePWChange='1', $TrackUnsub='', $UnsubURL='')
	{
		$data = array('username' => $Username, 'password' => $Password, 'companyname' => $CompanyName, 'phone' => $Phone);
		if($Address != '')
			$data['address'] = $Address;
		if($City != '')
			$data['city'] = $City;
		if($State != '')
			$data['state'] = $State;
		if($ZipCode != '')
			$data['zipcode'] = $ZipCode;
		if($Country != '')
			$data['country'] = $Country;
		if($TimeZone != '')
			$data['timezone'] = $TimeZone;
		if($BounceURL != '')
			$data['bounceurl'] = $BounceURL;
		if($SpamURL != '')
			$data['spamurl'] = $SpamURL;
		if($UnsubURL != '')
			$data['unsuburl'] = $UnsubURL;
		if($TrackOpens !== '')
			$data['trackopens'] = $TrackOpens ? 1 : 0;
		if($TrackLinks !== '')
			$data['tracklinks'] = $TrackLinks ? 1 : 0;
		if($TrackUnsub !== '')
			$data['trackunsub'] = $TrackUnsub ? 1 : 0;
		$data['forcepwchange'] = ($ForcePWChange == '1') ? 1 : 0;

		return $this->SetUpRequest('accounts/',$data,'post');
	}

	/**
	* EditAccount
	* Edit an administration account.
	* When editing an account if the username does not exist the account will atempt to be created instead.
	*
	* @param String $Username The username for the administration account you want to edit.
	* @param String $Password (optional) The new desired password for this administration account.
	* @param String $CompanyName (optional) The company name for this administration account.
	* @param String $Phone (optional) The phone number for this administration account.
	* @param String $Address (optional) The company name for this administration account.
	* @param String $City (optional) The city for this administration account.
	* @param String $State (optional) The state for this administration account.
	* @param String $ZipCode (optional) The zip code for this administration account.
	* @param Float $TimeZone (optional) The time zone offset for this account. This must be a valid timezone between -12.00 and 13.00
	* @param String $BounceURL (optional) The bounce url for this administration account. This must begin with either http:// or https://
	* @param String $SpamURL (optional) The spam url for this administration account. This must begin with either http:// or https://
	* @param Boolean $TrackOpens (optional) Toggle open tracking.
	* @param Boolean $TrackLinks (optional) Toggle click tracking.
	* @param Int $GenerateNewApiKey (optional) When set to '1' a new api key for this user will be generated. Default is 0 (No).
	* @param Boolean $TrackUnsub (optional) Toggle List-Unsubscribe tracking.
	* @param String $UnsubURL (optional) The list-unsubscribe url for this administration account. This must begin with either http:// or https://
	*
	* @return Array containing the apikey of the modified account.
	*/
	public function EditAccount($Username, $Password='', $CompanyName='', $Phone='', $Address='', $City='', $State='', $ZipCode='', $TimeZone='', $BounceURL='', $SpamURL='', $TrackOpens='', $TrackLinks='', $GenerateNewApiKey=0, $TrackUnsub='', $UnsubURL='')
	{
		$data = array('username' => $Username);
		if($Password != '')
			$data['password'] = $Password;
		if($CompanyName != '')
			$data['companyname'] = $CompanyName;
		if($Phone != '')
			$data['phone'] = $Phone;
		if($Address != '')
			$data['address'] = $Address;
		if($City != '')
			$data['city'] = $City;
		if($State != '')
			$data['state'] = $State;
		if($ZipCode != '')
			$data['zipcode'] = $ZipCode;
		if($TimeZone != '')
			$data['timezone'] = $TimeZone;
		if($BounceURL != '')
			$data['bounceurl'] = $BounceURL;
		if($SpamURL != '')
			$data['spamurl'] = $SpamURL;
		if($UnsubURL != '')
			$data['unsuburl'] = $UnsubURL;
		if($TrackOpens !== '')
			$data['trackopens'] = $TrackOpens ? 1 : 0;
		if($TrackLinks !== '')
			$data['tracklinks'] = $TrackLinks ? 1 : 0;
		if($TrackUnsub !== '')
			$data['trackunsub'] = $TrackUnsub ? 1 : 0;
		if($GenerateNewApiKey == 1)
			$data['generatenewapikey'] = 1;
		else
			$data['generatenewapikey'] = 0;
        
		return $this->SetUpRequest('accounts/',$data,'post');
	}

	/**
	 *
	 * @param type $username
	 * @return type 
	 */
	public function GetAccount($username='')
    {
        $data = array('username' => $username);
        return $this->SetUpRequest('accounts/',$data);
    }
	
	/**
	* GetAccounts
	* Get a list of administration accounts.
	*
	* @return Array containing administration account usernames and their apikeys.
	*/
	public function GetAccounts($start='0')
	{
		$data = array('startindex' => $start);
		return $this->SetUpRequest('accounts/',$data);
	}

	/**
	* DeleteAccount
	* Delete an account.
	*
	* @param String $Username The username of the account to delete.
	*
	* @return Array containing an success message
	*/
	public function DeleteAccount($Username)
	{
		$data = array('username' => $Username);
		return $this->SetUpRequest('accounts/delete',$data,'post');
	}

	/**
	* GetXHeaders
	* Retrieve the currently-configured custom x-header values for the current user account.
	*
	* @return Array containing the custom x-header values (or blank if not set)
	*/
	public function GetXHeaders()
	{
		return $this->SetUpRequest('accounts/xheaders');
	}

	/**
	* SetXHeaders
	* Set (or clear) up to four custom x-header field names.
	*
	* @param String $XHeader1 (optional) Custom x-header value
	* @param String $XHeader2 (optional) Custom x-header value
	* @param String $XHeader3 (optional) Custom x-header value
	* @param String $XHeader4 (optional) Custom x-header value
	*
	* @return Array containing a success or failure message
	*/
	public function SetXHeaders($XHeader1='', $XHeader2='', $XHeader3='', $XHeader4='')
	{
		$data = array('xheader1' => $XHeader1);
		$data['xheader2'] = $XHeader2;
		$data['xheader3'] = $XHeader3;
		$data['xheader4'] = $XHeader4;
		return $this->SetUpRequest('accounts/xheaders',$data,'post');
	}

	/**
	* CreateSender
	* Create an approved sender to create.
	*
	* @param String $EmailAddress The email address of the approved sender.
	*
	* @return Array containing an success message
	*/
	public function CreateSender($EmailAddress)
	{
		$data = array('emailaddress' => $EmailAddress);
		return $this->SetUpRequest('senders/',$data,'post');
	}

	/**
	* DeleteSender
	* Delete an approved sender.
	*
	* @param String $EmailAddress The email address of the approved sender to delete.
	*
	* @return Array containing an success message
	*/
	public function DeleteSender($EmailAddress)
	{
		$data = array('emailaddress' => $EmailAddress);
		return $this->SetUpRequest('senders/delete',$data,'post');
	}

	/**
	* GetSenders
	* Get a list of approved senders.
	*
	* @return Array containing a list of approved senders email addresses
	*/
	public function GetSenders($start='0')
    {
        $data = array('startindex' => $start);
        return $this->SetUpRequest('senders/', $data);
    }

	/**
	* GetSendersDetails
	* Get an approved senders details.
	*
	* @param String $EmailAddress The email address of the approved sender to check.
	*
	* @return Array containing the approved senders details
	*/
	public function GetSendersDetails($EmailAddress)
	{
		$data = array('emailaddress' => $EmailAddress);
		return $this->SetUpRequest('senders/details', $data);
	}

	/**
	* Sender Status
	* Check if the sender is in a ready state for sending
	*
	* @param String $EmailAddress The email address of the approved sender to check
	*
	* @return Array containing a message
	*/
	public function GetSenderStatus($EmailAddress) {

		$data = array('emailaddress' => $EmailAddress);
                return $this->SetUpRequest('senders/status', $data);
	}

	/**
	* SetSendersDkim
	* Set an approved senders DKIM.
	*
	* @param String $EmailAddress The email address of the approved sender to check.
	* @param String $Dkim The DKIM of the approved sender.
	*
	* @return Array containing a success message
	*/
	public function SetSendersDkim($EmailAddress, $Dkim)
	{
		$data = array('emailaddress' => $EmailAddress,'dkim' => $Dkim);
		return $this->SetUpRequest('senders/dkim', $data, 'post');
	}

	/**
	* GetRecipientStatus
	* Get a recipient's active status. This will return either active, or inactive based on bounced/complained.
	*
	* @param String $EmailAddress The email address of the recipient.
	*
	* @return Array containing a recipient status
	*/
	public function GetRecipientStatus($EmailAddress)
	{
		$data = array('emailaddress' => $EmailAddress);
		return $this->SetUpRequest('recipients/status',$data);
	}

	/**
	* GetRecipientsStatus
	* Get multiple recipient's active status. This will return either active, or inactive based on bounced/complained.
	*
	* @param Array $EmailAddressArray An array of email addresses in string format.
	*
	* @return Array a list of recipients and their status
	*/
	public function GetRecipientsStatus($EmailAddressArray)
	{
		$EmailAddressArray = implode(',',$Senders);
		return $this->GetRecipientsStatus($EmailAddressArray);
	}

	/**
	* ActivateRecipient
	* Remove an inactive state from a recipient. This will unbounce/ uncomplain the recipient.
	*
	* @param Array $EmailAddress The email address of the recipient.
	*
	* @return Array containing an success message
	*/
	public function ActivateRecipient($EmailAddress)
	{
		$data = array('emailaddress' => $EmailAddress);
		return $this->SetUpRequest('recipients/activate',$data);
	}

	/**
	* ActivateRecipients
	* Activate multiple recipient. This will unbounce/ uncomplain the recipients.
	*
	* @param Array $EmailAddressArray An array of email addresses in string format.
	*
	* @return Array containing an success message
	*/
	public function ActivateRecipients($EmailAddressArray)
	{
		$EmailAddressArray = implode(',',$Senders);
		return $this->ActivateRecipient($EmailAddressArray);
	}

	/**
	* Get an array of sent emails between the start and end date starting at the start index. This call will only return 200 at a time.
	*
	* @param String $StartTime Marks the start datetime of the search window. This must be provided in ISO 8601 format.
	* @param String $EndTime Marks the end datetime of the search window. This must be provided in ISO 8601 format.
	* @param Int $StartIndex (optional) If provided this will mark the offset of the search.
	* @param string $SenderAddress (optional) If provided this will limit your results to the sender email address provided.
    * @param array $XHeaders (optional) array of X-header values to filter by (key=xheader name, value=value to search on).
	*
	* @return Array containing the details of the sent emails
	*/
	public function GetSent($StartTime, $EndTime, $StartIndex=0, $SenderAddress='', $XHeaders=array())
	{
		$data = array('startindex' => (int)$StartIndex);
		$data['starttime'] = $StartTime;
		$data['endtime'] = $EndTime;
		if($SenderAddress != '')
			$data['sender'] = $SenderAddress;
		if(count($XHeaders) > 0)
			$data = array_merge($data, $XHeaders);
		return $this->SetUpRequest('reports/sent/',$data);
	}

	/**
	* Get the number of sent emails between the start and end date.
	*
	* @param String $StartTime Marks the start datetime of the search window. This must be provided in ISO 8601 format.
	* @param String $EndTime Marks the end datetime of the search window. This must be provided in ISO 8601 format.
	* @param string $SenderAddress (optional) If provided this will limit your results to the sender email address provided.
    * @param array $XHeaders (optional) array of X-header values to filter by (key=xheader name, value=value to search on).
	*
	* @return Array containing the number of sent emails
	*/
	public function GetSentCount($StartTime, $EndTime, $SenderAddress='', $XHeaders=array())
	{
		$data = array();
		$data['starttime'] = $StartTime;
		$data['endtime'] = $EndTime;
		if($SenderAddress != '')
			$data['sender'] = $SenderAddress;
		if(count($XHeaders) > 0)
			$data = array_merge($data, $XHeaders);
		return $this->SetUpRequest('reports/sent/count',$data);
	}

	/**
	* Get an array of delivered emails between the start and end date starting at the start index. This call will only return 200 at a time.
	*
	* @param String $StartTime Marks the start datetime of the search window. This must be provided in ISO 8601 format.
	* @param String $EndTime Marks the end datetime of the search window. This must be provided in ISO 8601 format.
	* @param Int $StartIndex (optional) If provided this will mark the offset of the search.
	* @param string $SenderAddress (optional) If provided this will limit your results to the sender email address provided.
    * @param array $XHeaders (optional) array of X-header values to filter by (key=xheader name, value=value to search on).
	*
	* @return Array containing the details of the delivered emails
	*/
	public function GetDelivered($StartTime, $EndTime, $StartIndex=0, $SenderAddress='', $XHeaders=array())
	{
		$data = array('startindex' => (int)$StartIndex);
		$data['starttime'] = $StartTime;
		$data['endtime'] = $EndTime;
		if($SenderAddress != '')
			$data['sender'] = $SenderAddress;
		if(count($XHeaders) > 0)
			$data = array_merge($data, $XHeaders);
		return $this->SetUpRequest('reports/delivered/',$data);
	}

	/**
	* Get the number of delivered emails between the start and end date.
	*
	* @param String $StartTime Marks the start datetime of the search window. This must be provided in ISO 8601 format.
	* @param String $EndTime Marks the end datetime of the search window. This must be provided in ISO 8601 format.
	* @param string $SenderAddress (optional) If provided this will limit your results to the sender email address provided.
    * @param array $XHeaders (optional) array of X-header values to filter by (key=xheader name, value=value to search on).
	*
	* @return Array containing the number of delivered emails
	*/
	public function GetDeliveredCount($StartTime, $EndTime, $SenderAddress='', $XHeaders=array())
	{
		$data = array();
		$data['starttime'] = $StartTime;
		$data['endtime'] = $EndTime;
		if($SenderAddress != '')
			$data['sender'] = $SenderAddress;
		if(count($XHeaders) > 0)
			$data = array_merge($data, $XHeaders);
		return $this->SetUpRequest('reports/delivered/count',$data);
	}

	/**
	* Get the number of bounced recipients between the start and end date.
	*
	* @param String $StartTime Marks the start datetime of the search window. This must be provided in ISO 8601 format.
	* @param String $EndTime Marks the end datetime of the search window. This must be provided in ISO 8601 format.
	* @param string $SenderAddress (optional) If provided this will limit your results to the sender email address provided.
    * @param array $XHeaders (optional) array of X-header values to filter by (key=xheader name, value=value to search on).
	*
	* @return Array containing the number of bounced recipients
	*/
	public function GetBouncesCount($StartTime, $EndTime, $SenderAddress='', $XHeaders=array())
	{
		$data = array();
		$data['starttime'] = $StartTime;
		$data['endtime'] = $EndTime;
		if($SenderAddress != '')
			$data['sender'] = $SenderAddress;
		if(count($XHeaders) > 0)
			$data = array_merge($data, $XHeaders);
		return $this->SetUpRequest('reports/bounces/count',$data);
	}

	/**
	* Get an array of bounces between the start and end date starting at the start index. This call will only return 200 at a time.
	*
	* @param String $StartTime Marks the start datetime of the search window. This must be provided in ISO 8601 format.
	* @param String $EndTime Marks the end datetime of the search window. This must be provided in ISO 8601 format.
	* @param Int $StartIndex (optional) If provided this will mark the offset of the search.
	* @param string $SenderAddress (optional) If provided this will limit your results to the sender email address provided.
	* @param string $BounceType (optional) If provided this will limit your results to the bounce type specified (hard, soft, previouslyhardbounced)
    * @param array $XHeaders (optional) array of X-header values to filter by (key=xheader name, value=value to search on).
	*
	* @return Array containing the details of the bounced emails
	*/
	public function GetBounces($StartTime, $EndTime, $StartIndex=0, $SenderAddress='', $BounceType='', $XHeaders=array())
	{
		$data = array('startindex' => (int)$StartIndex);
		$data['starttime'] = $StartTime;
		$data['endtime'] = $EndTime;
		if($SenderAddress != '')
			$data['sender'] = $SenderAddress;
		if($BounceType != '')
			$data['bouncetype'] = $BounceType;
		if(count($XHeaders) > 0)
			$data = array_merge($data, $XHeaders);
		return $this->SetUpRequest('reports/bounces/',$data);
	}

	/**
	* Get the number of spam complaints between the start and end date.
	*
	* @param String $StartTime Marks the start datetime of the search window. This must be provided in ISO 8601 format.
	* @param String $EndTime Marks the end datetime of the search window. This must be provided in ISO 8601 format.
	* @param string $SenderAddress (optional) If provided this will limit your results to the sender email address provided.
	* @param array $XHeaders (optional) array of X-header values to filter by (key=xheader name, value=value to search on).
	*
	* @return Array containing the number of spam complaints
	*/
	public function GetComplaintsCount($StartTime, $EndTime, $SenderAddress='', $XHeaders=array())
	{
		$data = array();
		$data['starttime'] = $StartTime;
		$data['endtime'] = $EndTime;
		if($SenderAddress != '')
			$data['sender'] = $SenderAddress;
		if(count($XHeaders) > 0)
			$data = array_merge($data, $XHeaders);
		return $this->SetUpRequest('reports/complaints/count',$data);
	}

	/**
	* Get an array of spam complaints between the start and end date starting at the start index. This call will only return 200 at a time.
	*
	* @param String $StartTime Marks the start datetime of the search window. This must be provided in ISO 8601 format.
	* @param String $EndTime Marks the end datetime of the search window. This must be provided in ISO 8601 format.
	* @param Int $StartIndex (optional) If provided this will mark the offset of the search.
	* @param string $SenderAddress (optional) If provided this will limit your results to the sender email address provided.
    * @param array $XHeaders (optional) array of X-header values to filter by (key=xheader name, value=value to search on).
	*
	* @return Array containing the details of the spam complaints
	*/
	public function GetComplaints($StartTime, $EndTime, $StartIndex=0, $SenderAddress='', $XHeaders=array())
	{
		$data = array('startindex' => (int)$StartIndex);
		$data['starttime'] = $StartTime;
		$data['endtime'] = $EndTime;
		if($SenderAddress != '')
			$data['sender'] = $SenderAddress;
		if(count($XHeaders) > 0)
			$data = array_merge($data, $XHeaders);
		return $this->SetUpRequest('reports/complaints/',$data);
	}

	/**
	* GetOpens
	* Get an array of email opens between the start and end date starting at the start index. This call will only return 200 at a time.
	*
	* @param String $StartTime Marks the start datetime of the search window. This must be provided in ISO 8601 format.
	* @param String $EndTime Marks the end datetime of the search window. This must be provided in ISO 8601 format.
	* @param Int $StartIndex (optional) If provided this will mark the offset of the search.
    * @param array $XHeaders (optional) array of X-header values to filter by (key=xheader name, value=value to search on).
	*
	* @return Array containing the details of the email opens
	*/
	public function GetOpens($StartTime, $EndTime, $StartIndex=0, $XHeaders=array())
	{
		$data = array('startindex' => (int)$StartIndex);
		$data['starttime'] = $StartTime;
		$data['endtime'] = $EndTime;
		if(count($XHeaders) > 0)
			$data = array_merge($data, $XHeaders);
		return $this->SetUpRequest('reports/opens/',$data);
	}

	/**
	* GetOpensCount
	* Get the number of email opens between the start and end date.
	*
	* @param String $StartTime Marks the start datetime of the search window. This must be provided in ISO 8601 format.
	* @param String $EndTime Marks the end datetime of the search window. This must be provided in ISO 8601 format.
    * @param array $XHeaders (optional) array of X-header values to filter by (key=xheader name, value=value to search on).
	*
	* @return Array containing the number of email opens
	*/
	public function GetOpensCount($StartTime, $EndTime, $XHeaders=array())
	{
		$data = array();
		$data['starttime'] = $StartTime;
		$data['endtime'] = $EndTime;
		if(count($XHeaders) > 0)
			$data = array_merge($data, $XHeaders);
		return $this->SetUpRequest('reports/opens/count',$data);
	}

	/**
	* GetClicks
	* Get an array of email link clicks between the start and end date starting at the start index. This call will only return 200 at a time.
	*
	* @param String $StartTime Marks the start datetime of the search window. This must be provided in ISO 8601 format.
	* @param String $EndTime Marks the end datetime of the search window. This must be provided in ISO 8601 format.
	* @param Int $StartIndex (optional) If provided this will mark the offset of the search.
    * @param array $XHeaders (optional) array of X-header values to filter by (key=xheader name, value=value to search on).
	*
	* @return Array containing the details of the link that was clicked
	*/
	public function GetClicks($StartTime, $EndTime, $StartIndex=0, $XHeaders=array())
	{
		$data = array('startindex' => (int)$StartIndex);
		$data['starttime'] = $StartTime;
		$data['endtime'] = $EndTime;
		if(count($XHeaders) > 0)
			$data = array_merge($data, $XHeaders);
		return $this->SetUpRequest('reports/clicks/',$data);
	}

	/**
	* GetClicksCount
	* Get the number of email link clicks between the start and end date.
	*
	* @param String $StartTime Marks the start datetime of the search window. This must be provided in ISO 8601 format.
	* @param String $EndTime Marks the end datetime of the search window. This must be provided in ISO 8601 format.
    * @param array $XHeaders (optional) array of X-header values to filter by (key=xheader name, value=value to search on).
	*
	* @return Array containing the number of links that were clicked based on the specified date range
	*/
	public function GetClicksCount($StartTime, $EndTime, $XHeaders=array())
	{
		$data = array();
		$data['starttime'] = $StartTime;
		$data['endtime'] = $EndTime;
		if(count($XHeaders) > 0)
			$data = array_merge($data, $XHeaders);
		return $this->SetUpRequest('reports/clicks/count',$data);
	}

	/**
	* GetIssues
	* Get an array of send issues between the start and end date starting at the start index. This call will only return 200 at a time.
	*
	* @param String $StartTime Marks the start datetime of the search window. This must be provided in ISO 8601 format.
	* @param String $EndTime Marks the end datetime of the search window. This must be provided in ISO 8601 format.
	* @param Int $StartIndex (optional) If provided this will mark the offset of the search.
	*
	* @return Array containing the details of the send issues
	*/
	public function GetIssues($StartTime, $EndTime, $StartIndex=0)
	{
		$data = array('startindex' => (int)$StartIndex);
		$data['starttime'] = $StartTime;
		$data['endtime'] = $EndTime;
		return $this->SetUpRequest('reports/issues/',$data);
	}

	/**
	* GetIssuesCount
	* Get the number of send issues between the start and end date.
	*
	* @param String $StartTime Marks the start datetime of the search window. This must be provided in ISO 8601 format.
	* @param String $EndTime Marks the end datetime of the search window. This must be provided in ISO 8601 format.
	*
	* @return Array containing the number of send issues
	*/
	public function GetIssuesCount($StartTime, $EndTime)
	{
		$data = array();
		$data['starttime'] = $StartTime;
		$data['endtime'] = $EndTime;
		return $this->SetUpRequest('reports/issues/count',$data);
	}

	/**
	* GetSuppressions
	* Get an array of suppressed emails between the start and end date starting at the start index. This call will only return 200 at a time.
	*
	* @param String $StartTime Marks the start datetime of the search window. This must be provided in ISO 8601 format.
	* @param String $EndTime Marks the end datetime of the search window. This must be provided in ISO 8601 format.
	* @param Int $StartIndex (optional) If provided this will mark the offset of the search.
	*
	* @return Array containing the details of the suppressed emails
	*/
	public function GetSuppressions($StartTime, $EndTime, $StartIndex=0)
	{
		$data = array('startindex' => (int)$StartIndex);
		$data['starttime'] = $StartTime;
		$data['endtime'] = $EndTime;
		return $this->SetUpRequest('suppressions/',$data);
	}

	/**
	* GetSuppressionsCount
	* Get the number of suppressed emails between the start and end date.
	*
	* @param String $StartTime Marks the start datetime of the search window. This must be provided in ISO 8601 format.
	* @param String $EndTime Marks the end datetime of the search window. This must be provided in ISO 8601 format.
	*
	* @return Array containing the number of suppressed emails
	*/
	public function GetSuppressionsCount($StartTime, $EndTime)
	{
		$data = array();
		$data['starttime'] = $StartTime;
		$data['endtime'] = $EndTime;
		return $this->SetUpRequest('suppressions/count',$data);
	}

	/**
	* SuppressionsActivate
	* Remove a recipient from the suppression list. This will not unbounce/uncomplain the recipient but you will be premitted to send to the again.
	*
	* @param Array $EmailAddress The email address of the recipient.
	*
	* @return Array containing a success message
	*/
	public function SuppressionsActivate($EmailAddress)
	{
		$data = array('emailaddress' => $EmailAddress);
		return $this->SetUpRequest('suppressions/activate',$data, 'post');
	}

	/**
	* SuppressionsAdd
	* Remove a recipient from the suppression list. This will not unbounce/uncomplain the recipient but you will be premitted to send to the again.
	*
	* @param Array $EmailAddress The email address of the recipient.
	*
	* @return Array containing a success message
	*/
	public function SuppressionsAdd($EmailAddress)
	{
		$data = array('emailaddress' => $EmailAddress);
		return $this->SetUpRequest('suppressions',$data, 'post');
	}

	/**
	* SuppressionsActivateBulk
	* Remove multiple recipients from the suppression list. This will not unbounce/uncomplain the recipients but you will be premitted to send to the again.
	*
	* @param Array $EmailAddressArray An array of email addresses in string format.
	*
	* @return Array containing a success message
	*/
	public function SuppressionsActivateBulk($EmailAddressArray)
	{
		$EmailAddressArray = implode(',',$Senders);
		return $this->SuppressionsActivate($EmailAddressArray);
	}

	/**
	 * Send
	 * Send an email to a recipient. Note: You MUST provide at least one of the bodys even though they are both marked as "optional".
	 * 
	 * @param array|string $From An associative array of your approved sender in key=>value. There can only be 1. If you provided a string it must be the from email address.
	 *        ex array(
	 *               ''Super Awesome' => 'superawesome@domain.com'
	 *           )
	 * @param array|string $To An associative array of recipients in name=>emailaddress. There can be multiple recipients. To avoid sending names dont make it an associative array.
	 *        ex array(
	 *               'John Doe' => 'email1@domain.com',
	 *               'Jane Doe' => 'email2@domain.com'
	 *               ...
	 *           )
	 * @param string $Subject The subject of the email being sent.
	 * @param string $TextBody (optional) The text/plain version of the email.
	 * @param string $HtmlBody (optional) The text/html version of the email.
	 * @param array $CC (optional) An associative array of recipients to cc in name=>emailaddress. There can be multiple recipients. To avoid sending names dont make it an associative array.
	 *        ex array(
	 *               'John Doe' => 'email1@domain.com',
	 *               'Jane Doe' => 'email2@domain.com'
	 *               ...
	 *           )
	 * @param array $XHeaders (optional) An associative array of X-Headers and data in x-headername=>value.
	 *        ex array(
	 *               'X-One' => 'Zoom',
	 *               'X-Two' => 'ZrXq!15'
	 *               ...
	 *           )
	 * @param string $ReplyTo (optional) The email address to reply to. If this is not provided it will be the from address.
	 *
	 * @return Array containing a success message
	 */
	public function Send($From = array(), $To = array(), $Subject='', $TextBody='', $HtmlBody='', $XHeaders = array(), $CC = array(), $ReplyTo = '')
	{
        //$TextBody = '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';
        
		if(is_array($To) && array_keys($To) !== range(0, count($To) - 1)) {
			$temp = array();
			foreach($To as $key=>$val) {
				$temp[] = '"' . $key . '" <' . $val . '>';
			}
			$To = $temp;
		}

		if(is_array($From) && array_keys($From) !== range(0, count($From) - 1)) {
			$temp = array();
			foreach($From as $key=>$val) {
				$temp[] = '"' . htmlspecialchars($key) . '" <' . $val . '>';
			}
			$From = $temp[0];
		}

		if(is_array($CC) && array_keys($CC) !== range(0, count($CC) - 1)) {
			$temp = array();
			foreach($CC as $key=>$val) {
				$temp[] = '"' . htmlspecialchars($key) . '" <' . $val . '>';
			}
			$CC = $temp;
		}

		if(!is_array($XHeaders))
			$XHeaders = array();

		$data = array(
			'to' => $To,
			'from' => $From,
			'cc' => $CC,
			'replyto' => $ReplyTo,
			'subject' => $Subject,
			'bodytext' => $TextBody,
			'bodyhtml' => $HtmlBody
		);
		$data = array_merge($data + $XHeaders);

		return $this->SetUpRequest('send/',$data, 'post');
	}

	/**
	* Core Functions
	*
	* THE FUNCTIONS BELOW ARE USED BY THE ABOVE CALLS AND SHOULD NOT BE INVOKED ALONE.
	*/

	private function SetUpRequest($loc = '/', $data = array(), $type='get')
	{
		$this->loc = $loc;

		$this->method = $type;

		if (!is_array($data))
			throw new InvalidArgumentException('Invalid data input for postBody.  Array expected');

		$data['apikey'] = $this->apikey;
		$data = http_build_query($data, '', '&');
		$this->requestBody = $data;

		return $this->execute();
	}

	private function execute()
	{
		$ch = curl_init();

		try
		{
			switch(strtoupper($this->method))
			{
				case 'GET':
					$this->executeGet($ch);
					break;
				case 'POST':
					$this->executePost($ch);
					break;
				default:
					throw new InvalidArgumentException('Current verb (' . $this->verb . ') is an invalid REST verb.');
			}
			return $this->responseBody;
		}
		catch(InvalidArgumentException $e)
		{
			curl_close($ch);
			throw $e;
		}
		catch(Exception $e)
		{
			curl_close($ch);
			throw $e;
		}

	}

	private function executeGet($ch)
	{
		$this->loc .= '?' . $this->requestBody;
		$this->doExecute($ch);
	}

	private function executePost($ch)
	{
		if (!is_string($this->requestBody))
		{
			$this->buildPostBody();
		}

		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->requestBody);
		curl_setopt($ch, CURLOPT_POST, 1);

		$this->doExecute($ch);
	}

	private function doExecute(&$curlHandle)
	{
		$this->setCurlOpts($curlHandle);
		$this->responseBody = curl_exec($curlHandle);
		$this->responseInfo	= curl_getinfo($curlHandle);

		curl_close($curlHandle);
	}

	private function setCurlOpts(&$curlHandle)
	{
		curl_setopt($curlHandle, CURLOPT_TIMEOUT, 120);
		curl_setopt($curlHandle, CURLOPT_URL, $this->url . $this->returnType . '/' . $this->loc);
		curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array ('Accept: ' . $this->returnTypes[$this->returnType]));
	}
}
