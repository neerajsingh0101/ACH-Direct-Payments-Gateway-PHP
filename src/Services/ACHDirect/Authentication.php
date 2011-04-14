<?php
class Services_ACHDirect_Authentication
{

	/**
	 * Set in the Virtual Terminal. 
	 * 
	 * @var string
	 */
	public $APILoginID;
	
	/**
	 * HMACMD5 (APILoginID + "|" + UTCTime, SecureTransactionKey) 
	 * 
	 * @var string
	 */
	public $TSHash;
	

	/**
	 * UTC in ticks 
	 * 
	 * @var string
	 */
	public $UTCTime;
}