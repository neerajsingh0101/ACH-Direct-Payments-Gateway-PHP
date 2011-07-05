<?php
require_once 'Zend/Soap/Client.php';
require_once 'Zend/Date.php';

require_once 'Services/ACHDirect/Authentication.php';
require_once 'Services/ACHDirect/Page.php';
require_once 'Services/ACHDirect/Range.php';
require_once 'Services/ACHDirect/Transaction.php';

class Services_ACHDirect
{
	
	/**
     * SoapClient object
     *
     * @var SoapClient
     */
    protected $_soapClient;
	

    /**
     * Merchant ID
     * 
     * @var int
     */
    protected $_merchantID;
    
    /**
     * API Login ID (from gatway settings)
     * 
     * @var string
     */
	protected $_apiLoginID;
	
    /**
     * secure transaction key (from gatway settings)
     * 
     * @var string
     */	
	protected $_secureTransactionKey;
    
	
	/**
	 * Constructor
	 * 
	 * @param int $merchantID
	 * @param string $apiLoginID
	 * @param string $secureTransactionKey
	 */
    public function __construct($merchantID, $apiLoginID, $secureTransactionKey, $environment = 'live')
    {
    	$this->_merchantID = $merchantID;
    	$this->_apiLoginID = $apiLoginID;
    	$this->_secureTransactionKey = $secureTransactionKey;
    	
    	if ($environment=='live') {
	        $this->_url = "https://ws.paymentsgateway.net/Service/v1/Transaction.wsdl";
	        $this->_location = "https://ws.paymentsgateway.net/Service/v1/Transaction.svc/Basic";
    	} else {
	        $this->_url = "https://sandbox.paymentsgateway.net/WS/Transaction.svcl";
	        $this->_location = "https://sandbox.paymentsgateway.net/WS/Transaction.svc/Basic";
    	}
    	
        $this->_client = new Zend_Soap_Client($this->_url, array("location" => $this->_location));
        $this->_client->setSoapVersion(SOAP_1_1);

	}
	
	/**
	 * @see http://www.paymentsgateway.com/developerDocumentation/Integration/webservices/merchantservice.aspx#gettransactionauthticket
	 */
	private function generateAuthToken()
	{
       	$time = time();
		$multiplied = $time * 10000000; //adjust to microseconds
		$addedtime = $multiplied + 621355968000000000; //adjust date from epoch to .net. not exact but close.
		$time = time() + 62135596800;
		$addedtime = $time . '0000000';
        $authentication = new Services_ACHDirect_Authentication();
        
        $authentication->APILoginID = $this->_apiLoginID;
        
        $authentication->TSHash = $this->hmac($this->_secureTransactionKey,$this->_apiLoginID . "|" . $addedtime);
        $authentication->UTCTime = $addedtime;
        
        return $authentication;
	}
	
	/**
	 * get transcation details
	 * 
	 * @see http://www.paymentsgateway.com/developerDocumentation/Integration/webservices/merchantservice.aspx#GetTransaction
	 * @param string $transactionID
	 */
	public function getTransaction($transactionID)
	{
		$params = array(
			"ticket" => $this->generateAuthToken(), 
			"MerchantID" =>$this->_merchantID, 
			"TransactionID" => strtolower($transactionID)
		);
		
		try {
    	    $response = $this->_client->getTransaction($params);
    	    
    	   	// cast the object
			$transaction = new Services_ACHDirect_Transaction();
	   		foreach ($response->getTransactionResult as $key => $val) {
	        	$transaction->$key = $val;
	   		}    
    	    
    	    return $transaction;
		} catch (Exception $e) {
			echo $e->getMessage() . " (" . $transactionID . ")\n";	
		} 
	}
	
	/**
	 * search transactions
	 * NOT WORKING?
	 * 
	 * @param Zend_Date $startDate
	 * @param Zend_Date $endDate
	 */
	public function searchTransactions(Zend_Date $startDate, Zend_Date $endDate)
	{
		$range = new Services_ACHDirect_Range();
		$range->MerchantIDs = $this->_merchantID;
		$range->StartDate = $startDate->toString('YYYY-MM-dd');
		$range->EndDate = $endDate->toString('YYYY-MM-dd');
		
		$page = new Services_ACHDirect_Page();
		
		$params = array(
			'ticket' => $this->generateAuthToken(), 
			'range' =>$range,
			'page' => $page,
			'SearchType' => 'Originated',
			'sFilter' => '',
			'TotalCount' => 0
		);
		
        $response = $this->_client->searchTransactions($params);

        return $response;
	}
	
	/**
	 * search transactions
	 * 
	 * @param Zend_Date $date
	 */
	public function searchTransactionsByDay(Zend_Date $date)
	{
		$params = array(
			'ticket' => $this->generateAuthToken(), 
			'MerchantID' => $this->_merchantID,
			'Day' => $date->toString('YYYY-MM-dd'),
			'PageIndex' => 0,
			'PageSize' => 200
		);
		
        $response = $this->_client->getReceivedDetail($params);
		
        // cast the objects
        $transactions = array();
        if (isset($response->getReceivedDetailResult) && isset($response->getReceivedDetailResult->Transaction)) {
        	if (is_array($response->getReceivedDetailResult->Transaction)) {
	   		     foreach ($response->getReceivedDetailResult->Transaction as $object) {
	        		$transaction = new Services_ACHDirect_Transaction();
				   	foreach ($object as $key => $val) {
				    	$transaction->$key = $val;
				   	}
				$transactions[] = $transaction;
			}
		        	
        	} else {
        		$transaction = new Services_ACHDirect_Transaction();
			   	foreach ($response->getReceivedDetailResult->Transaction as $key => $val) {
			    	$transaction->$key = $val;
			   	}
		        	
			   	$transactions[] = $transaction;
	        }
        }
                
        return $transactions;
	}
	
	/**
	 * search settle activity
	 * settles hold information about transferring the funds from the card issuer's bank to the aquiring bank, which is the one for your store.  
	 * 
	 * @see http://www.paymentsgateway.com/developerDocumentation/Integration/webservices/merchantservice.aspx#GetTransaction
	 * @param string $transactionID
	 */
	public function searchSettleActivity(Zend_Date $startDate, Zend_Date $endDate)
	{
		$range = new Services_ACHDirect_Range();
		$range->MerchantIDs = $this->_merchantID;
		$range->StartDate = $startDate->toString('YYYY-MM-dd');
		$range->EndDate = $endDate->toString('YYYY-MM-dd');
		
		$params = array(
			'ticket' => $this->generateAuthToken(), 
			'range' =>$range, 
			'PageIndex' => 0,
			'PageSize' => 100
		);
		
        $response = $this->_client->searchSettleActivity($params);

        return $response;
	}	

	/**
	 * RFC 2104 HMAC implementation for php.
	 * Creates an md5 HMAC.
	 * Eliminates the need to install mhash to compute a HMAC
	 *  
	 * @param unknown_type $key
	 * @param unknown_type $data
	 */
    protected function hmac ($key, $data)
    {
		$b = 64; // byte length for md5
		
		if (strlen($key) > $b) {
			$key = pack("H*",md5($key));
		}
		
		$key = str_pad($key, $b, chr(0x00));
		$ipad = str_pad('', $b, chr(0x36));
		$opad = str_pad('', $b, chr(0x5c));
		$k_ipad = $key ^ $ipad ;
		$k_opad = $key ^ $opad;
		
		return md5($k_opad . pack("H*",md5($k_ipad . $data)));
	}
	
}
