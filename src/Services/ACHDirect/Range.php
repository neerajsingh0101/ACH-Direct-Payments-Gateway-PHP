<?php
class Services_ACHDirect_Range
{

	/**
	 * Merchant IDs 
	 * 
	 * @var string
	 */
	public $MerchantIDs;
	
	/**
	 * StartDate 
	 * 
	 * @var string 
	 */
	public $StartDate;

	/**
	 * EndDate
	 * 
	 * @var string
	 */
	public $EndDate;
	
	
	/**
	 * If true child merchant IDs will be included 
	 * 
	 * @var boolean
	 */
	public $ChildMID = false;
	
	
	public $EntryType;
	public $TransactionType;
	
}