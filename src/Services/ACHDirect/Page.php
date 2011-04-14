<?php
class Services_ACHDirect_Page
{

	/**
	 * Number of records returned. 0 = maximum.
	 * 
	 * @var int
	 */
	public $PageSize = 10;
	
	/**
	 * Page number (0 based)  
	 * 
	 * @var int 
	 */
	public $PageIndex = 10;

	/**
	 * Sort by this field.
	 * 
	 * @var string
	 */
	public $SortBy = 'Status';
	
	
	/**
	 * If true child merchant IDs will be included 
	 * 
	 * @var string
	 */
	public $SortDirection = 'ascending';
	
}